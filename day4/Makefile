up: copy-env docker-up setup-topic setup-debezium

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
      --topic cdc.public.transactions \
      --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092,kafka-4:9092 \
      --partitions 3 \
      --replication-factor 3 \
      --config min.insync.replicas=2 \
      --if-not-exists

setup-debezium: wait-debezium delete-postgres-connector setup-postgres-connector

wait-debezium:
	@echo "Checking if Debezium is ready..."
	@until curl -s http://localhost:8084/connectors > /dev/null; do \
		echo "Waiting for Debezium to be ready..."; \
		sleep 1; \
	done
	@echo "Debezium is ready."

setup-postgres-connector:
	curl -i -X POST -H "Accept:application/json" -H "Content-Type:application/json" \
    http://localhost:8084/connectors/ -d @docker/debezium/config.json

delete-postgres-connector:
	curl -X DELETE http://localhost:8084/connectors/postgres-connector
