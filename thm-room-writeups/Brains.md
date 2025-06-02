# TryHackMe - Brains
[https://tryhackme.com/room/brains](https://tryhackme.com/room/brains)

## Target Info
- IP: 10.10.103.234
- OS: 

## 1. Enumeration
- nmap -sV -O 10.10.103.234

```
PORT      STATE SERVICE  VERSION
22/tcp    open  ssh      OpenSSH 8.2p1 Ubuntu 4ubuntu0.11 (Ubuntu Linux; protocol 2.0)
80/tcp    open  http     Apache httpd 2.4.41 ((Ubuntu))
50000/tcp open  ibm-db2?
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
```

- hydra -l root -P /usr/share/wordlists/rockyou.txt -f -o found.txt ssh://10.10.103.234
- パスワード認証をサポートしてない

- nmap -sV -p 50000 --script=banner,vuln,default 10.10.103.234
  - 50000ポートはwebサービスである可能性高い
- admin/admin admin/passwordでログインできず
  - 以下を発見
```
<style id="1971be14413" type="text/css">
    @import "/res/459560978433157637.css?v=1748519309691";
</style>
```   

```
<script type="text/javascript">
      $j(document).ready(function($) {
        var loginForm = $('.loginForm');

        $("#username").focus();

        loginForm.attr('action', '/loginSubmit.html');
        loginForm.submit(function() {
          return BS.LoginForm.submitLogin();
        });

        if (BS.Cookie.get("__test") != "1") {
          $("#noCookiesEnabledMessage").show();
        }

        if (BS.Cookie.get("RecentLogin") !== null) {
          const errBlock = document.querySelector('#errorMessage');
          errBlock.textContent = "Clear the browser cookies or restart the browser to log in.";
          errBlock.style.display = "block";
          BS.Cookie.remove("RecentLogin");
        }

        if ($('#fading').length > 0) {
          BS.Highlight('fading');
        }

        const username = {
          name: 'Username',
          input: document.getElementById('username'),
          error: document.getElementById('username-error'),
          maxLength: 60
        };
        const password = {
          name: 'Password',
          input: document.getElementById('password'),
          error: document.getElementById('password-error'),
          maxLength: 128
        };
        const submit = document.querySelector('.loginButton');
        function validateInput({name, input, error, maxLength}) {
          if (input.value.length > maxLength) {
            input.classList.add('errorField');
            error.textContent = name + ' should be no longer than ' + maxLength + ' characters';
            return false
          } else {
            input.classList.remove('errorField');
            error.textContent = '';
            return true;
          }
        }
        function handleChange() {
          const usernameValid = validateInput(username);
          const passwordValid = validateInput(password);
          submit.disabled = !usernameValid || !passwordValid;
        }
        username.input.addEventListener('input', handleChange);
        password.input.addEventListener('input', handleChange);
      });
```

- gobuster 
- gobuster dir -u http://10.10.103.234:50000 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt http

```
/img                  (Status: 302) [Size: 0] [--> /img/]
/profile              (Status: 302) [Size: 0] [--> /profile/]
/admin                (Status: 302) [Size: 0] [--> /admin/]
/overview             (Status: 401) [Size: 66]
/tests                (Status: 302) [Size: 0] [--> /tests/]
/plugins              (Status: 302) [Size: 0] [--> /plugins/]
/css                  (Status: 302) [Size: 0] [--> /css/]
/project              (Status: 401) [Size: 66]
/test                 (Status: 401) [Size: 66]
/license              (Status: 302) [Size: 0] [--> /license/]
/status               (Status: 302) [Size: 0] [--> /status/]
/update               (Status: 403) [Size: 13]
/problems             (Status: 302) [Size: 0] [--> /problems/]
/js                   (Status: 302) [Size: 0] [--> /js/]
/learn                (Status: 401) [Size: 66]
/changes              (Status: 401) [Size: 66]
/coverage             (Status: 302) [Size: 0] [--> /coverage/]
/change               (Status: 401) [Size: 66]
/build                (Status: 401) [Size: 66]
/maintenance          (Status: 302) [Size: 0] [--> /maintenance/]
/agent                (Status: 401) [Size: 66]
/investigations       (Status: 401) [Size: 66]
/agents               (Status: 401) [Size: 66]
/builds               (Status: 401) [Size: 66]
/favorite             (Status: 401) [Size: 66]
/nodes                (Status: 302) [Size: 0] [--> /nodes/]
/parameters           (Status: 302) [Size: 0] [--> /parameters/]
/artifacts            (Status: 302) [Size: 0] [--> /artifacts/]
/http%3A%2F%2Fwww     (Status: 400) [Size: 435]
/queue                (Status: 401) [Size: 66]
/clouds               (Status: 302) [Size: 0] [--> /clouds/]
/http%3A%2F%2Fyoutube (Status: 400) [Size: 435]
/http%3A%2F%2Fblogs   (Status: 400) [Size: 435]
/http%3A%2F%2Fblog    (Status: 400) [Size: 435]
/pipelines            (Status: 401) [Size: 66]
/**http%3A%2F%2Fwww   (Status: 400) [Size: 435]

```


- searchsploit

```
# searchsploit teamcity
-------------------------------------------- ---------------------------------
 Exploit Title                              |  Path
-------------------------------------------- ---------------------------------
JetBrains TeamCity 2018.2.4 - Remote Code E | java/remote/47891.txt
TeamCity < 9.0.2 - Disabled Registration By | multiple/remote/46514.js
TeamCity Agent - XML-RPC Command Execution  | multiple/remote/45917.rb
TeamCity Agent XML-RPC 10.0 - Remote Code E | php/webapps/48201.py
-------------------------------------------- ---------------------------------
Shellcodes: No Results

```



## 2. Exploitation


## 3. Privilege Escalation

## 4. Flags
