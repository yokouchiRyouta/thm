# TryHackMe - Linux Fundamentals Part 3
[https://tryhackme.com/room/linuxfundamentalspart3]()

## SSH
- セキュアシェル
- 暗号化することで安全にリモートでコマンドを実行できる

## ファイルの送受信
- python3 -m http.serverでサーバ立てて、wget http://hogehoge:8000/aaa.txt などで送り合う

## psコマンド
- プロセス一覧。ps auxでシステムが自動で読んでるプロセスも見れる
- 起動時に実行されるPUID=1はsystemd
- ここに入れ子でいろんなサーバの立ち上げなどを入れることもできる
- systemdが止まると全て終わる

## cron
- オンラインの「Crontab Generator」を使えば自動で生成もできる

## パッケージ
- パッケージはコマンドとそれに必要なコマンドが入った箱
- aptコマンドでubuntuにパッケージごとダウンロードできる
- dpkgは特定のファイルのみダウンロードしてくるやつ
- ローカルのaptにリポジトリの情報がないとダウンロードできないので、まずGPGキーの確認をする
- 確認できたら、どこからダウンロードするかを記載したリポジトリを追加
- ローカルのapt更新して信頼されていることを読み込む
- 普通にダウンロード

```
wget -qO - https://download.sublimetext.com/sublimehq-pub.gpg | sudo apt-key add -
sudo nano /etc/apt/sources.list.d/sublime-text.list
sudo apt update
sudo apt install sublime-text
```

## var
- 全てのログが貼っているところ/var/log
- アクセスログとエラーログは監視、防御に役立つ。逆にここから痕跡を消す必要がある