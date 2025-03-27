<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Management</title>
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
    <h1>Assessment Management</h1>
    <p>Manage assessments here.</p>
    <form action="../process/assessment_management_process.php" method="POST">
        <input type="hidden" name="sessionID" value="<?php echo htmlspecialchars($session_id); ?>">
        <label for="assessment_name">Assessment Name:</label>
        <input type="text" id="assessment_name" name="assessment_name" required>
        <label for="details">Details:</label>
        <textarea id="details" name="details" required></textarea>
        <button type="submit">Create Assessment</button>
    </form>
</body>
</html>
