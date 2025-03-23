<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../css/ui.css">
</head>
<body>
    <div class="sidenav">
        <nav>
            <ul>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="assessment.php">Assessment</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
    <?php
        // Assuming you have a database connection file
        include 'db_connection.php';

        // Start the session
        session_start();

        // Check if the user is logged in by verifying the session ID
        if (!isset($_SESSION['sessionID']) || !isset($_COOKIE['PHPSESSID'])) {
            header('Location: ../login.html');
            exit(); //commented out to allow testing
        }

        // Get the session ID from the session and cookies
        $session_id = isset($_SESSION['sessionID']) ? $_SESSION['sessionID'] : null;

        // Verify the session ID with the database
        $query = "SELECT * FROM login_attempts WHERE session_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();

        if (!$session) {
            header('Location: ../login.html');
            exit(); //commented out to allow testing
        }

        // Get the user ID from the session
        $user_id = isset($session['user_id']) && preg_match('/^\d{4}-student-\d{10}-\d{4}-\d{4}$/', $session['user_id']) ? $session['user_id'] : null;

        // Debugging: Print user ID
        echo "<pre>";
        echo "User ID: " . htmlspecialchars($user_id) . "\n";
        echo "</pre>";

        if (!$user_id) {
            echo "User ID not found.";
            echo "<a href='dashboard.php'>Go back to dashboard</a>";
            exit();
        }

        if ($user_id) {
            // Fetch the student profile from the database
            $query = "SELECT * FROM user_info WHERE userID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();

            if (!$student) {
                echo "Profile not found.";
                echo "<a href='dashboard.php'>Go back to dashboard</a>";
                exit();
            }
        } else {
            echo "User ID not found.";
            echo "<a href='dashboard.php'>Go back to dashboard</a>";
            exit();
        }
        ?>

        <h1>Student Profile</h1>
        <p><strong>Name:</strong> <?php echo isset($student['name']) ? htmlspecialchars($student['name']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?php echo isset($student['email']) ? htmlspecialchars($student['email']) : 'N/A'; ?></p>
        <p><strong>Age:</strong> <?php echo isset($student['age']) ? htmlspecialchars($student['age']) : 'N/A'; ?></p>
        <p><strong>Major:</strong> <?php echo isset($student['major']) ? htmlspecialchars($student['major']) : 'N/A'; ?></p>
    </div>
</body>
</html>