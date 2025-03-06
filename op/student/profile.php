<?php
// Assuming you have a database connection file
include 'db_connection.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['session_id'])) {
    header('Location: login.php');
    exit();
}

// Get the student ID from the session
$session_id = $_SESSION['session_id'];

// Fetch the student profile from the database
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Profile not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="op/css/modest.css"/>
    <link rel="stylesheet" href="op/css/master.css"/>
</head>
<body>
    <h1>Student Profile</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($student['age']); ?></p>
    <p><strong>Major:</strong> <?php echo htmlspecialchars($student['major']); ?></p>
</body>
</html>