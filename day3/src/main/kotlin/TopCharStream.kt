package org.queues

import org.apache.kafka.streams.KeyValue
import org.apache.kafka.streams.StreamsBuilder
import org.apache.kafka.streams.kstream.Materialized
import org.apache.kafka.streams.kstream.TimeWindows
import java.time.Duration
import kotlin.math.max

fun main(args: Array<String>) {
    val config = Cli.parseStreamCommand(args)

    createTopic(config.brokers, listOf(
        Topic(config.topicIn),
        Topic(config.topicOut),
    ))

    val stream = Kafka.stream(
        config.brokers,
        "top",
        StreamsBuilder()
            .apply {
                stream<String, String>(config.topicIn)
                    .flatMapValues { value ->
                        value.toCharArray()
                            .map { it.toString() }
                    }
                    .groupBy { _, char -> char }
                    .windowedBy(TimeWindows.ofSizeWithNoGrace(Duration.ofSeconds(5)))
                    .count(Materialized.`as`("char-top"))
                    .toStream()
                    .groupBy { windowed, _ -> windowed.key() }
                    .reduce { agg, new -> max(new, agg) }
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
