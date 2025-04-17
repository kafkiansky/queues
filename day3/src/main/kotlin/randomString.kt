package org.queues

fun randomString(
    chars: List<String>,
    length: Int = 5,
): String {
    return (1..length).joinToString("") { chars.random() }
}

fun lowercaseChars() = ('a'..'z').map { it.toString() }
