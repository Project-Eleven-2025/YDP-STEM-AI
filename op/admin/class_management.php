<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management</title>
    <link rel="stylesheet" href="../css/ui.css">
</head>
<body>
    <?php
        include 'db_connection.php';
        session_start();

        if (!isset($_GET['sessionID'])) {
            echo "Session ID is missing.";
            echo "<a href='../login.html'>Go to login</a>";
            exit();
        }

        $session_id = $_GET['sessionID'];
        // ...verify session logic...
    ?>
    <h1>Class Management</h1>
    <p>Manage classes here.</p>
    <form action="../process/class_management_process.php" method="POST">
        <input type="hidden" name="sessionID" value="<?php echo htmlspecialchars($session_id); ?>">
        <label for="class_name">Class Name:</label>
        <input type="text" id="class_name" name="class_name" required>
        <label for="teacher">Teacher:</label>
        <input type="text" id="teacher" name="teacher" required>
        <button type="submit">Create Class</button>
    </form>
</body>
</html>
