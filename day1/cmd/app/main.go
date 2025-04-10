package main

import (
	"context"
	"errors"
	"flag"
	"fmt"
	"os"
	"os/signal"
	"slices"
	"syscall"

	"github.com/kafkiansky/queues/day1/internal/app"
)

var (
	configPath = flag.String("config", "config.local.yaml", "Path to config file.")
)

func main() {
	flag.Parse()

	if err := run(); err != nil && !errors.Is(err, context.Canceled) {
		panic(err)
	}
}

func run() error {
	if len(os.Args) < 2 || !slices.Contains([]string{"producer", "consumer"}, os.Args[1]) {
		return fmt.Errorf("invalid running mode")
	}

	cfg, err := app.ParseConfig(*configPath)
	if err != nil {
		return fmt.Errorf("app.ParseConfig %q: %w", *configPath, err)
	}

	ctx, cancel := signal.NotifyContext(context.Background(), os.Interrupt, syscall.SIGTERM)
	defer cancel()

	switch os.Args[1] {
	case "producer":
		return app.RunAsProducer(ctx, cfg)
	default:
		return app.RunAsConsumer(ctx, cfg)
	}
}
