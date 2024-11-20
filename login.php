<?php
// Start the session to manage user login status
session_start();

/**
 * Connects to the MySQL database.
 * @return PDO The PDO database connection object, or null if the connection fails.
 */
function connectToDatabase() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brgy45_medsdb";

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set the error mode to exception for easier debugging
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Handle connection errors
        die("Connection failed: " . $e->getMessage());
    }
}

// Establish a database connection
$pdo = connectToDatabase();

// Initialize an error variable
$error = '';

// Process the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form, trimming whitespace
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare and execute a query to fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a user with the given username exists
    if ($user) {
        // Verify the entered password against the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Check user role and set session variables accordingly
            if ($user['role'] === 'admin') {
                $_SESSION['adminLoggedIn'] = true;  // Set admin login status
                $_SESSION['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); // Sanitize username for session
                header("Location: administrator/home.php");  // Redirect to admin homepage
                exit();
            } else {
                $_SESSION['userLoggedIn'] = true;  // Set regular user login status
                $_SESSION['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); // Sanitize username for session
                header("Location: user/announcement.php"); // Redirect to regular user homepage
                exit();
            }
        } else {
            // Display error message if password verification fails
            $error = 'Invalid username or password. Please try again.';
        }
    } else {
        // Display error message if no user with the given username is found
        $error = 'Invalid username or password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay 45 - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="login.css">
    <style>
        /* Style for error messages */
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Login Form Section -->
        <div class="login-form">
            <h2>Login</h2>
            <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </div>

                <?php
                // Display error message if any
                if (!empty($error)) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                }
                ?>
            </form>
            <!-- Forgot password link -->
            <div class="forgot-password">
    <p><a href="otp/forgot_password.php">Forgot Password?</a></p>
</div>
            <!-- Registration Link -->
            <div class="create-account">
                <p>Don't have an account? <a href="registration.php">Create one</a></p>
            </div>
        </div>

        <!-- About Section -->
        <div class="maps">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241.3030449708949!2d120.9647126299992!3d14.607692307538686!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca125940b74d%3A0x8d517e2661e4bed2!2sBarangay%2045%20Barangay%20Hall!5e0!3m2!1sen!2sph!4v1728296376106!5m2!1sen!2sph" 
                width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>