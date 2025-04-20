## day4 

`make up` - Поднять сервисы

`make down` - Остановить сервисы

#### Получить статистику транзакций из кликхауса
```shell
curl -X GET http://localhost:8083/transactions/daily
```

#### Отправить запрос на добавление транзакции
```shell
curl --header "Content-Type: application/json" \           
  --request POST \
  --data '{"transactionId":"1db6abec-ef5e-445b-9bd1-ed3eca2d5a64","accountId":"8ab6cadb-1aa0-4431-960e-7593ab4441e1", "amount": 100, "type": "CREDIT"}' \
  http://localhost:8083/transactions/add
```

Или запустить генератор рандомных транзакций:
```shell
bash random.sh
```

Это сгенерирует по 4 уникальные транзакции на аккаунт.