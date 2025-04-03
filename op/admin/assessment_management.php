<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Management</title>
    <link rel="stylesheet" href="../css/ui.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .sidenav {
            width: 200px;
            position: fixed;
            height: 100%;
            background-color: #333;
            padding-top: 20px;
        }
        .sidenav nav ul {
            list-style-type: none;
            padding: 0;
        }
        .sidenav nav ul li {
            margin: 10px 0;
        }
        .sidenav nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 4px;
        }
        .sidenav nav ul li a:hover {
            background-color: #575757;
        }
        form {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
        }
        .center-text {
            text-align: center;
        }
    </style>
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
    <div class="sidenav">
        <nav>
            <ul>
                <li><a href="profile.php?sessionID=<?php echo urlencode($sessionID); ?>">Profile</a></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($sessionID); ?>">Dashboard</a></li>
                <li><a href="user_management.php?sessionID=<?php echo urlencode($sessionID); ?>">User Management</a></li>
                <li><a href="quiz_management.php?sessionID=<?php echo urlencode($sessionID); ?>">Quiz Management</a></li>
                <li><a href="assessment_management.php?sessionID=<?php echo urlencode($sessionID); ?>">Assessment Management</a></li>
                <li><a href="class_management.php?sessionID=<?php echo urlencode($sessionID); ?>">Class Management</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($sessionID); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <h1 class="center-text" style="color: #333;">Assessment Management</h1>
    <p class="center-text" style="font-size: 1.2em; color: #555;">Manage assessments here.</p>
    <!-- SQL for this form:
    CREATE TABLE assessments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        assessment_name VARCHAR(255) NOT NULL,
        details TEXT NOT NULL,
        file_data LONGBLOB,
        file_name VARCHAR(255),
        file_type VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    INSERT INTO assessments (session_id, assessment_name, details, file_data, file_name, file_type)
    VALUES (:session_id, :assessment_name, :details, :file_data, :file_name, :file_type);

    Notes:
    - `session_id` is passed as a hidden input.
    - `assessment_name` and `details` are collected from the text input and textarea respectively.
    - `file_data`, `file_name`, and `file_type` should be populated with the uploaded file's content, name, and type respectively.
    - Use prepared statements to prevent SQL injection.
    -->
    <form action="../process/assessment_management_process.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id); ?>">
        <label for="assessment_name">Assessment Name:</label>
        <input type="text" id="assessment_name" name="assessment_name" required>
        <label for="details">Details:</label>
        <textarea id="details" name="details" required></textarea>
        <label for="file_upload">Upload File (docx, picture, video, etc.):</label>
        <input type="file" id="file_upload" name="file_data" accept=".docx,.jpg,.jpeg,.png,.mp4,.avi">
        <button type="submit">Create Assessment</button>
    </form>
    <div class="center-text" style="margin-top: 20px;">
        <a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Back to Dashboard</a>
    </div>
    <script src="../js/ui.js"></script>
    <script src="../js/assessment_management.js"></script>
</body>
</html>
