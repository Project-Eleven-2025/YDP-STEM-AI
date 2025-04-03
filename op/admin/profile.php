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
        .content {
            margin-left: 270px;
            padding: 20px;
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
        button {
            transition: background-color 0.3s ease;
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #16a085;
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
            border-radius: 50%;
            margin-bottom: 10px;
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
                <li><img src="../logo/logo.svg" style="width:150px;" class="logo" alt="Logo"></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="profile.php?sessionID=<?php echo urlencode($session_id); ?>">Profile</a></li>
                <li><a href="user_management.php?sessionID=<?php echo urlencode($session_id); ?>">User Management</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
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
        <h1>User Profile</h1>
        <p><strong>First Name:</strong> <?php echo isset($student['fname']) ? htmlspecialchars($student['fname']) : 'N/A'; ?></p>
        <p><strong>Last Name:</strong> <?php echo isset($student['lname']) ? htmlspecialchars($student['lname']) : 'N/A'; ?></p>
        <p><strong>Middle Name:</strong> <?php echo isset($student['mname']) ? htmlspecialchars($student['mname']) : 'N/A'; ?></p>
        <p><strong>Nickname:</strong> <?php echo isset($student['nname']) ? htmlspecialchars($student['nname']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?php echo isset($student['email']) ? htmlspecialchars($student['email']) : 'N/A'; ?></p>
        <p><strong>Birthdate:</strong> <?php echo isset($student['birthdate']) ? htmlspecialchars($student['birthdate']) : 'N/A'; ?></p>
        <p><strong>Address:</strong> <?php echo isset($student['address']) ? htmlspecialchars($student['address']) : 'N/A'; ?></p>
        <p><strong>Phone:</strong> <?php echo isset($student['phone']) ? htmlspecialchars($student['phone']) : 'N/A'; ?></p>
        <p><strong>Gender:</strong> <?php echo isset($student['gender']) ? htmlspecialchars($student['gender']) : 'N/A'; ?></p>
        <p><strong>Course:</strong> <?php echo isset($student['course']) ? htmlspecialchars($student['course']) : 'N/A'; ?></p>
        <p><strong>School:</strong> <?php echo isset($student['school']) ? htmlspecialchars($student['school']) : 'N/A'; ?></p>
        <p><strong>User Group:</strong> <?php echo isset($student['user_group']) ? htmlspecialchars($student['user_group']) : 'N/A'; ?></p>
        <p><strong>Created At:</strong> <?php echo isset($student['created_at']) ? htmlspecialchars($student['created_at']) : 'N/A'; ?></p>

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
                loginAttemptsDiv.style.display = loginAttemptsDiv.style.display === 'none' ? 'block' : 'none';
            });
        </script>
        <a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Go back to dashboard</a>
    </div>
</body>
</html>