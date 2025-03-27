<?php
include '../admin/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['sessionID'];
    $assessment_name = $_POST['assessment_name'];
    $details = $_POST['details'];

    // Verify session logic here...

    // Insert assessment into the database
    $query = "INSERT INTO assessments (assessment_name, details, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $assessment_name, $details);

    if ($stmt->execute()) {
        echo "Assessment created successfully.";
    } else {
        echo "Error creating assessment: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
