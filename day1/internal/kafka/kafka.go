package kafka

import (
	"context"
	"errors"
	"fmt"

	"github.com/twmb/franz-go/pkg/kadm"
	"github.com/twmb/franz-go/pkg/kerr"
)

type controller interface {
	CreateTopic(
		ctx context.Context,
		partitions int32,
		replicationFactor int16,
		configs map[string]*string,
		topic string,
	) (kadm.CreateTopicResponse, error)
}

func CreateTopic(
	ctx context.Context,
	ctrl controller,
	partitions int32,
	replicationFactor int16,
	configs map[string]*string,
	topic string,
) error {
	_, err := ctrl.CreateTopic(
		ctx,
		partitions,
		replicationFactor,
		configs,
		topic,
	)
	if err != nil {
		if errors.As(err, &kerr.TopicAlreadyExists) {
			return nil
		}

		return fmt.Errorf("create topic %q: %w", topic, err)
	}

	return nil
}
