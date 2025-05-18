# TryHackMe - Cyborg  
🔗 https://tryhackme.com/room/cyborg  
🎯 Objective: Discover and crack Apache MD5 hash, investigate Squid proxy configuration, and proceed toward gaining access.

---

## 🖥️ Target Information

| Property     | Value                                 |
|--------------|----------------------------------------|
| IP Address   | 10.10.142.29                           |
| Web Server   | Apache 2.4.18 (Ubuntu)                |
| SSH Server   | OpenSSH 7.2p2 Ubuntu 4ubuntu2.10      |
| OS Inferred  | Ubuntu                                |

---

## 🔍 1. Enumeration

### 🔧 Nmap
```bash
nmap -sV -O 10.10.142.29
```

Discovered services:
- 22/tcp → SSH (OpenSSH 7.2p2)
- 80/tcp → HTTP (Apache 2.4.18)

---

### 🔍 Gobuster Scan
```bash
gobuster dir -u http://10.10.142.29 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http
```

Identified resources:
- `/music_archive`
- Configuration files related to `Squid Proxy` (hinted in site comments)

---

## 📁 2. Configuration Leak Discovery

Discovered reference to Squid proxy config:
```
auth_param basic program /usr/lib64/squid/basic_ncsa_auth /etc/squid/passwd
...
acl auth_users proxy_auth REQUIRED
http_access allow auth_users
```

Also discovered a hash in config:
```
music_archive:$apr1$BpZ.Q.1m$F0qqPwHSOG50URuOVQTTn.
```

This indicates:
- Apache MD5 hash format (`$apr1$`)
- User: `music_archive`
- Likely used for proxy authentication

---

## 🔓 3. Cracking the Password

Extract hash:
```bash
echo '$apr1$BpZ.Q.1m$F0qqPwHSOG50URuOVQTTn.' > hash.txt
```

Crack using John:
```bash
john hash.txt
```

Optional (to show result):
```bash
john --show hash.txt
```

Expected result (if cracked using `rockyou.txt`):
```
music_archive:please
```

---

## 💬 4. Internal Message Clues

Website or file content included chat logs:
- From Alex: Squid proxy misconfiguration
- Stated that config files are lying around
- Mentions backup `music_archive` is "safe"

This suggests:
- `music_archive` is a service/backup
- Could be login credential or endpoint
- Configuration may expose more credentials or backend access

---

## ✅ Summary

| Phase                   | Status                            |
|------------------------|------------------------------------|
| Enumeration             | ✅ nmap + gobuster                 |
| Apache Hash Discovery   | ✅ Found in squid config           |
| Password Cracking       | ✅ Apache MD5 cracked via John     |
| Squid Proxy Clues       | ✅ Leaked via internal messages    |

🔜 **Next Steps**:
- Use `music_archive` credentials to authenticate to the proxy or web portal
- Investigate if proxy allows command execution or file upload
- Explore possible lateral movement via `/music_archive` or shell access

