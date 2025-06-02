# TryHackMe - tomghost
[https://tryhackme.com/room/tomghost](https://tryhackme.com/room/tomghost)

## Target Info
- IP: 10.10.99.41
- OS: Apacche tomcat9.0.30

## 1. Enumeration

```
nmap -sV -O 10.10.99.41

PORT     STATE SERVICE    VERSION
22/tcp   open  ssh        OpenSSH 7.2p2 Ubuntu 4ubuntu2.8 (Ubuntu Linux; protocol 2.0)
53/tcp   open  tcpwrapped
8009/tcp open  ajp13      Apache Jserv (Protocol v1.3)
8080/tcp open  http       Apache Tomcat 9.0.30
```

- gobuster

```
gobuster dir -u http://10.10.99.41:8080 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http

Starting gobuster in directory enumeration mode
===============================================================
/docs                 (Status: 302) [Size: 0] [--> /docs/]
/examples             (Status: 302) [Size: 0] [--> /examples/]
/manager              (Status: 302) [Size: 0] [
```
- docs/
  - ドキュメント
- manager/
  - アクセスできない
- examples
  - 何かのリンクがある

## 2. Exploitation


## 3. Privilege Escalation

## 4. Flags
