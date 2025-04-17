up:
	docker compose up -d

down:
	docker compose down --remove-orphans

kotlin:
	docker compose exec kotlin bash

run-producer:
	docker compose exec kotlin ./gradlew producer

run-map:
	docker compose exec kotlin ./gradlew map --args="--topic-out=uppercase"

run-map-consumer:
	docker compose exec kotlin ./gradlew consumer --args="--topic=uppercase"

run-count:
	docker compose exec kotlin ./gradlew count --args="--topic-out=count"

run-count-consumer:
	docker compose exec kotlin ./gradlew consumer --args="--topic=count"
