<?php
session_start();

// Initialize error
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);

    // Check if OTP matches the one sent to the user
    if ($otp == $_SESSION['otp']) {
        // OTP is correct, redirect to the reset password page
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify OTP</button>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>