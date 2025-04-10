package producer

import (
	"context"
	"errors"
	"log/slog"
	"sync"
	"time"

	"github.com/kafkiansky/queues/day1/internal/random"
	"github.com/twmb/franz-go/pkg/kgo"
)

func Run(
	ctx context.Context,
	cl *kgo.Client,
	log *slog.Logger,
	topic string,
) error {
	var wg sync.WaitGroup

	rand := make(chan []byte)

	wg.Add(1)
	go func() {
		defer func() {
			close(rand)
			wg.Done()
		}()

		for {
			select {
			case <-ctx.Done():
				return
			case rand <- random.Bytes(15):
			}
		}
	}()

	wg.Add(1)
	go func() {
		defer wg.Done()

		for {
			select {
			case <-ctx.Done():
				return
			case msg := <-rand:
				cl.Produce(ctx, &kgo.Record{Topic: topic, Value: msg}, func(r *kgo.Record, err error) {
					if err != nil {
						log.Error("produce message error",
							slog.String("err", err.Error()))
					} else {
						log.Info("message produced",
							slog.String("message", string(msg)),
							slog.Int64("offset", r.Offset))
					}
				})
			}
		}
	}()

	wg.Add(1)
	go func() {
		defer wg.Done()

		<-ctx.Done()

		flushCtx, flushCancel := context.WithTimeout(context.Background(), time.Second*5)
		defer flushCancel()

		log.Info("flush all buffered records")

		if err := cl.Flush(flushCtx); err != nil && !errors.Is(err, context.Canceled) {
			log.Error("flush producer error",
				slog.String("err", err.Error()))
		}
	}()

	wg.Wait()

	return nil
}
