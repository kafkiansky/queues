package consumer

import (
	"context"
	"errors"
	"fmt"
	"log/slog"
	"time"

	"github.com/twmb/franz-go/pkg/kgo"
)

var (
	errClientClosed = errors.New("client closed")
)

const maxRecords = 100

func Run(
	ctx context.Context,
	cl *kgo.Client,
	log *slog.Logger,
	topic string,
) error {
	msgs := make([]*kgo.Record, maxRecords)

	for {
		cursor, err := pollRecords(ctx, cl, msgs)
		if err != nil {
			if errors.Is(err, errClientClosed) {
				return nil
			}

			log.Error("poll records error",
				slog.String("err", err.Error()))
		}

		if cursor > 0 {
			processRecords(log, msgs[:cursor])

			//nolint:contextcheck
			if err := commitRecords(cl); err != nil {
				log.Error("commit records error",
					slog.String("err", err.Error()))
			}
		}

		cl.AllowRebalance()
	}
}

func pollRecords(ctx context.Context, cl *kgo.Client, msgs []*kgo.Record) (int, error) {
	fetches := cl.PollRecords(ctx, len(msgs))
	if fetches.IsClientClosed() || context.Cause(ctx) != nil {
		return 0, errClientClosed
	}

	if err := fetches.Err(); err != nil {
		return 0, fmt.Errorf("client.PollRecords: %w", err)
	}

	var cursor int
	fetches.EachPartition(func(p kgo.FetchTopicPartition) {
		p.EachRecord(func(r *kgo.Record) {
			msgs[cursor] = r
			cursor++
		})
	})

	return cursor, nil
}

func processRecords(log *slog.Logger, msgs []*kgo.Record) {
	for msgIdx := range msgs {
		log.Info("message consumed",
			slog.String("message", string(msgs[msgIdx].Value)))
	}
}

func commitRecords(cl *kgo.Client) error {
	commitCtx, commitCancel := context.WithTimeout(context.Background(), time.Second*5)
	defer commitCancel()

	if err := cl.CommitUncommittedOffsets(commitCtx); err != nil {
		return fmt.Errorf("kgo.CommitUncommittedOffsets: %w", err)
	}

	return nil
}
