package org.queues

const val defaultBrokers = "kafka-1:9092,kafka-2:9092,kafka-3:9092,kafka-4:9092"
const val defaultProduceTopic = "input"
const val defaultConsumeTopic = "output"

class Cli private constructor() {
    companion object Parser {
        fun parseProducerCommand(args: Array<String>): ProduceCommand = ProduceCommand(
            brokers = args.parseValue("brokers")?.split(",") ?: defaultBrokers.split(","),
            topic = args.parseValue("topic") ?: defaultProduceTopic,
            count = args.parseValue("count")?.toIntOrNull() ?: 10_000,
        )

        fun parseConsumerCommand(args: Array<String>): ConsumeCommand = ConsumeCommand(
            brokers = args.parseValue("brokers")?.split(",") ?: defaultBrokers.split(","),
            topic = args.parseValue("topic") ?: defaultConsumeTopic,
            groupId = args.parseValue("group") ?: "testing",
        )
    }
}

data class ProduceCommand(
    val brokers: List<String>,
    val topic: String,
    val count: Int = 10_000,
)

data class ConsumeCommand(
    val brokers: List<String>,
    val topic: String,
    val groupId: String,
)

fun Array<String>.parseValue(name: String): String? = this
    .firstOrNull { it.startsWith("--$name") }
    ?.substringAfter("=")
