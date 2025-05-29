# TryHackMe - Brains
[https://tryhackme.com/room/anthem](https://tryhackme.com/room/anthem)

## Target Info
- IP: 10.10.94.131
- OS: 

## 1. Enumeration
- nmap scan: nmap -sV -O 10.10.94.131
- Open ports: 80, 3389

```
PORT     STATE SERVICE       VERSION
80/tcp   open  http          Microsoft HTTPAPI httpd 2.0 (SSDP/UPnP)
3389/tcp open  ms-wbt-server Microsoft Terminal Services
```

- Details
  - Anthem.com. Site of Blogite
- gobuster

```
gobuster dir -u http://10.10.94.131 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http
```

- dirsearch -u 

```
[02:49:10] 403 -  312B  - /%2e%2e//google.com
[02:49:11] 403 -  312B  - /.%2e/%2e%2e/%2e%2e/%2e%2e/etc/passwd
[02:49:45] 301 -  149B  - /.vscode  ->  http://anthem.com/.vscode/
[02:50:05] 403 -  312B  - /\..\..\..\..\..\..\..\..\..\etc\passwd
[02:53:41] 301 -  118B  - /archive  ->  /
[02:53:42] 301 -  118B  - /archive.aspx  ->  /
[02:54:11] 500 -   45B  - /base/static/c
[02:54:23] 200 -    5KB - /blog/
[02:54:23] 200 -    5KB - /blog
[02:54:40] 200 -    3KB - /categories
[02:54:42] 403 -  312B  - /cgi-bin/.%2e/%2e%2e/%2e%2e/%2e%2e/etc/passwd
[02:57:33] 302 -  126B  - /INSTALL  ->  /umbraco/
[02:57:33] 302 -  126B  - /Install  ->  /umbraco/
[02:57:33] 302 -  126B  - /install  ->  /umbraco/
[02:57:34] 302 -  126B  - /install/  ->  /umbraco/
[03:01:17] 200 -  192B  - /robots.txt
[03:01:21] 200 -    2KB - /rss
[03:01:28] 200 -    3KB - /Search
[03:01:28] 200 -    3KB - /search
[03:01:53] 200 -    1KB - /sitemap
[03:02:43] 200 -    3KB - /tags
[03:03:10] 403 -    2KB - /Trace.axd
```

- Using Gobuster, the following paths were discovered:
  - /assets
  - /fuel
  - /offline
- /
  - Flag Discovery
  - THM{G!T_G00D}
- robots.txt
  - UmbracoIsTheBest!
- /authors
  - Flag Discovery
  - THM{L0L_WH0_D15}
- /sitemap
  - XML file.
  - Probably a file containing logs.

```
<urlset xsi:schemalocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<url>
<loc>http://10.10.94.131/blog/</loc>
<lastmod>2020-04-05T20:37:17+00:00</lastmod>
</url>
<url>
<loc>http://10.10.94.131/archive/</loc>
<lastmod>2020-04-05T19:11:38+00:00</lastmod>
</url>
<url>
<loc>http://10.10.94.131/archive/we-are-hiring/</loc>
<lastmod>2020-04-05T21:01:02+00:00</lastmod>
</url>
<url>
<loc>
http://10.10.94.131/archive/a-cheers-to-our-it-department/
</loc>
<lastmod>2020-04-05T21:02:29+00:00</lastmod>
</url>
<url>
<loc>http://10.10.94.131/authors/</loc>
<lastmod>2020-04-05T23:13:00+00:00</lastmod>
</url>
<url>
<loc>http://10.10.94.131/authors/jane-doe/</loc>
<lastmod>2020-04-05T21:11:16+00:00</lastmod>
</url>
</urlset>
```
- Email
  - JD@anthem.com
- 3389 connection

```
nmap -p 3389 --script rdp-enum-encryption,rdp-ntlm-info -sV 10.10.94.131

Starting Nmap 7.80 ( https://nmap.org ) at 2025-05-15 14:27 BST
Nmap scan report for 10.10.94.131
Host is up (0.00094s latency).

PORT     STATE SERVICE       VERSION
3389/tcp open  ms-wbt-server Microsoft Terminal Services
| rdp-enum-encryption: 
|   Security layer
|     CredSSP (NLA): SUCCESS
|     CredSSP with Early User Auth: SUCCESS
|     RDSTLS: SUCCESS
|     SSL: SUCCESS
|_  RDP Protocol Version:  RDP 10.6 server
| rdp-ntlm-info: 
|   Target_Name: WIN-LU09299160F
|   NetBIOS_Domain_Name: WIN-LU09299160F
|   NetBIOS_Computer_Name: WIN-LU09299160F
|   DNS_Domain_Name: WIN-LU09299160F
|   DNS_Computer_Name: WIN-LU09299160F
|   Product_Version: 10.0.17763
|_  System_Time: 2025-05-15T13:27:09+00:00
MAC Address: 02:54:1F:FF:87:BF (Unknown)
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .

```

- Umbroco CMS

```
Umbraco.Sys.ServerVariables = {
  "umbracoUrls": {
    "externalLoginsUrl": "/umbraco/ExternalLogin",
    "serverVarsJs": "/umbraco/ServerVariables",
    "authenticationApiBaseUrl": "/umbraco/backoffice/UmbracoApi/Authentication/",
    "currentUserApiBaseUrl": "/umbraco/backoffice/UmbracoApi/CurrentUser/"
  },
  ...
}
```

- searchexploit
  - searchsploit umbraco
  - searchsploit -m 19671
  - cat 19671.py




## 2. Exploitation


## 3. Privilege Escalation

## 4. Flags
