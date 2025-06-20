#!/bin/bash
# This script sets up a CRON job to run cron.php every 5 minutes on Unix-like systems.
# For Windows, use Task Scheduler to run:
# php -c "C:/path/to/php.ini" "C:/path/to/cron.php"

CRON_CMD="*/5 * * * * php $(pwd)/cron.php"
( crontab -l 2>/dev/null; echo "$CRON_CMD" ) | crontab -
echo "✅ CRON job scheduled for every 5 minutes."
