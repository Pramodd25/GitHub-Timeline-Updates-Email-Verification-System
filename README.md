# 🕒 GitHub Timeline Updates + Email Verification System

This PHP-based project automates secure email OTP verification and simulates GitHub-style timeline updates via scheduled emails. It dynamically generates OTPs, sends them to registered users, and logs all activity — ensuring **no hardcoded codes** and maintaining best practices in secure backend automation.

---

## 🚀 Features

- 🔐 **Secure OTP Generation** – No hardcoded verification codes
- 📩 **Email Sending** – Uses PHP’s built-in `mail()` function
- 🕒 **Automated CRON Scheduler** – Sends timeline-style updates every 5 minutes
- 🧾 **Email Logging** – Logs all email actions to `mail_log.txt`
- 📜 **Unsubscribe Support** – `unsubscribe.php` handles user opt-outs
- 📁 **File-Based Storage** – Emails stored in `registered_emails.txt`
- ✅ **Skips Invalid Emails** – Avoids sending to empty or malformed addresses

---

## 🧰 Tech Stack

- **Backend**: PHP 7+
- **Scheduler**: CRON (Unix) or Task Scheduler (Windows)
- **Email Service**: PHP `mail()` function
- **Storage**: File-based (`.txt` for emails and logs)

---

## 📁 Project Structure

path: ->index.php
      ->type: php
      ->role: entrypoint
      ->description: Main user interface for email input and status.

  - path: ->functions.php
          ->type: php
          ->role: utility
          ->description: Functions for OTP generation and sending emails.

  - path: ->cron.php
          ->type: php
          ->role: automation
          ->description: CRON-executed script to send timeline updates via email.

  - path: ->unsubscribe.php
          ->type: php
          ->role: api
          ->description: Handles unsubscribe requests from users.

  - path: ->registered_emails.txt
          ->type: text
          ->role: data
          ->description: Stores list of active registered emails.

  - path: ->mail_log.txt
          ->type: text
          ->role: log
          ->description: Logs all email sending activities with status.

  - path: ->setup_cron.sh
          ->type: bash
          ->role: setup
          ->description: Schedules cron.php to run every 5 minutes via CRON.


---

## 🛠️ Setup Instructions

### 🔧 PHP & CRON Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/github-timeline-verification.git
   cd github-timeline-verification

2. Ensure PHP is installed: php -v
     
3. Configure php.ini (if using Windows):
[mail function]
SMTP = smtp.yourdomain.com
smtp_port = 25
sendmail_from = your-email@domain.com

4. Set up CRON job (Linux/Mac):
   chmod +x setup_cron.sh
   ./setup_cron.sh


📧How It Works
1.Users are listed in registered_emails.txt.

2.cron.php runs every 5 minutes (via CRON) and:

3.Generates a dynamic 6-digit verification code.

4.Sends the code as part of an email update to each user.

5.Logs success/failure in mail_log.txt.

6.unsubscribe.php allows users to opt out of future emails.


✅ Security Notes:

✅ No hardcoded verification codes

⏱️ OTPs expire per CRON cycle (adjustable)

❌ Invalid/empty emails are skipped automatically

