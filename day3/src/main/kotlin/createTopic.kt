package org.queues

import org.apache.kafka.clients.admin.AdminClient
import org.apache.kafka.clients.admin.AdminClientConfig
import org.apache.kafka.clients.admin.NewTopic
import java.util.Properties

fun createTopic(
    brokers: List<String>,
    topics: List<Topic>,
) {
    AdminClient
        .create(Properties().apply {
            put(AdminClientConfig.BOOTSTRAP_SERVERS_CONFIG, brokers.joinToString(","))
        })
        .use { admin ->
            admin
                .createTopics(
                    topics.map {
                        NewTopic(
                            it.name,
                            it.partitions,
                            it.replicationFactor
                        ).configs(
                            mapOf(
                                "min.insync.replicas" to it.isr.toString(),
                            ),
                        )
                    }
                )
                .all()
        }
}

data class Topic(
    val name: String,
    val partitions: Int = 3,
    val replicationFactor: Short = 3,
    val isr: Short = 2,
)
