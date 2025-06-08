# TryHackMe - Agent Sudo
[https://tryhackme.com/room/agentsudoctf](https://tryhackme.com/room/agentsudoctf)

## Target Info
- IP: 10.10.45.55
- OS: 

## 1. Enumeration

### nmap

```
sudo nmap -sV -O 10.10.45.55
Password:
Starting Nmap 7.97 ( https://nmap.org ) at 2025-06-07 12:42 +0900
Stats: 0:00:28 elapsed; 0 hosts completed (1 up), 1 undergoing Script Scan
NSE Timing: About 0.00% done
Nmap scan report for 10.10.45.55
Host is up (0.25s latency).
Not shown: 997 closed tcp ports (reset)
PORT   STATE SERVICE VERSION
21/tcp open  ftp     vsftpd 3.0.3
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
80/tcp open  http    Apache httpd 2.4.29 ((Ubuntu))
No exact OS matches for host (If you know what OS is running on it, see https://nmap.org/submit/ ).
TCP/IP fingerprint:
```

### 80port access

```
Dear agents,

Use your own codename as user-agent to access the site.

From,
Agent R
```

- エージェントRから各エージェントへ。サイトにアクセスするときは自身のUAをコードネームとして使って
- コメントなどを確認したがこれ以上の情報はなし

### gobuster

- gobuster dir -u http://10.10.45.55 -w common.txt http

```
Starting gobuster in directory enumeration mode
===============================================================
/.hta                 (Status: 403) [Size: 276]
/.htpasswd            (Status: 403) [Size: 276]
/.htaccess            (Status: 403) [Size: 276]
/index.php            (Status: 200) [Size: 218]
/server-status        (Status: 403) [Size: 276]
```
- index.phpは初期ページと同じ
- 特に手がかりなし
- もう一つ上のワードリストで攻撃してみる

```
gobuster dir -u http://10.10.45.55 -w raft-large-words.txt http

```

### user-agentを探す
- おそらくUAに任意の値を入れるとユーザのページに行くことができる。
- Burpsuiteを使用して、リクエストを改竄し総当たり攻撃する
- Cのユーザを発見。名前はchris
- http://10.10.45.55/agent_C_attention.php

```
Attention chris,

Do you still remember our deal? Please tell agent J about the stuff ASAP. Also, change your god damn password, is weak!

From,
Agent R
```

- chrisのユーザはパスワードが弱いらしい。
- そしてJの存在も仄めかされてる
- 念の為Jのページも確認。agent_J_attentionはなし

### FTP
- hydra -l chris -P rockyou.txt -f -o found.txt ftp://10.10.45.55
- [21][ftp] host: 10.10.45.55   login: chris   password: crystal
- ふふん

```
lftp -u chris,crystal 10.10.45.55
lftp chris@10.10.45.55:~> ls
-rw-r--r--    1 0        0             217 Oct 29  2019 To_agentJ.txt
-rw-r--r--    1 0        0           33143 Oct 29  2019 cute-alien.jpg
-rw-r--r--    1 0        0           34842 Oct 29  2019 cutie.png
```

### To_agentJ

```
cat To_agentJ.txt 
Dear agent J,

All these alien like photos are fake! Agent R stored the real picture inside your directory. Your login password is somehow stored in the fake picture. It shouldn't be a problem for you.

From,
Agent C

```
- このエーリアンの写真は偽物。Jユーザーの中にパスワードとかのファイルがあるよってことで




## 2. Exploitation


## 3. Privilege Escalation

## 4. Flags
