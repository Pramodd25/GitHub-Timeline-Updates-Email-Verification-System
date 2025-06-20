<?php
// Generates a 6-digit numeric verification code with leading zeros (e.g., "003472")
function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}
// Registers an email by appending it to 'registered_emails.txt' if it's valid and not already present
function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

    // Validate email before registering
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

// Removes an email from the subscription list (unsubscribe)
function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $updated = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $updated) . PHP_EOL);
}

// Sends a verification code email to the given address
function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    $success = mail($email, $subject, $message, $headers);

    // Log for debugging
    $logfile = __DIR__ . '/mail_log.txt';
    $status = $success ? 'SUCCESS' : 'FAIL';
    file_put_contents($logfile, "[$status] Sent to: $email with code $code\n", FILE_APPEND);
}
// Simulates fetching GitHub timeline data
function fetchGitHubTimeline() {
    // Simulated GitHub data (can be replaced with real API fetch)
    return json_encode([
        ["type" => "PushEvent", "actor" => ["login" => "testuser1"]],
        ["type" => "ForkEvent", "actor" => ["login" => "testuser2"]]
    ]);
}
// Sends formatted GitHub updates to all registered subscriber email
function formatGitHubData($data) {
    $events = json_decode($data, true);
    if (!is_array($events)) return "<p>No data available.</p>";

    $html = '<h2>GitHub Timeline Updates</h2>';
    $html .= '<table border="1"><tr><th>Event</th><th>User</th></tr>';
    foreach ($events as $event) {
        $type = htmlspecialchars($event['type'] ?? 'Unknown');
        $user = htmlspecialchars($event['actor']['login'] ?? 'Anonymous');
        $html .= "<tr><td>$type</td><td>$user</td></tr>";
    }
    $html .= '</table>';

    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $data = fetchGitHubTimeline();
    $html = formatGitHubData($data);

    $subject = "Latest GitHub Updates";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost/github-timeline-Pramodd25/src/unsubscribe.php?email=" . urlencode($email);
        $body = $html . '<p><a href="' . $unsubscribeLink . '" id="unsubscribe-button">Unsubscribe</a></p>';

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $success = mail($email, $subject, $body, $headers);
            file_put_contents(__DIR__ . '/mail_log.txt', "Mail to: $email => " . ($success ? "✅ Sent" : "❌ Failed") . "\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/mail_log.txt', "❌ Skipped invalid or empty recipient: $email\n", FILE_APPEND);
        }
    }
}
