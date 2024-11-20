<?php
session_start();
require 'db.php'; // Assuming you have a db connection file

// Initialize error and success messages
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $newPassword)) {
        $error = 'Password must be at least 8 characters long and contain one uppercase letter, one lowercase letter, and one number.';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $_SESSION['otp_email']]);

        $success = "Password reset successfully!";
        
        // Clear OTP session data
        unset($_SESSION['otp']);
        unset($_SESSION['otp_email']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>