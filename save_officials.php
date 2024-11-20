<?php
// Function to handle file uploads
function handleFileUpload($file) {
    $targetDir = "images/brgyoff/";
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }

    // Check file size (limit to 2MB)
    if ($file["size"] > 2000000) {
        return ["success" => false, "message" => "Sorry, your file is too large."];
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return ["success" => false, "message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }

    // Attempt to upload the file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ["success" => true, "fileName" => basename($file["name"])];
    } else {
        return ["success" => false, "message" => "Sorry, there was an error uploading your file."];
    }
}

// Sample data array to represent officials (in a real application, this should be fetched from a database)
session_start(); // Start a session to hold the officials array
if (!isset($_SESSION['officials'])) {
    $_SESSION['officials'] = []; // Initialize the officials array in session
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' && isset($_FILES['officialImage'], $_POST['name'], $_POST['position'])) {
            $uploadResult = handleFileUpload($_FILES['officialImage']);
            if ($uploadResult['success']) {
                // Add official to the session array
                $_SESSION['officials'][] = [
                    'id' => uniqid(), // Generate a unique ID for the official
                    'image' => $uploadResult['fileName'],
                    'name' => $_POST['name'],
                    'position' => $_POST['position']
                ];
                echo json_encode(["success" => true, "message" => "Official added successfully!"]);
            } else {
                echo json_encode($uploadResult);
            }
        } elseif ($action === 'edit' && isset($_POST['id'], $_POST['name'], $_POST['position'])) {
            $id = $_POST['id'];
            $index = array_search($id, array_column($_SESSION['officials'], 'id'));

            if ($index !== false) {
                if (isset($_FILES['officialImage']) && $_FILES['officialImage']['error'] === UPLOAD_ERR_OK) {
                    // If an image is uploaded, handle the upload
                    $uploadResult = handleFileUpload($_FILES['officialImage']);
                    if ($uploadResult['success']) {
                        $_SESSION['officials'][$index]['image'] = $uploadResult['fileName'];
                    } else {
                        echo json_encode($uploadResult);
                        exit; // Exit if there was an upload error
                    }
                }
                // Update the official's name and position
                $_SESSION['officials'][$index]['name'] = $_POST['name'];
                $_SESSION['officials'][$index]['position'] = $_POST['position'];

                echo json_encode(["success" => true, "message" => "Official updated successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Official not found."]);
            }
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $index = array_search($id, array_column($_SESSION['officials'], 'id'));
            if ($index !== false) {
                unset($_SESSION['officials'][$index]);
                echo json_encode(["success" => true, "message" => "Official deleted successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Official not found."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input."]);
        }
    }
}
?>
