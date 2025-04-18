up: copy-env docker-up setup-topic

down:
	docker compose down --remove-orphans

php:
	docker compose exec php bash

docker-up:
	docker compose up -d

copy-env:
	cp .env.example .env

setup-topic:
	docker compose exec kafka-1 kafka-topics.sh \
      --create \
      --topic transactions \
      --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092,kafka-4:9092 \
      --partitions 3 \
      --replication-factor 3 \
      --config min.insync.replicas=2 \
      --if-not-exists
