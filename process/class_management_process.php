<?php
include '../admin/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['sessionID'];
    $class_name = $_POST['class_name'];
    $teacher = $_POST['teacher'];

    // Verify session logic here...

    // Insert class into the database
    $query = "INSERT INTO classes (class_name, teacher, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $class_name, $teacher);

    if ($stmt->execute()) {
        echo "Class created successfully.";
    } else {
        echo "Error creating class: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
