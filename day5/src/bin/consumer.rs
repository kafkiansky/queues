use std::{
    str::from_utf8,
    sync::atomic::{AtomicU32, Ordering},
};

use async_nats::jetstream::{self};
use futures::TryStreamExt;

use app::{nats, tracer};
use dotenv::dotenv;

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    dotenv()?;

    tracer::init();

    let count = AtomicU32::new(0);

    let nats = nats::connect_from_env().await?;

    let jetstream = jetstream::new(nats);

    let stream = nats::create_stream(jetstream).await?;

    let consumer = stream
        .create_consumer(jetstream::consumer::pull::Config {
            durable_name: Some("count-words".to_string()),
            ..Default::default()
        })
        .await?;

    let mut messages = consumer.messages().await?;

    while let Some(message) = messages.try_next().await? {
        let word = from_utf8(&message.payload).unwrap_or_default();
        let processed = count.fetch_add(1, Ordering::Relaxed);

        tracing::info!(r#"message "{}#{}" processed"#, word, processed);

        if let Err(err) = message.ack().await {
            tracing::error!(r#"ack message error "{}""#, err);
        }
    }

    Ok(())
}
