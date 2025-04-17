package org.example

import kotlinx.coroutines.CompletableDeferred
import kotlinx.coroutines.Deferred
import org.apache.kafka.clients.producer.KafkaProducer
import org.apache.kafka.clients.producer.ProducerRecord

fun<K, V> produceRecord(
    producer: KafkaProducer<K, V>,
    record: Record<K, V>,
): Deferred<Record<K, V>> {
    val deferred = CompletableDeferred<Record<K, V>>()

    producer.send(ProducerRecord(record.topic, record.key, record.value)) { metadata, exception ->
        if (exception != null) {
            deferred.completeExceptionally(exception)
        } else {
            deferred.complete(Record(
                topic = record.topic,
                value = record.value,
                key = record.key,
                partition = metadata.partition(),
            ))
        }
    }

    return deferred
}
