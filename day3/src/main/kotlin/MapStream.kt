package org.queues

import org.apache.kafka.streams.StreamsBuilder

fun main(args: Array<String>) {
    val config = Cli.parseStreamCommand(args)

    createTopic(config.brokers, listOf(
        Topic(config.topicIn),
        Topic(config.topicOut),
    ))

    val stream = Kafka.stream(
        config.brokers,
        "uppercase",
        StreamsBuilder()
            .apply {
                stream<String, String>(config.topicIn)
                    .mapValues { value -> value.uppercase() }
                    .to(config.topicOut)
            }
            .build()
    )

    stream.start()

    Runtime
        .getRuntime()
        .addShutdownHook(Thread(stream::close))
}
