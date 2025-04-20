\c day4

CREATE TABLE IF NOT EXISTS transactions (
    id UUID NOT NULL,
    account_id UUID NOT NULL,
    type TEXT NOT NULL,
    amount BIGINT NOT NULL,
    date TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW(),
    PRIMARY KEY (id)
);

ALTER TABLE transactions REPLICA IDENTITY FULL;

CREATE PUBLICATION debezium_pub FOR ALL TABLES;

SELECT pg_create_logical_replication_slot('debezium_slot', 'pgoutput');
