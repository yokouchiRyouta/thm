# TryHackMe - Passive Reconnaissance
[https://tryhackme.com/room/passiverecon](https://tryhackme.com/room/passiverecon)

## 偵察
- 情報を収集する行為。キルチェーンの１歩目
- 攻撃の足がかりになる情報を収集する。ここが一番重要
- パッシブスキャンは公開情報から行う。SNSやHTMLコードの確認など
- アクティブスキャンは能動的にエクスプロイトを試して穴を探す。ソーシャルエンジニアリングなど

## whois
- DNSの情報を取得。43ポートで実行される
- どのサーバを経由したかとか、いつ登録された、その会社の情報とかを取得可能

## サブドメインの重要性
- サブドメインには古いサービスが入っていることが多く、攻撃対象にするのが良い
- 探す手段として、site:aaa.comみたいな検索を行う
- もしくは総当たり攻撃で探す
- もしくはDNSDumperとかもあるのでそれを使う
- https://dnsdumpster.com/

## Shodan.io
- 対象のネットワークに接続されている製品を見つける
- IPを指定して、そこから探す
- 地理的情報だったり、サーバのホスティング業者、サーバの種類とかもわかってまう。
- 公開済みの情報を取得するだけなので、パッシブスキャンに該当
- ペンテスターも使う
- https://www.shodan.io/

## コマンド
- Lookup WHOIS record	
  - whois tryhackme.com
- Lookup DNS A records
  - nslookup -type=A tryhackme.com
- Lookup DNS MX records at DNS server	
  - nslookup -type=MX tryhackme.com 1.1.1.1
- Lookup DNS TXT records	
  - nslookup -type=TXT tryhackme.com
- Lookup DNS A records	
  - dig tryhackme.com A
- Lookup DNS MX records at DNS server	
  - dig @1.1.1.1 tryhackme.com MX
- Lookup DNS TXT records	
  - dig tryhackme.com TXT