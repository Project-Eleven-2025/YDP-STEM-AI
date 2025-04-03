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
            overflow-x: hidden; /* Prevent horizontal overflow */
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
            overflow-y: auto; /* Enable vertical scrolling if content overflows */
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
            overflow-wrap: break-word; /* Handle long words or URLs */
        }
        h2 {
            color: #5d4037;
            font-size: 24px;
            margin-bottom: 20px;
        }
        form {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            overflow: hidden; /* Prevent content overflow within the form */
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
            <li style="text-align: center;"><img src="../logo/logo.svg" class="logo" alt=""></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="class_management.php?sessionID=<?php echo urlencode($session_id); ?>">Class Management</a></li>
                <li><a href="quiz_management.php?sessionID=<?php echo urlencode($session_id); ?>">Quiz Management</a></li>
                <li><a href="assessment_management.php?sessionID=<?php echo urlencode($session_id); ?>">Assessment Management</a></li>
                <li><a src="syllabus_manager.php?sessionID=<?php echo urlencode($session_id); ?>" href="#" title="Soon">Syllabus Management</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
        <h1 class="center-text" style="color: #333;">Assessment Management</h1>
        <p class="center-text" style="font-size: 1.2em; color: #555;">Manage assessments here.</p>
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

        <h2>Existing Assessments</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f4f4f4; text-align: left;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Assessment Name</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Details</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch assessments from the database
                    $query = "SELECT * FROM assessments WHERE session_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $session_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['assessment_name']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['details']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>
                                    <a href='edit_assessment.php?assessmentID={$row['id']}&sessionID=" . urlencode($session_id) . "' style='margin-right: 10px; color: #007bff;'>Edit</a>
                                    <a href='../process/delete_assessment.php?assessmentID={$row['id']}&sessionID=" . urlencode($session_id) . "' style='margin-right: 10px; color: #dc3545;' onclick='return confirm(\"Are you sure you want to delete this assessment?\");'>Delete</a>
                                    <a href='assign_assessment.php?assessmentID={$row['id']}&sessionID=" . urlencode($session_id) . "' style='color: #28a745;'>Assign</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='padding: 10px; text-align: center; border: 1px solid #ddd;'>No assessments found.</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div class="center-text" style="margin-top: 20px;">
        <a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Back to Dashboard</a>
    </div>
    <script src="../js/ui.js"></script>
    <script src="../js/assessment_management.js"></script>
</body>
</html>
