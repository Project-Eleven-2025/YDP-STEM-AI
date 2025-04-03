<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
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
            padding: 10px 15px;
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
        #loginAttempts {
            display: none;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
        // Assuming you have a database connection file
        include 'db_connection.php';

        // Start the session
        session_start();

        // Check if the session ID is passed in the URL
        if (!isset($_GET['sessionID'])) {
            echo "Session ID is missing.";
            echo "<a href='../login.html'>Go to login</a>";
            exit();
        }

        // Get the session ID from the URL
        $session_id = $_GET['sessionID'];
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
    <div class="content">
    <?php
        // Verify the session ID with the database
        $query = "SELECT * FROM login_attempts WHERE session_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();

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
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        if (!$student) {
            echo "Profile not found.";
            echo "<a href='dashboard.php?sessionID=" . urlencode($session_id) . "'>Go back to dashboard</a>";
            exit();
        }
        ?>
        <h1 style="color: #2c3e50; font-family: Arial, sans-serif;">User Profile</h1>
        <p><strong>First Name:</strong> <span style="color: #34495e;"><?php echo isset($student['fname']) ? htmlspecialchars($student['fname']) : 'N/A'; ?></span></p>
        <p><strong>Last Name:</strong> <span style="color: #34495e;"><?php echo isset($student['lname']) ? htmlspecialchars($student['lname']) : 'N/A'; ?></span></p>
        <p><strong>Middle Name:</strong> <span style="color: #34495e;"><?php echo isset($student['mname']) ? htmlspecialchars($student['mname']) : 'N/A'; ?></span></p>
        <p><strong>Nickname:</strong> <span style="color: #34495e;"><?php echo isset($student['nname']) ? htmlspecialchars($student['nname']) : 'N/A'; ?></span></p>
        <p><strong>Email:</strong> <span style="color: #34495e;"><?php echo isset($student['email']) ? htmlspecialchars($student['email']) : 'N/A'; ?></span></p>
        <p><strong>Birthdate:</strong> <span style="color: #34495e;"><?php echo isset($student['birthdate']) ? htmlspecialchars($student['birthdate']) : 'N/A'; ?></span></p>
        <p><strong>Address:</strong> <span style="color: #34495e;"><?php echo isset($student['address']) ? htmlspecialchars($student['address']) : 'N/A'; ?></span></p>
        <p><strong>Phone:</strong> <span style="color: #34495e;"><?php echo isset($student['phone']) ? htmlspecialchars($student['phone']) : 'N/A'; ?></span></p>
        <p><strong>Gender:</strong> <span style="color: #34495e;"><?php echo isset($student['gender']) ? htmlspecialchars($student['gender']) : 'N/A'; ?></span></p>
        <p><strong>Course:</strong> <span style="color: #34495e;"><?php echo isset($student['course']) ? htmlspecialchars($student['course']) : 'N/A'; ?></span></p>
        <p><strong>School:</strong> <span style="color: #34495e;"><?php echo isset($student['school']) ? htmlspecialchars($student['school']) : 'N/A'; ?></span></p>
        <p><strong>User Group:</strong> <span style="color: #34495e;"><?php echo isset($student['user_group']) ? htmlspecialchars($student['user_group']) : 'N/A'; ?></span></p>
        <p><strong>Created At:</strong> <span style="color: #34495e;"><?php echo isset($student['created_at']) ? htmlspecialchars($student['created_at']) : 'N/A'; ?></span></p>

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
                $query = "SELECT * FROM login_attempts WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('s', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
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
            if (loginAttemptsDiv.style.display === 'none') {
                loginAttemptsDiv.style.display = 'block';
            } else {
                loginAttemptsDiv.style.display = 'none';
            }
            });
        </script>
        <a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Go back to dashboard</a>
    </div>
</body>
</html>