FROM gradle:jdk21

WORKDIR /app

COPY gradlew .
COPY gradle/wrapper/gradle-wrapper.jar gradle/wrapper/
COPY gradle/wrapper/gradle-wrapper.properties gradle/wrapper/

RUN chmod +x gradlew

COPY build.gradle.kts settings.gradle.kts gradlew /app/
COPY src /app/src

RUN ./gradlew build
