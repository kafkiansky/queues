package org.queues

import org.apache.kafka.streams.KeyValue
import org.apache.kafka.streams.StreamsBuilder
import org.apache.kafka.streams.kstream.Materialized

fun main(args: Array<String>) {
    val config = Cli.parseStreamCommand(args)

    createTopic(config.brokers, listOf(
        Topic(config.topicIn),
        Topic(config.topicOut),
    ))

    val stream = Kafka.stream(
        config.brokers,
        "count",
        StreamsBuilder()
            .apply {
                stream<String, String>(config.topicIn)
                    .flatMapValues { value ->
                        value
                            .toCharArray()
                            .map { it.toString() }
                            .also { println("Count in $value") }
                    }
                    .groupBy { _, char -> char }
                    .count(Materialized.`as`("char-counts"))
                    .toStream()
                    .map { char, count -> KeyValue(char, count.toString()) }
                    .to(config.topicOut)
            }
            .build()
    )

    stream.start()

    Runtime
        .getRuntime()
        .addShutdownHook(Thread(stream::close))
}
