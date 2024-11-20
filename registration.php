<?php
session_start();

function connectToDatabase() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brgy45_medsdb";
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

$pdo = connectToDatabase();
$specialAdminPassword = 'adminSecret123'; // Replace with your actual admin password

// Initialize error variables
$errors = [
    'username' => '',
    'password' => '',
    'first_name' => '',
    'middle_initial' => '',
    'surname' => '',
    'age' => '',
    'address' => '',
    'gender' => '',
    'email' => '',
    'occupation' => '',
    'civil_status' => '',
    'pwd' => '',
    'blood_type' => '',
    'admin_password' => ''
];

// Initialize variables with default values or null
$username = $password = $firstName = $middleInitial = $surname = $birthday = $age = $address = $gender = $email = $occupation = $civilStatus = $pwd = $bloodType = $role = $adminPassword = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assign the POST values ONLY if the form is submitted
   $username = trim($_POST['username']);
   $password = trim($_POST['password']);
   $firstName = trim($_POST['first_name']);
   $middleInitial = trim($_POST['middle_initial']);
   $surname = trim($_POST['surname']);
   $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : ''; // Use the null coalescing operator
   $age = trim($_POST['age']); // Note: $age will be calculated, so this line is unnecessary
   $houseNumber = $_POST['house_number'] ?? '';
   $street = $_POST['street'] ?? '';
   $gender = $_POST['gender'] ?? '';
   $email = trim($_POST['email']);
   $occupation = trim($_POST['occupation']);
   $civilStatus = $_POST['civil_status'] ?? '';
   $pwd = $_POST['pwd'] ?? '';
   $bloodType = $_POST['blood_type'] ?? '';
   $role = $_POST['role'] ?? ''; // Get the role or provide a default
   $adminPassword = $_POST['admin_password'] ?? '';
   $address = $houseNumber ? $houseNumber . ' ' . $street : $street;

   // Validate input
if (!preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
    $errors['username'] = 'Username must be at least 5 characters long and contain only letters and numbers.';
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
    $errors['password'] = 'Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.';
}

if (empty($firstName)) {
    $errors['first_name'] = 'First name cannot be empty.';
}

if (!preg_match('/^[a-zA-Z.]{0,2}$/', $middleInitial)) {  // Updated regex
    $errors['middle_initial'] = 'Middle initial must only contain letters and a period (max 2 characters).';
}

if (empty($surname)) {
    $errors['surname'] = 'Surname cannot be empty.';
}

if (empty($birthday)) {
    $errors['birthday'] = 'Birthday cannot be empty.';
} else {
    $birthDate = DateTime::createFromFormat('Y-m-d', $birthday);
    $currentDate = new DateTime();

    if (!$birthDate || $birthDate > $currentDate) {
        $errors['birthday'] = 'Invalid birthday. Please select a valid date.';
    } else {
        $age = $currentDate->diff($birthDate)->y;

        if ($age < 1 || $age > 120) {
            $errors['birthday'] = 'Age must be between 1 and 120 years based on the provided birthday.';
        }
    }
}

if (!is_numeric($age) || $age < 1 || $age > 120) {
    $errors['age'] = 'Age must be a number between 1 and 120.';
}

if (empty($address)) {
    $errors['address'] = 'Address cannot be empty.';
}

if (empty($gender)) {
    $errors['gender'] = 'Gender cannot be empty.';
} elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
    $errors['gender'] = 'Invalid gender selected.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format.';
}

if (empty($occupation)) {
    $errors['occupation'] = 'Occupation cannot be empty.';
}

if (empty($civilStatus)) {
    $errors['civil_status'] = 'Civil status cannot be empty.';
}

if (empty($pwd) || !in_array($pwd, ['Yes', 'No'])) {
    $errors['pwd'] = 'PWD must be either Yes or No.';
}

if (empty($bloodType) || !in_array($bloodType, ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])) {
    $errors['blood_type'] = 'Please select a valid blood type.';
}

if ($role === 'admin' && $adminPassword !== $specialAdminPassword) {
    $errors['admin_password'] = 'Invalid admin password. Please try again.';
}

