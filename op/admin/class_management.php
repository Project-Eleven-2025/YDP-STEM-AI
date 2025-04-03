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
            <li style="text-align: center;"><img src="../logo/logo.svg" style="width:150px;" alt=""></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="class_management.php?sessionID=<?php echo urlencode($session_id); ?>">Class Management</a></li>
                <li><a href="quiz_management.php?sessionID=<?php echo urlencode($session_id); ?>">Quiz Management</a></li>
                <li><a href="assessment_management.php?sessionID=<?php echo urlencode($session_id); ?>">Assessment Management</a></li>
                <li><a href="syllabus_manager.php?sessionID=<?php echo urlencode($session_id); ?>">Syllabus Management</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
        <h1>Class Management</h1>
        <p>Manage classes here.</p>
        <form action="../process/class_management_process.php" method="POST">
            <input type="hidden" name="sessionID" value="<?php echo htmlspecialchars($session_id); ?>">
            <label for="class_name">Class Name:</label>
            <input type="text" id="class_name" name="class_name" required>
            <label for="teacher">Teacher:</label>
            <input type="text" id="teacher" name="teacher" required>
            <button type="submit">Create Class</button>
        </form>
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
                // Fetch and display class data from the database
                $query = "SELECT * FROM classes"; // Replace with your actual table name
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['teacher']) . "</td>";
                        echo "<td><a href='edit_class.php?id=" . urlencode($row['id']) . "&sessionID=" . urlencode($session_id) . "'>Edit</a> | <a href='delete_class.php?id=" . urlencode($row['id']) . "&sessionID=" . urlencode($session_id) . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No classes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
