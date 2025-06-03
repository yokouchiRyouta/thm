# TryHackMe - Active Reconnaissance
[https://tryhackme.com/room/activerecon](https://tryhackme.com/room/activerecon)

## アクティブスキャン
- 実際に目標と接触することで情報収集を行うやり方
- 必ず法的な同意が必要となる

## Webブラウザを使ったアクティブ偵察
- ブラウザの開発者ツールを使ったもの
- JSの内容、HTMLの中身を確認可能。APIのエンドポイントもわかる
- クッキーの情報とか、認証不備とかもわかる
- 見た目が通常のユーザと変わらないので気づかれにくいのがポイント
- 便利拡張機能
  - FoxyProxy プロキシの切り替えが可能
  - User-Agent Switcher and Manager UAの偽装が可能
  - Wappalyzer 対象のブラウザで使用されている情報、CMSだったり、JSライブラリだったり

## ping
- 対象のサーバが生きているかを確認。
- ICMPで送っている。返ってくることだけ確認できれば良い
- 遅延の調査にも使える

## traceroute
- パケットがどこ経由で到達しているかを確認するコマンド
- 各ルータの応答時間とかを表示。遅延確認にも一役買うよ
- linuxはtraceroute
- TTLという、何このルータを渡れるかを決める数値があって、それを使って一つずつICMPで応答を破棄することで、対象のサーバからIP付きでエラーメッセージが飛んでくる
- これをどんどん数増やして、最終的には全てのルータのIPを取得できる

## telnet
- すでに廃れた、平文で全てやりとりするとこ
- sshはそれのセキュア版
- しかし用途はあり、サービスのバナー情報(OSなどの情報含む)を取得することができる

## netcat
- TCP/UDP対応、クライアントにもなれるしサーバにもなれる。
- ファイルの転送やバックドア設置、リバースシェルとかにもなんでもござれ
- nc -vnlp 1234 これで待ち受けることもできる
- nc 1.1.1.1 1234みたいにアクセスしにいくこともできる。接続が完了したら、文字列を送れる。
- ここでコマンドを送れるようにして、それをトリガーにシェルが動いてその結果を返す、みたいなマルウェア仕込んでバックドア完了

## コマンド
- ping 
  - ping -c 10 10.10.243.49 on Linux or macOS
  - ping -n 10 10.10.243.49 on MS Windows
- traceroute	
  - traceroute 10.10.243.49 on Linux or macOS
  - tracert 10.10.243.49 on MS Windows
- telnet   
  - telnet 10.10.243.49 PORT_NUMBER
- netcat
  - nc 10.10.243.49 PORT_NUMBER
- netcat
  - nc -lvnp PORT_NUMBER