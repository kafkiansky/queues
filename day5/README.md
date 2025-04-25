### day5 

`make up` - Поднять сервисы

`make down` - Остановить сервисы

#### nats rpc

Запустить http сервер:
```shell
make run-server
```

Отправить запрос на переворачивание слова:
```shell
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"word":"nats"}' \
  http://localhost:8080/reverse-word
```

#### nats pub-sub

Запустить http сервер:
```shell
make run-server
```

Запустить консьюмера, который будет подсчитывать количество символов в слове:
```shell
make run-count-subscriber
```

Запустить консьюмера, который будет подсчитывать количество гласных в слове:
```shell
make run-vowels-subscriber
```

Запустить консьюмера, который будет подсчитывать количество согласных в слове:
```shell
make run-consonants-subscriber
```

Опубликовать слово в очередь:
```shell
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"word":"nats"}' \
  http://localhost:8080/push-word
```

#### nats jetstream queue

Запустить http сервер:
```shell
make run-server
```

Запустить консьюмера:
```shell
make run-consumer
```

Опубликовать слово в очередь:
```shell
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"word":"nats"}' \
  http://localhost:8080/queue-word
```
