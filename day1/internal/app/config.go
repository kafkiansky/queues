package app

import (
	"context"
	"fmt"
	"log/slog"
	"os"

	"github.com/kafkiansky/queues/day1/internal/kafka"
	"github.com/twmb/franz-go/pkg/kadm"
	"github.com/twmb/franz-go/pkg/kgo"
	"gopkg.in/yaml.v3"
)

type Config struct {
	Kafka struct {
		Brokers []string `yaml:"brokers"`
		Topic   struct {
			Name              string             `yaml:"name"`
			Partitions        int32              `yaml:"partitions"`
			ReplicationFactor int16              `yaml:"replication_factor"`
			Configs           map[string]*string `yaml:"configs"`
		} `yaml:"topic"`
		Consumer struct {
			Group string `yaml:"group"`
		} `yaml:"consumer"`
	} `yaml:"kafka"`
	Log struct {
		Level slog.Level `yaml:"level"`
		Fmt   string     `yaml:"fmt"`
	} `yaml:"log"`
}

func ParseConfig(path string) (Config, error) {
	b, err := os.ReadFile(path)
	if err != nil {
		return Config{}, fmt.Errorf("os.ReadFile: %w", err)
	}

	var cfg Config
	if err := yaml.Unmarshal([]byte(os.ExpandEnv(string(b))), &cfg); err != nil {
		return Config{}, fmt.Errorf("yaml.Unmarshal: %w", err)
	}

	return cfg, nil
}

func (cfg Config) logger() (*slog.Logger, error) {
	opts := &slog.HandlerOptions{
		Level: cfg.Log.Level,
	}

	var handler slog.Handler

	switch cfg.Log.Fmt {
	case "text":
		handler = slog.NewTextHandler(os.Stdout, opts)
	case "json":
		handler = slog.NewJSONHandler(os.Stdout, opts)
	default:
		return nil, fmt.Errorf("invalid fmt value: %q", cfg.Log.Fmt)
	}

	return slog.New(handler), nil
}

func (cfg Config) kafka(ctx context.Context, log *slog.Logger) (*kgo.Client, error) {
	client, err := kgo.NewClient(
		kgo.SeedBrokers(cfg.Kafka.Brokers...),
		kgo.ConsumerGroup(cfg.Kafka.Consumer.Group),
		kgo.ConsumeTopics(cfg.Kafka.Topic.Name),
		kgo.ConsumeResetOffset(kgo.NewOffset().AtStart()),
		kgo.DisableAutoCommit(),
		kgo.FetchIsolationLevel(kgo.ReadCommitted()),
		kgo.Balancers(kgo.CooperativeStickyBalancer()),
		kgo.BlockRebalanceOnPoll(),
		kgo.MaxBufferedRecords(1_000),
	)

	if err != nil {
		return nil, fmt.Errorf("kgo.NewClient: %w", err)
	}

	log.Info("trying to create topic")

	if err := kafka.CreateTopic(
		ctx,
		kadm.NewClient(client),
		cfg.Kafka.Topic.Partitions,
		cfg.Kafka.Topic.ReplicationFactor,
		cfg.Kafka.Topic.Configs,
		cfg.Kafka.Topic.Name,
	); err != nil {
		log.Error("create topic error",
			slog.String("err", err.Error()))

		return nil, err
	}

	log.Info("topic created")

	return client, nil
}
