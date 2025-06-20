<?php
require_once 'functions.php';
session_start(); // Starts session to store email and code across requests

$message = '';
$showCodeField = false;

// Step 1: If session has email and code, show OTP form
if (isset($_SESSION['email_to_verify']) && isset($_SESSION['verification_code'])) {
    $showCodeField = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Step 1: Email submitted
    if (isset($_POST['submit_email']) && isset($_POST['email'])) {
        $email = trim($_POST['email']);

        // Only generate new code if not already present or email changed
        if (!isset($_SESSION['verification_code']) || $_SESSION['email_to_verify'] !== $email) {
            $code = generateVerificationCode();
            $_SESSION['email_to_verify'] = $email;
            $_SESSION['verification_code'] = $code;

            sendVerificationEmail($email, $code);
            $message = "✅ Verification code sent to your email.";
        } else {
            $message = "ℹ️ A verification code was already sent. Please check your email.";
        }

        $showCodeField = true;
    }

    // ✅ Step 2: OTP submitted
    if (isset($_POST['submit_verification']) && isset($_POST['verification_code'])) {
        $enteredCode = trim($_POST['verification_code']);
        $storedCode = $_SESSION['verification_code'] ?? '';
        $email = $_SESSION['email_to_verify'] ?? '';

        // Debugging output (optional, remove in production)
        // echo "<pre>Entered: $enteredCode\nStored: $storedCode</pre>";

        if ($enteredCode === $storedCode) {
            registerEmail($email);
            $message = "✅ Email verified successfully!";
            unset($_SESSION['email_to_verify'], $_SESSION['verification_code']);
            $showCodeField = false;
        } else {
            $message = "❌ Incorrect verification code.";
            $showCodeField = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GitHub Timeline Subscription</title>
</head>
<body>
    <h2>Subscribe to GitHub Timeline Updates</h2>

    <?php if ($message): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <!-- Step 1: Show email input form if code isn't yet requested -->
    <!-- Email input form -->
    <?php if (!$showCodeField): ?>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required>
        <button type="submit" name="submit_email" id="submit-email">Submit</button>
    </form>
    <?php endif; ?>
    <!-- Step 2: Show verification code input after email is submitted -->
    <!-- OTP verification form -->
    <?php if ($showCodeField): ?>
    <form method="POST">
        <label>Verification Code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required>
        <button type="submit" name="submit_verification" id="submit-verification">Verify</button>
    </form>
    <?php endif; ?>
</body>
</html>
