package app

import (
	"context"
	"log/slog"

	"github.com/kafkiansky/queues/day1/internal/consumer"
	"github.com/kafkiansky/queues/day1/internal/producer"
)

func RunAsProducer(
	ctx context.Context,
	cfg Config,
) error {
	logger, err := cfg.logger()
	if err != nil {
		return err
	}

	l := logger.With(
		slog.String("topic", cfg.Kafka.Topic.Name),
	)

	kfk, err := cfg.kafka(ctx, l)
	if err != nil {
		return err
	}

	defer kfk.Close()

	return producer.Run(
		ctx,
		kfk,
		l,
		cfg.Kafka.Topic.Name,
	)
}

func RunAsConsumer(
	ctx context.Context,
	cfg Config,
) error {
	logger, err := cfg.logger()
	if err != nil {
		return err
	}

	l := logger.With(
		slog.String("topic", cfg.Kafka.Topic.Name),
		slog.String("group", cfg.Kafka.Consumer.Group),
	)

	kfk, err := cfg.kafka(ctx, l)
	if err != nil {
		return err
	}

	defer kfk.Close()

	return consumer.Run(
		ctx,
		kfk,
		l,
		cfg.Kafka.Topic.Name,
	)
}
