<?php
// Start the session
session_start();
function connectToDatabase() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brgy45_medsdb";
    try {
        // Create a new PDO connection to the MySQL database.
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set the PDO error mode to exception to handle errors more gracefully.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // If the connection fails, terminate the script and display the error message.
        die("Connection failed: " . $e->getMessage());
    }
}
try {
    $pdo = connectToDatabase(); 


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullName = $_POST['full_name'] ?? '';
        $age = (int) ($_POST['age'] ?? 0);
        $gender = $_POST['gender'] ?? '';
        $address = $_POST['address'] ?? '';
        $medicine = $_POST['medicine'] ?? '';
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $status = 'Pending'; // Set initial status

        // Input validation (add more validation as needed):
        if (empty($fullName) || empty($medicine) || $quantity <= 0) {
            // Handle invalid input (redirect back with an error message, etc.)
            $_SESSION['error_message'] = "Please fill in all the required fields and enter a valid quantity.";
            header("Location: appointment.php"); // Redirect back to the form
            exit();

        }



        try {
            $stmt = $pdo->prepare("INSERT INTO requests (full_name, age, gender, address, medicine, quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullName, $age, $gender, $address, $medicine, $quantity, $status]);

            // Success - Redirect back to the form or a success page
            $_SESSION['success_message'] = "Request submitted successfully!"; // Set success message in session
            header("Location: appointment.php");  // Redirect back to the form
            exit();
        } catch (PDOException $e) {
            // Handle database insertion errors
            error_log("Database error: " . $e->getMessage()); // Log the error
            $_SESSION['error_message'] = "Error submitting request. Please try again later." ; // Generic message to user
            header("Location: appointment.php"); // Redirect back with the error message
            exit();
        }
    } else {
        // Handle invalid requests (not POST)
        header("Location: appointment.php"); // Redirect to the form page
        exit();

    }



} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage()); // Handle connection errors
}

?>