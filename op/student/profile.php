<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../css/ui.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f9fafc;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .logo {
            background-color: white;
            padding: 20px;
            width: 150px;
            border-radius: 35px;
            margin-bottom: 10px;
        }
        .sidenav {
            width: 250px;
            position: fixed;
            height: 100%;
            background-color: #3e2723;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidenav nav ul {
            list-style-type: none;
            padding: 0;
        }
        .sidenav nav ul li {
            margin: 15px 0;
            text-align: center;
        }
        .sidenav nav ul li a {
            color: #d7ccc8;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
        }
        .sidenav nav ul li a:hover {
            background-color: #795548;
        }
        h2 {
            color: #5d4037;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h1 {
            color: #34495e;
            font-size: 28px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        p strong {
            color: #2c3e50;
        }
        a {
            transition: color 0.3s ease;
            color: #1abc9c;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #16a085;
        }
        #loginAttempts {
            display: none;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #34495e;
            color: #fff;
        }
        td {
            color: #2c3e50;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #ddd;
            display: block;
            margin: 0 auto 15px;
        }
        .content {
            margin-left: 270px;
            padding: 30px;
            max-width: 900px;
        }
        .profile-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: left;
            max-width: 600px;
            margin: 20px auto;
        }
        dl {
            display: grid;
            grid-template-columns: max-content auto;
            gap: 10px 15px;
            font-size: 16px;
            color: #555;
        }
        dt {
            font-weight: bold;
            color: #333;
        }
        dd {
            margin: 0;
            color: #444;
        }
        button {
            display: block;
            width: 100%;
            max-width: 250px;
            background-color: #1abc9c;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-top: 10px;
            text-align: center;
        }
        button:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>
    <?php

    session_start();
        // Include the database connection file
        $db_connection_path = 'db.php';
        if (file_exists($db_connection_path)) {
            include $db_connection_path;
        } else {
            die("Database connection file not found. Please check the path.");
        }

        // Start the session
       

        // Check if the session ID is passed in the URL
        if (!isset($_GET['sessionID'])) {
            echo "Session ID is missing.";
            echo "<a href='../login.html'>Go to login</a>";
            exit();
        }

        // Get the session ID from the URL
        $session_id = $_GET['sessionID'];

        // Verify the database connection
        if (!isset($pdo)) {
            echo "<div style='color: red; font-weight: bold; text-align: center; margin-top: 20px;'>";
            echo "Error: Unable to establish a database connection. Please contact the administrator.";
            echo "</div>";
            exit();
        }
    ?>
    <div class="sidenav">
        <nav>
            <ul>
                <li><img src="../logo/logo.svg" style="width:150px;" class="logo" alt="Logo"></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="profile.php?sessionID=<?php echo urlencode($session_id); ?>">Profile</a></li>
                <li><a href="class.php?sessionID=<?php echo urlencode($session_id); ?>">Class</a></li>
                <li><a href="account_settings.php?sessionID=<?php echo urlencode($session_id); ?>">Account</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
    <?php
        // Verify the session ID with the database
        $query = "SELECT * FROM login_attempts WHERE session_id = :session_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
        $stmt->execute();
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            echo "Invalid session ID.";
            echo "<a href='../login.html'>Go to login</a>";
            exit();
        }

        // Get the user ID from the login_attempts table
        $user_id = isset($session['user_id']) ? $session['user_id'] : null;

        // Debugging: Print user ID
        echo "<pre>";
        echo "User ID: " . htmlspecialchars($user_id) . "\n";
        echo "</pre>";

        if (!$user_id) {
            echo "User ID not found.";
            echo "<a href='dashboard.php?sessionID=" . urlencode($session_id) . "'>Go back to dashboard</a>";
            exit();
        }

        // Fetch the student profile from the database
        $query = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            echo "Profile not found.";
            echo "<a href='dashboard.php?sessionID=" . urlencode($session_id) . "'>Go back to dashboard</a>";
            exit();
        }
        ?>
        <div class="profile-card">
            <h1>User Profile</h1>
            <dl>
                <dt>First Name:</dt> <dd><?php echo isset($student['fname']) ? htmlspecialchars($student['fname']) : 'N/A'; ?></dd>
                <dt>Last Name:</dt> <dd><?php echo isset($student['lname']) ? htmlspecialchars($student['lname']) : 'N/A'; ?></dd>
                <dt>Middle Name:</dt> <dd><?php echo isset($student['mname']) ? htmlspecialchars($student['mname']) : 'N/A'; ?></dd>
                <dt>Nickname:</dt> <dd><?php echo isset($student['nname']) ? htmlspecialchars($student['nname']) : 'N/A'; ?></dd>
                <dt>Email:</dt> <dd><?php echo isset($student['email']) ? htmlspecialchars($student['email']) : 'N/A'; ?></dd>
                <dt>Birthdate:</dt> 
                <dd><?php echo isset($student['birthdate']) ? date("F d, Y", strtotime($student['birthdate'])) : 'N/A'; ?></dd>
                <dt>Address:</dt> <dd><?php echo isset($student['address']) ? htmlspecialchars($student['address']) : 'N/A'; ?></dd>
                <dt>Phone:</dt> <dd><?php echo isset($student['phone']) ? htmlspecialchars($student['phone']) : 'N/A'; ?></dd>
                <dt>Gender:</dt> <dd><?php echo isset($student['gender']) ? htmlspecialchars($student['gender']) : 'N/A'; ?></dd>
                <dt>Course:</dt> <dd><?php echo isset($student['course']) ? htmlspecialchars($student['course']) : 'N/A'; ?></dd>
                <dt>School:</dt> <dd><?php echo isset($student['school']) ? htmlspecialchars($student['school']) : 'N/A'; ?></dd>
                <dt>User Group:</dt> <dd><?php echo isset($student['user_group']) ? htmlspecialchars($student['user_group']) : 'N/A'; ?></dd>
                <dt>Created At:</dt> 
                <dd><?php echo isset($student['created_at']) ? date("F d, Y - h:i A", strtotime($student['created_at'])) : 'N/A'; ?></dd>
            </dl>
        </div>


        <h2>Login Attempts</h2>
        <button id="showLoginAttempts">Show Login Attempts</button>
        <div id="loginAttempts">
            <table>
            <thead>
                <tr>
                <th>ID</th>
                <th>Session ID</th>
                <th>Created At</th>
                <th>Logged Out At</th>
                <th>User ID</th>
                <th>Device OS</th>
                <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM login_attempts WHERE user_id = :user_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['session_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['logged_out_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['device_os']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            </table>
        </div>

        <script>
            document.getElementById('showLoginAttempts').addEventListener('click', function() {
                const loginAttemptsDiv = document.getElementById('loginAttempts');
                loginAttemptsDiv.style.display = loginAttemptsDiv.style.display === 'none' ? 'block' : 'none';
            });
        </script>
        <a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Go back to dashboard</a>
    </div>
</body>
</html>