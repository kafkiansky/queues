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
