<?php
include "../op/admin/db.php"; // Ensure correct path
session_start();

if (!isset($_GET['quizID']) || !isset($_GET['sessionID'])) {
    echo "Invalid request.";
    exit();
}

$quiz_id = intval($_GET['quizID']);
$session_id = $_GET['sessionID'];

// Verify session logic if necessary
// ...existing session verification logic...

try {
    // Prepare the SQL query to delete the quiz
    $query = "DELETE FROM quizzes WHERE id = :quiz_id";
    $stmt = $pdo->prepare($query); // Use PDO to prepare the statement
    $stmt->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT); // Bind the quiz_id parameter

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to quiz management page after successful deletion
        header("Location: ../admin/quiz_management.php?sessionID=" . urlencode($session_id));
        exit();
    } else {
        echo "Error deleting quiz.";
    }
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}

?>
