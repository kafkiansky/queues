### day5 

`make up` - Поднять сервисы

`make down` - Остановить сервисы

#### Отправить запрос на переворачивание слова
```shell
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"word":"nats"}' \
  http://localhost:8080/reverse-word
```

Запрос обработается с помощью nats rpc.
