<?php
// Establish a connection to the MySQL database using the MySQLi extension
$conn = new mysqli('localhost:3307', 'root', '', 'brgy45_medsdb.sql');

// Check if the connection to the database is successful
if ($conn->connect_error) {
    // If the connection fails, stop the script and show an error message
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve all announcements from the 'announcements' table, ordered by creation date in descending order
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- character encoding and set viewport for responsive design -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- page title -->
    <title>Barangay 45 - Announcements</title>
    
    <!-- Link to Bootstrap CSS for responsive layout and styling -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Link to external custom CSS for additional page styles -->
    <link rel="stylesheet" href="home1.css">
</head>
<body>
    <!-- Navbar toggle button for mobile devices -->
    <div class="navbar-toggle-box" onclick="toggleNav()">
        <img src="images/menuicon.png" alt="Menu Icon">
    </div>

    <!-- Navigation bar with links to different pages of the system -->
    <div class="navbar-links" id="navbar-links">
    <a href="home.php">Home</a>
        <a href="home.1.php">Announcements</a>
        <a href="admin_medications.php">All Medications</a>
        <a href="Officer.php">List of Barangay Officials</a>
        <a href="Medicine.php">Update Medicine</a>
        <a href="view_requests.php">Requests</a>
        <a href="contacts.php">Contacts</a>
        <a href="logout.php">Log Out</a>
    </div>

    <!-- Main content section where announcements are displayed -->
    <div class="main-content">
        <h1>Announcements</h1>
        
        <!-- Loop through each announcement record fetched from the database -->
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="announcement">
                <!-- Display the announcement title -->
                <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                
                <!-- Display the announcement content, convert newlines into <br> tags for formatting -->
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                
                <!-- If there is an image path, display the image and make it clickable for the modal view -->
                <?php if (!empty($row['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Announcement Image" onclick="openModal(this)">
                <?php endif; ?>
                
                <!-- If there is a video path, display the video with controls for playback -->
                <?php if (!empty($row['video_path'])): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($row['video_path']); ?>" type="video/mp4">
                        <!-- Show this message if the browser does not support the video tag -->
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
                
                <!-- Display the date when the announcement was posted -->
                <small>Posted on: <?php echo $row['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal to display images in full screen when clicked -->
    <div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- Image element to display the selected image in the modal -->
                    <img id="modalImage" src="" class="img-responsive" alt="Modal Image" style="max-width: 100%; height: auto;">
                </div>
                <div class="modal-footer">
                    <!-- Button to close the modal -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery library and Bootstrap JavaScript for modal functionality and responsiveness -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <!-- JavaScript to manage image modal and navbar toggle functionality -->
    <script>
        // Function to display the clicked image in the modal
        function openModal(imageElement) {
            var modalImage = document.getElementById('modalImage');
            modalImage.src = imageElement.src;  // Set the modal image source to the clicked image's source
            $('#imageModal').modal('show');     // Show the modal with the image
        }

        // Function to toggle the navbar links display for mobile view
        function toggleNav() {
            var navLinks = document.getElementById("navbar-links");
            if (navLinks.style.display === "block") {
                navLinks.style.display = "none";  // Hide the navbar links if they are visible
            } else {
                navLinks.style.display = "block"; // Show the navbar links if they are hidden
            }
        }
    </script>
</body>
</html>
