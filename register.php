<?php
// STEP 1: Database credentials
$host = 'localhost';
$username = 'root';
$password = ''; // Leave blank for XAMPP
$dbname = 'registration_db';

// STEP 2: Connect to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// STEP 3: Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// STEP 4: Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = htmlspecialchars($_POST['first-name']);
    $lastName = htmlspecialchars($_POST['last-name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birthdate = $_POST['birthdate'];
    $specialization = $_POST['specialization'];
    $bio = htmlspecialchars($_POST['bio']);

    // File upload
    $uploadDir = "uploads/";
    $profilePicture = basename($_FILES["profile-picture"]["name"]);
    $targetFile = $uploadDir . $profilePicture;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $targetFile)) {
        $fileMsg = "Uploaded.";
    } else {
        $fileMsg = "Upload failed.";
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO users 
        (first_name, last_name, email, password, birthdate, specialization, bio, profile_picture)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $password, $birthdate, $specialization, $bio, $profilePicture);

    if ($stmt->execute()) {
        echo "<h2>✅ Registration successful!</h2>";
    } else {
        echo "<h2>❌ Error: " . $stmt->error . "</h2>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
