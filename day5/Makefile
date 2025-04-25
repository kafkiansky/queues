up: copy-envs docker-up
down: docker-down

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

rust:
	docker compose exec rust bash

copy-envs:
	cp .env.example .env

run-server:
	docker compose exec rust cargo run --bin=server

run-count-subscriber:
	docker compose exec rust cargo run --bin=subscriber -- --mode=count

run-vowels-subscriber:
	docker compose exec rust cargo run --bin=subscriber -- --mode=vowels

run-consonants-subscriber:
	docker compose exec rust cargo run --bin=subscriber -- --mode=consonants
