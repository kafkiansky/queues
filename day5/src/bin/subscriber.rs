use clap::Parser;

use std::str::from_utf8;

use app::{nats, tracer};
use dotenv::dotenv;
use futures::StreamExt;

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

    tracer::init();

    tracing::debug!(
        r#"run subscriber in mode "{}""#,
        match cli.mode {
            Mode::Count => "count",
            Mode::Vowels => "vowels",
            Mode::Consonants => "consonants",
        }
    );

    let nats = nats::connect_from_env().await?;

    let mut subscription = nats.subscribe(nats::PUBSUB_CHANNEL).await?;

    while let Some(message) = subscription.next().await {
        if let Ok(word) = from_utf8(&message.payload) {
            cli.mode.handle(word);
        }
    }

    Ok(())
}
