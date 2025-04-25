use std::str::from_utf8;

use async_nats::subject::ToSubject;
use futures::StreamExt;
use tokio::task::JoinHandle;

pub async fn reverse_word<S: ToSubject>(
    client: async_nats::Client,
    subject: S,
) -> anyhow::Result<JoinHandle<Result<(), async_nats::Error>>> {
    let mut requests = client.subscribe(subject).await?;

    let handle = tokio::spawn({
        let client = client.clone();
        async move {
            while let Some(request) = requests.next().await {
                if let Some(reply) = request.reply {
                    let word = from_utf8(&request.payload).unwrap_or_default();
                    client
                        .publish(reply, word.chars().rev().collect::<String>().into())
                        .await?;
                }
            }
            Ok::<(), async_nats::Error>(())
        }
    });

    Ok(handle)
}
