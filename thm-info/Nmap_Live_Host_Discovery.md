# TryHackMe - Nmap Live Host Discovery
[https://tryhackme.com/room/nmap01](https://tryhackme.com/room/nmap01)

## ネットワークセグメントとサブネット
- ネットワークセグメントは物理的につながったネットワーク
- サブネットはそのつながっている機器の中で分類される論理ネットワーク
- スイッチはセグメントを構成する機器。L2が多い
- ルーターは異なるネットワーク、セグメント間を通信する。
- FWはその間においてセキュリテぅ
- ARPはMACアドレスで同一のものがないか確認するやつ。
- ARPは同一ルータを超えられない。IPアドレスを使用した異なるセグメントへのアクセスはできない
- ARPのスキャン
  - arp-scan, nmap -sn
  - 別のサブネットとかはnmap -sP

## nmapの対象指定方法
- IP直指定、IPレンジで指定、サブネットで指定、ファイルを引数にして書いてあるIPに対して行うのもある
- IPリストの確認。攻撃の前
  - nmap -sL -n 10.10.10.0/29

## 動いているホストの発見方法
- 実際にポートスキャンする前に、どこのサーバのIPが生きているかを確認する段階。
- ARPを使用してレンジの中にいるMACアドレスを洗い出す
  - sudo nmap -sn -PR 10.10.10.0/24
- ICMPのエコーリクエストを使う場合
  - ICMPで生きているサーバを返してもらう。エコーリクエストでIPが返ってくるのを利用
  - sudo nmap -sn -PE 10.10.10.0/24
- よく使われるポートに送ってICMPのブロックを回避
  - 企業ではセキュリティの関係でICMPのリクエストをブロックしていることがある。まず発見されなければ攻撃しようがないため。
  - そのためICMPではなくTCPのポート(80)とかに対して送って、生きているかを確認する
  - sudo nmap -sn -PS22,80,443 10.10.10.0/24
- UDP
  - 上記のUDP版
  - sudo nmap -sn -PU53,123 10.10.10.0/24

## ホストの発見
- 生きていないIPにポートスキャンするのは無駄、コスト、時間、検知されるリスク。
- なので生きているホストを見つけることは大事
- -snはポートスキャンをせずにホストの発見だけを行う
- sudo nmap -sn 10.10.10.0/24をやると自動でいい感じんおやつを選んでやってくれる
- ARPは高速化つバレにくいという特徴がある。同一ネットワークでしか機能しないが有効
- 防御側は特別な設定をすることで検知可能
- arp-scan
  - nmap -sn -PRとほぼ同じ
  - 応答したMACアドレスとIPが返ってくる
- まずは静かなやつでホストを発見するのが肝要。ARP。その次いICMP
- それらを一括でやるとき、MACが帰ってきたらそれはARPなので同一サブネットない
- MACが帰ってきたらICMPなのでサブネット外である
- タイムスタンプのやつは-PP、MACアドレスの検索は-PM

## TCP
- SYNとACKが返ってくる。応答できなくてもRSTが返ってくるので生きていることがわかる。
- rootだとSYNだけでいけるが、一般ユーザはACKからのSYNまで強制
- 誰がやったかバレてしまうので、rootでこっそりやるのが基本。-PS
- -PAでACKでスキャンもできる。このときまだハンドシェイクしてないので、生きているホストは、RSTを返す。返ってくるってことは生きてる
- -PUで閉じているホストに送って、到達しなかったよ、の応答で生きているかどうかを確認。ステルスせいは抜群

## コマンド
- ARP Scan
  - sudo nmap -PR -sn MACHINE_IP/24
- ICMP Echo Scan	
  - sudo nmap -PE -sn MACHINE_IP/24
- ICMP Timestamp Scan 
  - sudo nmap -PP -sn MACHINE_IP/24
- ICMP Address Mask Scan	
  - sudo nmap -PM -sn MACHINE_IP/24
- TCP SYN Ping Scan	
  - sudo nmap -PS22,80,443 -sn MACHINE_IP/30
- TCP ACK Ping Scan	
  - sudo nmap -PA22,80,443 -sn MACHINE_IP/30
- UDP Ping Scan	
  - sudo nmap -PU53,161,162 -sn MACHINE_IP/30
