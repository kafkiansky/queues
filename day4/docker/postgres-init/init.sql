\c day4

create publication sequin_pub for all tables;

select
    pg_create_logical_replication_slot('sequin_slot', 'pgoutput');
