package org.queues

import kotlinx.coroutines.coroutineScope
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.isActive
import java.time.Duration

suspend fun main(args: Array<String>) = coroutineScope {
    val config = Cli.parseConsumerCommand(args)

    createTopic(config.brokers, listOf(
        Topic(config.topic),
    ))

    val consumer = Kafka.consumer<String, String>(
        config.brokers,
        config.groupId,
    )
    consumer.subscribe(listOf(config.topic))

    val channel = flow {
        while (isActive) {
            consumer.poll(Duration.ofMillis(100)).forEach { record ->
                emit(record)
            }
        }
    }

    channel
        .collect { record ->
            println("received: ${record.value()}")
        }

    consumer.close()
}
