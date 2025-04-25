use std::str::from_utf8;

use app::{nats, responder, tracer};
use async_nats::Client;
use axum::{Json, Router, extract::State, http::StatusCode, routing::post};
use dotenv::dotenv;
use serde::{Deserialize, Serialize};

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    dotenv()?;

    tracer::init();

    let nats = nats::connect_from_env().await?;

    let responder = responder::reverse_word(nats.clone(), nats::RPC_CHANNEL).await?;

    let app = Router::new()
        .route("/reverse-word", post(reverse_word))
        .route("/push-word", post(push_word))
        .with_state(nats);

    let listener = tokio::net::TcpListener::bind("0.0.0.0:8080").await?;
    tracing::debug!("listening on {}", listener.local_addr().unwrap());

    axum::serve(listener, app).await?;

    let _ = responder.await?;

    Ok(())
}

async fn reverse_word(
    nats: State<Client>,
    Json(req): Json<ReverseWord>,
) -> (StatusCode, Json<ReversedWord>) {
    tracing::info!(r#"a new word "{}" for reverse received"#, req.word.clone());

    match nats
        .request(nats::RPC_CHANNEL, req.word.clone().into())
        .await
    {
        Ok(reply) => {
            let reversed = from_utf8(&reply.payload).unwrap_or_default().to_owned();

            tracing::info!(
                r#"a word "{}" was reversed to "{}""#,
                req.word.clone(),
                reversed.clone()
            );

            return (
                StatusCode::OK,
                Json(ReversedWord {
                    word: reversed.clone(),
                }),
            );
        }
        Err(err) => {
            tracing::error!("request completed with error: {}", err);
        }
    }

    (StatusCode::INTERNAL_SERVER_ERROR, Default::default())
}

#[derive(Deserialize)]
struct ReverseWord {
    word: String,
}

#[derive(Serialize, Default)]
struct ReversedWord {
    word: String,
}

#[derive(Deserialize)]
struct PushWord {
    word: String,
}

async fn push_word(nats: State<Client>, Json(req): Json<PushWord>) -> StatusCode {
    tracing::info!(r#"a new word "{}" for queue received"#, req.word.clone());

    match nats
        .publish(nats::QUEUE_CHANNEL, req.word.clone().into())
        .await
    {
        Ok(_) => {
            tracing::info!(r#"a word "{}" successfully queued"#, req.word.clone());

            return StatusCode::OK;
        }
        Err(err) => {
            tracing::error!("request completed with error: {}", err);
        }
    }

    StatusCode::INTERNAL_SERVER_ERROR
}
