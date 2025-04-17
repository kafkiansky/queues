plugins {
    kotlin("jvm") version "2.1.10"
}

group = "org.queues"
version = "1.0-SNAPSHOT"

repositories {
    mavenCentral()
}

dependencies {
    testImplementation(kotlin("test"))
    implementation("org.apache.kafka:kafka-clients:3.9.0")
    implementation("org.apache.kafka:kafka-streams:3.9.0")
    implementation("org.jetbrains.kotlinx:kotlinx-coroutines-core:1.10.2")
}

tasks.test {
    useJUnitPlatform()
}

tasks.register<JavaExec>("producer") {
    classpath = sourceSets["main"].runtimeClasspath
    mainClass.set("org.queues.ProducerKt")
}

tasks.register<JavaExec>("consumer") {
    classpath = sourceSets["main"].runtimeClasspath
    mainClass.set("org.queues.ConsumerKt")
}

tasks.register<JavaExec>("map") {
    classpath = sourceSets["main"].runtimeClasspath
    mainClass.set("org.queues.MapStreamKt")
}

kotlin {
    jvmToolchain(21)
}