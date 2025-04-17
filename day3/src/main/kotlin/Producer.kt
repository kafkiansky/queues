package org.example

import kotlinx.coroutines.Deferred
import kotlinx.coroutines.awaitAll
import kotlinx.coroutines.coroutineScope
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.take
import kotlinx.coroutines.isActive

suspend fun main(args: Array<String>) = coroutineScope {
    val config = Cli.parseProducerCommand(args)

    val producer = Kafka.producer(
        config.brokers,
    )

    val chars = lowercaseChars()

    val channel = flow {
        while (isActive) {
            emit(randomString(chars))
        }
    }

    val futures = ArrayList<Deferred<Record<String, String>>>(config.count)

    channel
        .take(config.count)
        .collect {
            futures += produceRecord(producer, Record(
                topic = config.topic,
                value = it,
            ))
        }

    futures
        .awaitAll()
        .forEach { record ->
            println("${record.value} sent to partition ${record.partition}")
        }

    producer.close()
}