if (!array_filter($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, first_name, middle_initial, surname, birthday, house_number, street, gender, role, email, occupation, civil_status, pwd, blood_type) 
                               VALUES (:username, :password, :first_name, :middle_initial, :surname, :birthday, :house_number, :street, :gender, :role, :email, :occupation, :civil_status, :pwd, :blood_type)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':middle_initial', $middleInitial);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':house_number', $houseNumber);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':civil_status', $civilStatus);
        $stmt->bindParam(':pwd', $pwd);
        $stmt->bindParam(':blood_type', $bloodType);

        $stmt->execute();

        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $errors['username'] = "Username already exists. Please choose a different one.";
        } else {
            error_log("Database error during registration: " . $e->getMessage()); // Log the error
            $errors['db'] = "Registration failed. Please try again later."; // User-friendly message
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($firstName ?? ''); ?>" oninput="capitalizeFirstLetter(this)">
                <div class="error-message"><?php echo $errors['first_name']; ?></div>
            </div>

            <div class="form-group">
                <label for="middle_initial">Middle Initial:</label>
                <input type="text" class="form-control" id="middle_initial" name="middle_initial" maxlength="2" oninput="autoCapitalizeAndAddPeriod(this)" value="<?php echo htmlspecialchars($middleInitial ?? ''); ?>">
                <div class="error-message"><?php echo $errors['middle_initial']; ?></div>
            </div>

            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" class="form-control" id="surname" name="surname" required value="<?php echo htmlspecialchars($surname ?? ''); ?>">
                <div class="error-message"><?php echo $errors['surname']; ?></div>
            </div>

            <div class="form-group">
    <label for="username">Username:</label>
    <input type="text" class="form-control" id="username" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
    <div class="error-message"><?php echo $errors['username']; ?></div>
</div>

<div class="form-group">
    <label for="password">Password:</label>
    <input type="password" class="form-control" id="password" name="password" required>
    <div class="error-message"><?php echo $errors['password']; ?></div>
</div>

<div class="form-group">
    <label for="birthday">Birthday:</label>
    <input type="date" class="form-control" id="birthday" name="birthday" required value="<?php echo htmlspecialchars($birthday ?? ''); ?>">
    <div class="error-message"><?php echo $errors['birthday']; ?></div>
</div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" class="form-control" id="age" name="age" required value="<?php echo isset($age) ? htmlspecialchars($age) : ''; ?>" readonly>
                <?php if (!empty($ageError)): ?><div class="error-message"><?php echo $ageError; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
    <label for="address">Address:</label>
    <input type="text" class="form-control" id="house_number" name="house_number" placeholder="House Number" value="<?php echo htmlspecialchars($houseNumber ?? ''); ?>">
    <select class="form-control" id="street" name="street">
        <option value="">Select Street</option>
        <option value="Asuncion Ext. Tondo, Manila." <?php echo ($street == "Asuncion Ext. Tondo, Manila.") ? 'selected' : ''; ?>>Asuncion Ext. Tondo, Manila.</option>
                    <option value="General Barientos St. Tondo, Manila.">General Barientos St. Tondo, Manila.</option>
                    <option value="P. Soriano St. Tondo, Manila.">P. Soriano St. Tondo, Manila.</option>
                    <option value="Camba Ext. Tondo, Manila.">Camba Ext. Tondo, Manila.</option>
                    <option value="Madrid Ext. Tondo, Manila.">Madrid Ext. Tondo, Manila.</option>
                    <option value="Barcelona St. Tondo Manila.">Barcelona St. Tondo Manila.</option>
                    <option value="San Ramon St. Tondo, Manila.">San Ramon St. Tondo, Manila.</option>
                    <option value="Tuazon St. Tondo Manila.">Tuazon St. Tondo Manila.</option>
                    <option value="Morga St. Tondo Manila.">Morga St. Tondo Manila.</option>
                    <option value="P. Ortega St. Tondo Manila.">P. Ortega St. Tondo Manila.</option>
                    </select>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address ?? ''); ?>" readonly>
                    <div class="error-message"><?php echo $errors['address']; ?></div>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if(isset($gender) && $gender === "Male") echo "selected"; ?>>Male</option>
                    <option value="Female" <?php if(isset($gender) && $gender === "Female") echo "selected"; ?>>Female</option>
                    <option value="Other" <?php if(isset($gender) && $gender === "Other") echo "selected"; ?>>Other</option>
                </select>
                <?php if (!empty($genderError)): ?><div class="error-message"><?php echo $genderError; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <?php if (!empty($emailError)): ?><div class="error-message"><?php echo $emailError; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="occupation">Occupation:</label>
                <input type="text" class="form-control" id="occupation" name="occupation" required value="<?php echo htmlspecialchars($occupation ?? ''); ?>" oninput="capitalizeFirstLetter(this)">
                <div class="error-message"><?php echo $errors['occupation']; ?></div>
            </div>

            <div class="form-group">
                <label for="civil_status">Civil Status:</label>
                <select class="form-control" id="civil_status" name="civil_status" required>
                    <option value="">Select</option>
                    <option value="Single" <?php echo (isset($civilStatus) && $civilStatus === 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Married" <?php echo (isset($civilStatus) && $civilStatus === 'Married') ? 'selected' : ''; ?>>Married</option>
                    <option value="Widowed" <?php echo (isset($civilStatus) && $civilStatus === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                    <option value="Divorced" <?php echo (isset($civilStatus) && $civilStatus === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                </select>
                <div class="error-message"><?php echo $errors['civil_status']; ?></div>
            </div>

            <div class="form-group">
                <label for="pwd">PWD:</label>
                <select class="form-control" id="pwd" name="pwd" required>
                    <option value="Yes" <?php echo (isset($pwd) && $pwd === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="No" <?php echo (isset($pwd) && $pwd === 'No') ? 'selected' : ''; ?>>No</option>
                </select>
                <div class="error-message"><?php echo $errors['pwd']; ?></div>
            </div>

            <div class="form-group">
                <label for="blood_type">Blood Type:</label>
                <select class="form-control" id="blood_type" name="blood_type" required>
                    <option value="">Select</option>
                    <option value="A+" <?php echo (isset($bloodType) && $bloodType === 'A+') ? 'selected' : ''; ?>>A+</option>
                    <option value="A-" <?php echo (isset($bloodType) && $bloodType === 'A-') ? 'selected' : ''; ?>>A-</option>
                    <option value="B+" <?php echo (isset($bloodType) && $bloodType === 'B+') ? 'selected' : ''; ?>>B+</option>
                    <option value="B-" <?php echo (isset($bloodType) && $bloodType === 'B-') ? 'selected' : ''; ?>>B-</option>
                    <option value="AB+" <?php echo (isset($bloodType) && $bloodType === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                    <option value="AB-" <?php echo (isset($bloodType) && $bloodType === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                    <option value="O+" <?php echo (isset($bloodType) && $bloodType === 'O+') ? 'selected' : ''; ?>>O+</option>
                    <option value="O-" <?php echo (isset($bloodType) && $bloodType === 'O-') ? 'selected' : ''; ?>>O-</option>
                </select>
                <div class="error-message"><?php echo $errors['blood_type']; ?></div>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required onchange="toggleAdminPasswordField()">
                    <option value="user" <?php echo (isset($role) && $role == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo (isset($role) && $role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group" id="admin-password-field" style="display:none;">
                <label for="admin_password">Admin Password:</label>
                <input type="password" class="form-control" id="admin_password" name="admin_password">
                <div class="error-message"><?php echo $errors['admin_password']; ?></div>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <script>
    document.getElementById('birthday').addEventListener('change', function() {
        const birthday = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthday.getFullYear();
        const monthDifference = today.getMonth() - birthday.getMonth();
        
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthday.getDate())) {
            age--;
        }
        
        document.getElementById('age').value = age > 0 ? age : 0;
    });

    function toggleAdminPasswordField() {
      var role = document.getElementById("role").value;
      var adminPasswordField = document.getElementById("admin-password-field");
      if (role === "admin") {
        adminPasswordField.style.display = "block";
      } else {
        adminPasswordField.style.display = "none";
      }
    }
    toggleAdminPasswordField(); // Call on page load

    function combineAddress() {
            const houseNumber = document.getElementById('house_number').value;
            const street = document.getElementById('street').value;
            const combinedAddress = houseNumber ? `${houseNumber} ${street}` : street;  // Include house number if entered
            document.getElementById('address').value = combinedAddress;
        }
        document.getElementById('house_number').addEventListener('input', combineAddress);
        document.getElementById('street').addEventListener('change', combineAddress);

        function capitalizeFirstLetter(input) {
            let words = input.value.split(" ");
            let capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));
            input.value = capitalizedWords.join(" ");
        }

        function autoCapitalizeAndAddPeriod(input) {
            let value = input.value.toUpperCase();
            if (value.length === 1 && !value.endsWith(".")) {
                value += ".";
            }
            input.value = value.slice(0, 2); // Limit to 2 characters (letter + period)
        }
  </script>
</body>
</html>