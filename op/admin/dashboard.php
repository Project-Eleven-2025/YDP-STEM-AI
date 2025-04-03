<?php
session_start();

$servername = "localhost";
$username = "ydp-stem"; // Fixed: Corrected username
$password = "project2025"; // Fixed: Corrected password
$dbname = "masterlist_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['sessionID'])) {
    if (isset($_COOKIE['session_id'])) {
        $_SESSION['sessionID'] = $_COOKIE['session_id'];
    } else {
        //If sessionID is not set, redirect to login page
        echo "Session expired. Please log in again. COOKIE MISSING!<br>\n";
        header("Location: ../login.html");
        exit();
    }
}

// Check if the table exists
$table_check_query = "SHOW TABLES LIKE 'login_attempts'";
$table_check_result = $conn->query($table_check_query);
if ($table_check_result->num_rows == 0) {
    die("Error: The required table 'login_attempts' does not exist in the database. Please contact the administrator.");
}

$sessionID = $_SESSION['sessionID']; // Store sessionID in a separate variable
$query = "SELECT user_id, created_at FROM login_attempts WHERE session_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $sessionID);
$stmt->execute();
$stmt->bind_result($userID, $created_at);
$stmt->fetch();
$stmt->close();

if (!$created_at) {
    // If created_at is not retrieved, redirect to login page
    echo "Session expired. Please log in again. Datecreated Missing<br>\n";
    header("Location: ../login.html");
    exit();
}

$current_time = time();
$session_start_time = strtotime($created_at);
$session_age = $current_time - $session_start_time;

if ($session_age > 86400) { // 86400 seconds = 24 hours
    // Session is older than 24 hours, destroy session and redirect to login page
    session_unset();
    session_destroy();
    echo "Session expired. Please log in again. Session age invalid<br>\n";
    header("Location: ../login.html");
    exit();
}

$query = "SELECT nname FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID); // Updated parameter type to integer
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "User Nickname not found.<br>\n";
    // If nname is not found, redirect to login page
    header("Location: ../login.html");
    exit();
}
$stmt->bind_result($user_nickname);
$stmt->fetch();
$stmt->close();

// Session is valid, continue with the rest of the dashboard code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

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
        <h1>Welcome, <?php echo htmlspecialchars($user_nickname); ?>!</h1>
        <div class="class-list">
            <h2>Class List</h2>
            <ul>
            <?php
            $query = "SELECT class_name FROM classes WHERE teacherID = ?"; // Fixed: Corrected column name to teacherID
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userID); // Fixed: Corrected parameter type to integer
            $stmt->execute();
            $stmt->bind_result($class_name);
            while ($stmt->fetch()) {
                echo "<li>" . htmlspecialchars($class_name) . "</li>";
            }
            $stmt->close();
            ?>
            </ul>
        </div>
    </div>

<?php
function getQuizzesCompleted($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM quizzes WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getLessonsFinished($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM lessons WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getAssessmentsDone($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM assessments WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getUserCertificates($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM certificates WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getAccountAge($userID) {
    global $conn;
    $query = "SELECT created_at FROM users WHERE id = ?"; // Updated table and column name
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID); // Updated parameter type to integer
    $stmt->execute();
    $stmt->bind_result($created_at); // Updated variable name to match column name
    $stmt->fetch();
    $stmt->close();
    $current_date = new DateTime();
    $registration_date = new DateTime($created_at); // Updated variable name to match column name
    $interval = $current_date->diff($registration_date);
    return $interval->days;
}

$conn->close(); // Add this line here to close the connection at the end
?>
</body>
</html>