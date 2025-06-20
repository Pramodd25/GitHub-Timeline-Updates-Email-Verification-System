<?php
require_once 'functions.php';
session_start(); // Start a new or resume existing session to track user state

$message = '';
$email = $_POST['unsubscribe_email'] ?? $_GET['email'] ?? '';
$showCodeField = false;

// Show OTP input if session has unsubscribe email
if (isset($_SESSION['unsubscribe_email']) && isset($_SESSION['unsubscribe_code'])) {
    $showCodeField = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Handle unsubscribe email submission
    if (isset($_POST['submit_unsubscribe'])) {
        $code = generateVerificationCode();
        $_SESSION['unsubscribe_email'] = $email;
        $_SESSION['unsubscribe_code'] = $code;

        $subject = "Confirm Unsubscription";
        $body = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com\r\n";

        mail($email, $subject, $body, $headers);
        $message = "✅ Unsubscribe verification code sent to your email.";
        $showCodeField = true;
    }

    // Step 2: Handle OTP submission
    if (isset($_POST['submit_verification'])) {
        $userCode = trim($_POST['unsubscribe_verification_code']);
        $actualCode = $_SESSION['unsubscribe_code'] ?? '';
        $email = $_SESSION['unsubscribe_email'] ?? '';

        if ($userCode === $actualCode) {
            unsubscribeEmail($email);// Call custom function to unsubscribe the user
            $message = "✅ You have been unsubscribed.";
            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']); // Clear session
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
<head><title>Unsubscribe</title></head>
<body>
<h2>Unsubscribe from GitHub Timeline</h2>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<!-- Step 1: Email input form (shown only if code is not yet sent) -->
<!-- Email form -->
<?php if (!$showCodeField): ?>
<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="unsubscribe_email" value="<?= htmlspecialchars($email) ?>" required>
    <button type="submit" id="submit-unsubscribe" name="submit_unsubscribe">Unsubscribe</button>
</form>
<?php endif; ?>

<!-- Step 2: OTP verification form (shown after code is sent) -->
<!-- OTP form -->
<?php if ($showCodeField): ?>
<form method="POST">
    <label>Verification Code:</label><br>
    <input type="text" name="unsubscribe_verification_code" required>
    <button type="submit" id="verify-unsubscribe" name="submit_verification">Verify</button>
</form>
<?php endif; ?>
</body>
</html>
