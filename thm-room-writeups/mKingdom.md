# TryHackMe - mKingdom
[https://tryhackme.com/room/mkingdom](https://tryhackme.com/room/mkingdom)

## Target Info
- IP: 10.10.84.231
- OS: Apacche 2.4.7

## 1. Enumeration

```
nmap -sV -O

PORT   STATE SERVICE VERSION
85/tcp open  http    Apache httpd 2.4.7 ((Ubuntu))
MAC Address: 02:26:CE:3C:48:13 (Unknown)
No exact OS matches for host (If you know what OS is running on it, see https://nmap.org/submit/ ).
```

- http://10.10.84.231:85　にアクセス
- ソースに以下を発見
  - `<title>0H N0! PWN3D 4G4IN</title>`
- gobuster
  - `gobuster dir -u http://10.10.84.231:85 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http`  

```
Starting gobuster in directory enumeration mode
===============================================================
/app                  (Status: 301) [Size: 312] [--> http://10.10.84.231:85/app/]
/server-status        (Status: 403) [Size: 292]

```

- app/castle/index.php
  - contact/ 地図とか問い合わせ
  - blog/ ブログ　一つしかない
    - topic/#{id}/garbage　とか　カテゴリ？　blogの右メニューから飛べる

- gobuster2
  - `gobuster dir -u http://10.10.84.231:85/app/castle -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http`  

```
Starting gobuster in directory enumeration mode
===============================================================
/updates              (Status: 301) [Size: 327] [--> http://10.10.84.231:85/app/castle/updates/]
/packages             (Status: 301) [Size: 328] [--> http://10.10.84.231:85/app/castle/packages/]
/application          (Status: 301) [Size: 331] [--> http://10.10.84.231:85/app/castle/application/]
/concrete             (Status: 301) [Size: 328] 

```

- app/castle/
  - updates/ ファイルを実行できそうなところ。phpコードあげれればなんかできそう
  - packages/  ここも同じ
  - application/　何も映らない
  - concrete/ いろんなファイルが見放題

- gobuster2
  - `gobuster dir -u http://10.10.84.231:85/app/castle/application -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http` 

```
Starting gobuster in directory enumeration mode
===============================================================
/files                (Status: 301) [Size: 337] [--> http://10.10.84.231:85/app/castle/application/files/]
/tools                (Status: 301) [Size: 337] [--> http://10.10.84.231:85/app/castle/application/tools/]
/jobs                 (Status: 301) [Size: 336] [--> http://10.10.84.231:85/app/castle/application/jobs/]
/themes               (Status: 301) [Size: 338] [--> http://10.10.84.231:85/app/castle/application/themes/]
/mail                 (Status: 301) [Size: 336] [--> http://10.10.84.231:85/app/castle/application/mail/]
/src                  (Status: 301) [Size: 335] [--> http://10.10.84.231:85/app/castle/application/src/]
/languages            (Status: 301) [Size: 341] [--> http://10.10.84.231:85/app/castle/application/languages/]
/blocks               (Status: 301) [Size: 338] [--> http://10.10.84.231:85/app/castle/application/blocks/]
/config               (Status: 301) [Size: 338] [--> http://10.10.84.231:85/app/castle/application/config/]
/elements             (Status: 301) [Size: 340] [--> http://10.10.84.231:85/app/castle/application/elements/]
/authentication       (Status: 301) [Size: 346] [--> http://10.10.84.231:85/app/castle/application/authentication/]
/views                (Status: 301) [Size: 337] [--> http://10.10.84.231:85/app/castle/application/views/]
/attributes           (Status: 301) [Size: 342] [--> http://10.10.84.231:85/app/castle/application/attributes/]
/bootstrap            (Status: 301) [Size: 341] [--> http://10.10.84.231:85/app/castle/application/bootstrap/]
/controllers          (Status: 301) [Size: 343] [--> http://10.10.84.231:85/app/castle/application/controllers/]
Progress: 218275 / 218276 (100.00%)
``` 

### 気になったファイル
- app/castle/concrete/authenticate/concrete/db.xml
```
<schema xsi:schemaLocation="http://www.concrete5.org/doctrine-xml/0.5 http://concrete5.github.io/doctrine-xml/doctrine-xml-0.5.xsd">
<table name="authTypeConcreteCookieMap">
<field name="ID" type="integer">
<unsigned/>
<autoincrement/>
<key/>
</field>
<field name="token" type="string" size="64"/>
<field name="uID" type="integer" size="10"/>
<field name="validThrough" type="integer" size="10"/>
<index name="token">
<unique/>
<col>token</col>
</index>
<index name="uID">
<col>uID</col>
</index>
</table>
</schema>
```

- composer.json

```
	
name	"concrete5/core"
license	"MIT"
description	"concrete5 core subtree split"
type	"concrete5-core"
keywords	
0	"concrete5"
1	"CMS"
2	"concreteCMS"
homepage	"https://www.concrete5.org/"
support	
issues	"https://www.concrete5.org/developers/bugs/"
docs	"https://documentation.concrete5.org/"
irc	"irc://irc.freenode.org/concrete5"
source	"http://documentation.concrete5.org/api/"
forum	"https://www.concrete5.org/community/forums/"
slack	"https://www.concrete5.org/slack"
minimum-stability	"stable"
prefer-stable	true
config	
optimize-autoloader	true
preferred-install	
*	"dist"
autoload	
psr-4	
Concrete\Core\	"src"
bin	
0	"bin/concrete5"
require	
ext-PDO	"*"
php	">=5.5.9"
doctrine/dbal	"~2.5"
symfony/class-loader	"3.*"
symfony/http-foundation	"^3.4.17"
symfony/routing	"3.*"
symfony/http-kernel	"3.4.*"
doctrine/orm	"~2.5"
doctrine/migrations	"1.* <1.8"
league/flysystem	"1.*"
symfony/event-dispatcher	"3.*"
symfony/serializer	"3.*"
illuminate/container	"5.2.*"
illuminate/config	"5.2.*"
illuminate/filesystem	"5.2.*"
patchwork/utf8	"~1.2.3|~1.3"
wikimedia/less.php	"^1.8.0"
imagine/imagine	"^1.1.0"
natxet/cssmin	"3.*"
tedivm/jshrink	"1.*"
michelf/php-markdown	"1.*"
filp/whoops	"2.*"
pagerfanta/pagerfanta	"~1.0.1 ~1.0"
htmlawed/htmlawed	"1.*"
mobiledetect/mobiledetectlib	"2.*"
monolog/monolog	"^1.5.0"
sunra/php-simple-html-dom-parser	"1.*"
hautelook/phpass	"0.3.*"
voku/urlify	"~1.0.1"
dapphp/securimage	"3.*"
anahkiasen/html-object	"~1.4"
primal/color	"1.0.*"
concrete5/zend-queue	"0.9.*"
zendframework/zend-mail	"2.7.*"
zendframework/zend-cache	"2.7.*"
zendframework/zend-http	"2.6.*"
zendframework/zend-feed	"2.7.*"
zendframework/zend-i18n	"2.7.*"
nesbot/carbon	"~1.15"
egulias/email-validator	"1.*"
punic/punic	"^3.0.1"
tedivm/stash	"0.14.*"
lusitanian/oauth	"0.8.*"
concrete5/oauth-user-data	"~1.0"
mlocati/concrete5-translation-library	"1.5.*"
mlocati/ip-lib	"^1.3.0"
league/url	"~3.3.5"
concrete5/doctrine-xml	"^1.2.0"
symfony/yaml	"3.*"
ocramius/proxy-manager	"^1.0"
paragonie/random_compat	"^2.0"
league/flysystem-cached-adapter	"~1.0.3"
league/csv	"~8.2"
symfony/console	"^3.2|^4.0"
league/oauth2-server	"^5.1.4"
league/openid-connect-claims	"^1.1.0"
indigophp/hash-compat	"^1.1"
phpseclib/phpseclib	"^2.0"
symfony/psr-http-message-bridge	"^1.0"
guzzlehttp/guzzle	"^6.3"
league/fractal	"^0.17.0"
theorchard/monolog-cascade	"0.5.*"
extra	
branch-alias	
dev-develop	"8.x-dev"
```

### ページのソース確認
- /app/castle/index.php/
  - CMS: concrete5
  - バージョン: 8.5.2
  - login/ の存在判明
    - ログイン時にはlogin/authenticate/concreteにpostしてる
    - ccm_tockenでこれがあった。1748437033:9a9d59397c16206a6ea44ab105f2ac34
  - blog/ には何もなし
  - blog/hello-world/
    - http://10.10.84.231:85/app/castle/index.php/download_file/view/5/205 ダウンロードリンクの存在


### searchsploit

```
searchsploit -w concrete5
--------------------------------------------------------------------------------------- --------------------------------------------
 Exploit Title                                                                         |  URL
--------------------------------------------------------------------------------------- --------------------------------------------
Concrete5 8.5.4 - 'name' Stored XSS                                                    | https://www.exploit-db.com/exploits/49721
Concrete5 CME v9.1.3 - Xpath injection                                                 | https://www.exploit-db.com/exploits/51144
Concrete5 CMS 5.5.2.1 - Information Disclosure / SQL Injection / Cross-Site Scripting  | https://www.exploit-db.com/exploits/37103
Concrete5 CMS 5.6.1.2 - Multiple Vulnerabilities                                       | https://www.exploit-db.com/exploits/26077
Concrete5 CMS 5.6.2.1 - 'index.php?cID' SQL Injection                                  | https://www.exploit-db.com/exploits/31735
Concrete5 CMS 5.7.3.1 - 'Application::dispatch' Method Local File Inclusion            | https://www.exploit-db.com/exploits/40045
Concrete5 CMS 8.1.0 - 'Host' Header Injection                                          | https://www.exploit-db.com/exploits/41885
Concrete5 CMS < 5.4.2.1 - Multiple Vulnerabilities                                     | https://www.exploit-db.com/exploits/17925
Concrete5 CMS < 8.3.0 - Username / Comments Enumeration                                | https://www.exploit-db.com/exploits/44194
Concrete5 CMS FlashUploader - Arbitrary '.SWF' File Upload                             | https://www.exploit-db.com/exploits/37226
--------------------------------------------------------------------------------------- --------------------------------------------
Shellcodes: No Results

```

- ログイン画面でブルートフォース
  - admin/passwordでいけた

## 進入後
- phpファイルのアップロード確認
- リバースシェル入れて、ファイル一覧から実行
- 中に入れたら
  - python3 -c 'import pty; pty.spawn("/bin/bash")'
- SUID探査
  - find / -user root -perm -u=s -type f 2>/dev/null
- catでSUID使えるので
  
