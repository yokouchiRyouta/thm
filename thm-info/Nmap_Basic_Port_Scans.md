# TryHackMe - Nmap Basic Port Scans
[https://tryhackme.com/room/nmap02](https://tryhackme.com/room/nmap02)

## nmapの応答
- ポートの状態は
  - openちゃんとした応答が返ってくる
  - closed RSTが返ってくる。生きてる
  - filtered ポートに到達する前にブロックされている。ICMPとか
  - unfiltered 到達できたが開いているかわからない。返ってくるので生きてる
- スキャンの方法
  - -sS SYNスキャン
  - -sU UDPスキャン
  - -sAスキャン
  - などなど

## nmap小技
- -sN NULLスキャン。応答を確認
- -sX Xmasスキャン。いろんなやつの組み合わせ
- FWの挙動を確かめたいのなら-sAとか。これは結構ブロックされる率高いのでこれでFWの有無を確認
- FWを突破したい場合は-sXとか-sNとか-sFを組み合わせて送る。どれか一つが通れば良い
- -sTはハンドシェイクするやつ。これは一般でも使える。
- -sSはSYNだけ送れるのでステルス性が高い。その代わりroot権限しか無理

## ポートの指定方法
- -Pで直指定、または20-25のようなレンジ指定
- -p-で全部。-Fで100まで。--top-ports 10で頻出トップ10でもいける
- -T0からT5まである。０によるほど遅い。IDSを回避できる
- 通常が-T3。-T5は超高速だけど信頼性、ふか増
- -T4がバランスが良くCTFでは結構使われる
- よく出る例
  - nmap -sS -p1-1024 -T4 --max-rate 100 10.10.10.10
  - SYNスキャンで、1~1024ポートで、並列に100スレッドで実行。-T4でやや強め