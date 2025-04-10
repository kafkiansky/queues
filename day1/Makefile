up:
	docker compose up -d

down:
	docker compose down --remove-orphans

run-producer:
	docker compose exec go go run cmd/app/main.go producer

run-consumer:
	docker compose exec go go run cmd/app/main.go consumer

lint:
	docker run -t --rm -v $(PWD):/app -w /app golangci/golangci-lint:v2.0.2 golangci-lint run
