<?php
include '../admin/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['sessionID'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Verify session logic here...

    // Insert user into the database
    $query = "INSERT INTO users (username, email, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username, $email);

    if ($stmt->execute()) {
        echo "User added successfully.";
    } else {
        echo "Error adding user: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
