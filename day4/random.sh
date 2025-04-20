#!/bin/bash

for i in {1..30}; do
  accountId=$(uuidgen)

  for i in {1..4}; do
    transactionId=$(uuidgen)

    response=$(curl -s -o /dev/null -w "%{http_code}" \
        -X POST "http://localhost:8083/transactions/add" \
        -H "Content-Type: application/json" \
        -d '{"transactionId":"'"$transactionId"'","accountId":"'"$accountId"'","amount":100,"type":"CREDIT"}')

      if [ "$response" -eq 200 ]; then
        echo "Успешно: $transactionId"
      else
        echo "Ошибка для $transactionId: код $response"
      fi
  done
done
