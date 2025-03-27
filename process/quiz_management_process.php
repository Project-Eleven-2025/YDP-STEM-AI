<?php
include '../admin/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['sessionID'];
    $quiz_name = $_POST['quiz_name'];
    $description = $_POST['description'];

    // Verify session logic here...

    // Insert quiz into the database
    $query = "INSERT INTO quizzes (quiz_name, description, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $quiz_name, $description);

    if ($stmt->execute()) {
        echo "Quiz created successfully.";
    } else {
        echo "Error creating quiz: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
