use async_nats::{ConnectOptions, ToServerAddrs};
use std::env;

pub static RPC_CHANNEL: &str = "words.reverse";
pub static QUEUE_CHANNEL: &str = "words.queue";

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
