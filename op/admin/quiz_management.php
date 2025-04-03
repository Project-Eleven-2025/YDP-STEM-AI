<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management</title>
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
        }
        button {
            transition: background-color 0.3s ease;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            transition: color 0.3s ease;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            color: #0056b3;
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
    <h1>Quiz Management</h1>
    <p>Manage quizzes here.</p>
    <form action="../process/quiz_management_process.php" method="POST">
        <input type="hidden" name="sessionID" value="<?php echo htmlspecialchars($session_id); ?>">
        <label for="quiz_name">Quiz Name:</label>
        <input type="text" id="quiz_name" name="quiz_name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Create Quiz</button>
    </form>
</body>
</html>
