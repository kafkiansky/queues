up:
	docker compose up -d

down:
	docker compose down --remove-orphans

run-producer:
	docker compose exec go go run cmd/app/main.go producer

run-consumer:
	docker compose exec go go run cmd/app/main.go consumer
