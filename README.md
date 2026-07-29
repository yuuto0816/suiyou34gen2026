# 前期最終課題 画像投稿掲示板

AWSのEC2（Amazon Linux）に、画像投稿ができる掲示板を構築する手順です。

## 1. 必要なもののインストールと準備

まずはDockerとGitを入れて起動します。

```bash
sudo yum update -y
sudo yum install -y git docker
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker ec2-user

```

※ここで一回 `exit` と打ってログアウトし、もう一度SSHで入り直してください。（dockerコマンドを使えるようにするためです）

次に、Docker Composeをインストールします。

```bash
mkdir -p ~/.docker/cli-plugins/
curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose
chmod +x ~/.docker/cli-plugins/docker-compose

```

## 2. ファイルのダウンロードと起動

GitHubからソースコードを持ってきて、コンテナを起動します。

```bash
git clone https://github.com/yuuto0816/suiyou34gen2026.git
cd suiyou34gen2026
docker compose up -d

```

## 3. データベースのテーブル作成

MySQLの中に、アクセスログ用と掲示板用のテーブルを作ります。

```bash
docker compose exec -T mysql mysql -u root example_db < create_access_logs.sql
docker compose exec -T mysql mysql -u root example_db < create_bbs_entries.sql

```

## 4. 動作確認

ブラウザを開いて、以下のURLにアクセスします。

`http://<EC2のパブリックIPアドレス>/bbsimagetest.php`

掲示板の画面が表示されて、画像付きで投稿ができれば完成です！
