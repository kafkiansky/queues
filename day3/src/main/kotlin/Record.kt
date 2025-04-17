package org.example

data class Record<K, V>(
    val topic: String,
    val value: V,
    val key: K? = null,
    val partition: Number? = null,
)
