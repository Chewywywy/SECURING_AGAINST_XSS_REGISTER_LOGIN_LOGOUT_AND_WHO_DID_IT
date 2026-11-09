# Secure Login System: XSS Protection

A PHP + MySQL (PDO) register, login, logout, and "Who Did It" activity log system, built to demonstrate how Cross-Site Scripting (XSS) attacks work and how to defend against them.

## Features

- User registration and login with hashed passwords (`password_hash` / `password_verify`)
- Logout with full session destruction
- "Who Did It" activity log that records register, login, and logout actions
- XSS protection on all output
- Strong password policy
- CSRF token on every form
- SQL injection protection through PDO prepared statements

## Security Measures

| # | Measure | File | What it does |
|---|---------|------|--------------|
| 1 | HttpOnly session cookie | `security.php` | Stops JavaScript from reading the session cookie |
| 2 | Content Security Policy header | `security.php` | Tells the browser to block inline and third-party scripts |
| 3 | Output escaping with `htmlspecialchars()` | `security.php` (`e()`) | Displays injected scripts as plain text instead of running them |
| 4 | Password validation | `security.php` | Requires 8+ characters with uppercase, lowercase, and a number |
| 5 | Input sanitizing with `strip_tags()` | `security.php` (`cleanInput()`) | Removes HTML tags from usernames before saving |

## Password Rules

- Minimum of 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number

## Project Structure

```
xss_secure/
├── index.php       # Redirects to login
├── db.php          # PDO connection, auto-creates database and tables
├── security.php    # Session settings, headers, escaping, validation, CSRF, logging
├── register.php    # Registration page
├── login.php       # Login page
├── logout.php      # Logout and session destroy
├── dashboard.php   # Welcome page and Who Did It log
└── style.css       # Styling
```

## Requirements

- XAMPP (Apache + MySQL) or any PHP 7.4+ and MySQL setup
- A web browser

## Installation

1. Clone or download this repository.
2. Copy the folder into `C:\xampp\htdocs\`.
3. Start **Apache** and **MySQL** in the XAMPP Control Panel.
4. Open `http://localhost/xss_secure/` in your browser.

The database (`xss_demo`) and its tables are created automatically on first run.

## XSS Attack Demo (for learning only)

To reproduce the vulnerability on a local copy:

1. In `register.php`, replace `cleanInput($_POST['username'] ?? '')` with `$_POST['username'] ?? ''`.
2. In `dashboard.php`, replace `e($log['username'])` with `$log['username']`.
3. Register with this username: `<h1 style="color:red">HACKED BY XSS</h1>`
4. Log in and open the dashboard. The injected HTML is rendered (defacement).

Restore the original code to see the attack neutralized and displayed as plain text.

## Disclaimer

This project was made for educational purposes as part of a school activity. Only test attacks on your own local system.
