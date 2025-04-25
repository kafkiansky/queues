use clap::Parser;

use std::{env, str::from_utf8};

use app::nats;
use dotenv::dotenv;
use futures::{SinkExt, StreamExt};
use tracing_subscriber::{layer::SubscriberExt, util::SubscriberInitExt};

#[derive(Parser, Debug)]
#[command(about, long_about = None)]
struct Cli {
    #[arg(short, long)]
    mode: Mode,
}

#[derive(Clone, Debug, clap::ValueEnum)]
enum Mode {
    Count,
    Vowels,
    Consonants,
}

impl Mode {
    pub fn handle(&self, word: &str) {
        match self {
            Self::Count => {
                tracing::info!(r#"a word "{}" contains "{}" chars"#, word, word.len());
            }
            Self::Vowels => {
                tracing::info!(
                    r#"a word "{}" contains "{}" vowels"#,
                    word,
                    word.chars()
                        .filter(|c| c.is_ascii() && "aeiouAEIOU".contains(*c))
                        .count()
                );
            }
            Self::Consonants => {
                tracing::info!(
                    r#"a word "{}" contains "{}" consonants"#,
                    word,
                    word.chars()
                        .filter(|c| c.is_ascii_alphabetic() && !"aeiouAEIOU".contains(*c))
                        .count()
                );
            }
        }
    }
}

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    let cli = Cli::parse();

    dotenv()?;

    tracing_subscriber::registry()
        .with(
            tracing_subscriber::EnvFilter::try_from_default_env().unwrap_or_else(|_| {
                format!("{}=debug,axum::rejection=trace", env!("CARGO_CRATE_NAME")).into()
            }),
        )
        .with(tracing_subscriber::fmt::layer())
        .init();

    tracing::debug!(
        r#"run subscriber in mode "{}""#,
        match cli.mode {
            Mode::Count => "count",
            Mode::Vowels => "vowels",
            Mode::Consonants => "consonants",
        }
    );

    let nats = nats::connect(
        env::var("NATS_URL").unwrap_or("nats://localhost:4222".to_string()),
        env::var("NATS_USER").ok(),
        env::var("NATS_PASSWORD").ok(),
    )
    .await?;

    let mut subscription = nats.subscribe(nats::QUEUE_CHANNEL).await?;

    while let Some(message) = subscription.next().await {
        if let Ok(word) = from_utf8(&message.payload) {
            cli.mode.handle(word);
        }
    }

    Ok(())
}
