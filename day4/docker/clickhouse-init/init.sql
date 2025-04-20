CREATE DATABASE IF NOT EXISTS day4;

CREATE TABLE day4.transactions (
    id UUID,
    account_id UUID,
    type LowCardinality(String),
    amount Int64,
    date DateTime
)
    ENGINE = ReplicatedMergeTree(
        '/clickhouse/tables/{cluster}/{shard}/{table}',
        '{replica}'
    )
    ORDER BY (account_id, date)
;

CREATE TABLE day4.transactions_daily (
    account_id UUID,
    date Date,
    count AggregateFunction(count, UInt64),
    amount AggregateFunction(sum, Int64)
)
    ENGINE = ReplicatedAggregatingMergeTree(
        '/clickhouse/tables/{cluster}/{shard}/{table}',
        '{replica}'
    )
    ORDER BY (account_id, date)
;

CREATE TABLE day4.transactions_from_kafka (
    payload String
)
    ENGINE = Kafka SETTINGS kafka_broker_list = 'kafka-1:9092,kafka-2:9092,kafka-3:9092,kafka-4:9092',
                            kafka_topic_list = 'cdc.public.transactions',
                            kafka_group_name = 'clickhouse_transactions_consumer',
                            kafka_format = 'JSONEachRow',
                            kafka_row_delimiter = '\n',
                            kafka_num_consumers = 3
;

CREATE MATERIALIZED VIEW day4.transactions_view TO day4.transactions AS
SELECT
    JSONExtractString(payload, 'after', 'id') as id,
    JSONExtractString(payload, 'after', 'account_id') as account_id,
    JSONExtractString(payload, 'after', 'type') as type,
    JSONExtractInt(payload, 'after', 'amount') as amount,
    fromUnixTimestamp64Micro(JSONExtractUInt(payload, 'after', 'date')) as date
FROM
    day4.transactions_from_kafka
;

CREATE MATERIALIZED VIEW day4.transactions_daily_view TO day4.transactions_daily AS
SELECT
    account_id,
    toDate(date) AS date,
    countState() AS count,
    sumState(amount) AS amount
FROM
    day4.transactions
GROUP BY
    account_id, date
;
