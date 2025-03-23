<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../css/ui.css">
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
                <li><a href="profile.php?sessionID=<?php echo urlencode($session_id); ?>">Profile</a></li>
                <li><a href="assessment.php?sessionID=<?php echo urlencode($session_id); ?>">Assessment</a></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
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
    </div>
</body>
</html>