package org.example

class Cli private constructor() {
    companion object ProduceCommand {
        fun parseProducerCommand(args: Array<String>): org.example.ProduceCommand = ProduceCommand(
            brokers = args.parseValue("brokers")?.split(",") ?: listOf(
                "kafka-1:9092",
                "kafka-2:9092",
                "kafka-3:9092",
                "kafka-4:9092",
            ),
            topic = args.parseValue("topic") ?: "input",
            count = args.parseValue("count")?.toIntOrNull() ?: 10_000,
        )
    }
}

data class ProduceCommand(
    val brokers: List<String>,
    val topic: String,
    val count: Int = 10_000,
)

fun Array<String>.parseValue(name: String): String? = this
    .firstOrNull { it.startsWith("--$name") }
    ?.substringAfter("=")
