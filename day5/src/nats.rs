use async_nats::{
    ConnectOptions, ToServerAddrs,
    jetstream::{
        self, Context,
        stream::{self, Stream},
    },
};
use std::env;

pub static RPC_CHANNEL: &str = "words.reverse";
pub static PUBSUB_CHANNEL: &str = "words.pubsub";
pub static QUEUE_CHANNEL: &str = "words";

pub async fn connect<T: ToServerAddrs>(
    url: T,
    user: Option<String>,
    password: Option<String>,
) -> anyhow::Result<async_nats::Client> {
    let mut options = ConnectOptions::new();

    if user.is_some() && password.is_some() {
        options = options.user_and_password(user.unwrap(), password.unwrap());
    }

    Ok(options.connect(url).await?)
}

pub async fn connect_from_env() -> anyhow::Result<async_nats::Client> {
    let client = connect(
        env::var("NATS_URL").unwrap_or("nats://localhost:4222".to_string()),
        env::var("NATS_USER").ok(),
        env::var("NATS_PASSWORD").ok(),
    )
    .await?;

    Ok(client)
}

pub async fn create_stream(js: Context) -> anyhow::Result<Stream> {
    let stream = js
        .create_stream(jetstream::stream::Config {
            name: QUEUE_CHANNEL.to_owned(),
            retention: stream::RetentionPolicy::WorkQueue,
            ..Default::default()
        })
        .await?;

    Ok(stream)
}
