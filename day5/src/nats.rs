use async_nats::{ConnectOptions, ToServerAddrs};

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
