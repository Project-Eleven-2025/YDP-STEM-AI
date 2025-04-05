<?php
    include 'db.php'; // Ensure this file initializes $pdo
    session_start();

    if (!isset($_GET['sessionID']) || !isset($_SESSION['user_id'])) {
        header("Location: ../login.html");
        exit();
    }

    $session_id = $_GET['sessionID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management</title>
    <link rel="stylesheet" href="../css/ui.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
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
        form {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px; /* Added padding for better spacing */
            background-color: #fff; /* Added background color for contrast */
            border-radius: 8px; /* Rounded corners for a modern look */
        }
        button {
            transition: background-color 0.3s ease;
            background-color: #007bff; /* Added default background color */
            color: #fff; /* White text for contrast */
            border: none; /* Removed border for cleaner look */
            padding: 10px 15px; /* Added padding for better click area */
            border-radius: 4px; /* Rounded corners for consistency */
            cursor: pointer; /* Pointer cursor for better UX */
        }
        button:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }
        a {
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        table th {
            background-color: #f4f4f9;
            color: #333;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="sidenav">
        <nav>
            <ul>
            <li style="text-align: center;"><img src="../logo/logo.svg" class="logo" alt=""></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="class.php?sessionID=<?php echo urlencode($session_id); ?>">Classes</a></li>
                <li><a href="quiz.php?sessionID=<?php echo urlencode($session_id); ?>">Quizzes</a></li>
                <li><a href="assessment.php?sessionID=<?php echo urlencode($session_id); ?>">Assessments</a></li>
                <li><a href="course-materials.php?sessionID=<?php echo urlencode($session_id); ?>">Course Lesson Materials</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Teacher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ensure $pdo is properly initialized in db.php
                $query = "SELECT * FROM classes"; // Replace with your actual table name
                $stmt = $pdo->query($query);
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($classes) {
                    foreach ($classes as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['teacher']) . "</td>";
                        echo "<td><a href='edit_class.php?id=" . urlencode($row['id']) . "&sessionID=" . urlencode($session_id) . "'>Edit</a> | <a href='delete_class.php?id=" . urlencode($row['id']) . "&sessionID=" . urlencode($session_id) . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Looks like classes haven't started yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
