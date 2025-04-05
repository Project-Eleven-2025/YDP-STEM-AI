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
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        html, body {
            height: 100%;
        }
        .logo {
            background-color: white;
            padding: 20px;
            width: 150px;
            border-radius: 35px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .sidenav {
            width: 250px;
            position: fixed;
            height: 100%;
            background-color: #3e2723;
            padding-top: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
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
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidenav nav ul li a:hover {
            background-color: #5d4037;
            color: #ffffff;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
            font-size: 16px;
            height: 100%;
            overflow-y: auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            color: #5d4037;
            font-size: 24px;
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        form input[type="text"], form textarea, form input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f9f9f9;
        }
        form textarea {
            resize: vertical;
            min-height: 120px;
        }
        button {
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #007bff;
            color: #fff;
            border: none;
            font-size: 16px;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        button:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        a {
            transition: color 0.3s ease, text-decoration 0.3s ease;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .sidenav {
                width: 200px;
            }
            .content {
                margin-left: 200px;
                padding: 20px;
            }
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php
        include 'db.php';
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
                <li><a href="class.php?sessionID=<?php echo urlencode($session_id); ?>">Classes</a></li>
                <li><a href="quiz.php?sessionID=<?php echo urlencode($session_id); ?>">Quizzes</a></li>
                <li><a href="assessment.php?sessionID=<?php echo urlencode($session_id); ?>">Assessments</a></li>
                <li><a href="course-materials.php?sessionID=<?php echo urlencode($session_id); ?>">Course Lesson Materials</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
        <h1>Quizzes</h1>
        
        <h2>Available Course Quizzes</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f4f4f4; text-align: left;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Quiz Name</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Description</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch quizzes from the database using PDO
                    $query = "SELECT q.*, 
                                     MAX(s.score) AS highest_score, 
                                     MAX(s.created_at) AS latest_attempt 
                              FROM quizzes q
                              LEFT JOIN scores s ON q.id = s.quiz_id AND s.user_id = :user_id
                              GROUP BY q.id";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([':user_id' => $_SESSION['user_id']]);
                    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($quizzes) > 0) {
                        foreach ($quizzes as $row) {
                            echo "<tr>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['title']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['description']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ddd;'>";

                            // Removed check for availableForUser
                            echo "<a href='answer_quiz.php?quizID=" . urlencode($row['id']) . "&sessionID=" . urlencode($session_id) . "' 
                                    style='color: #28a745;' onclick=\"localStorage.setItem('quizID', '" . $row['id'] . "');\">Answer</a>";

                            // Display the highest score
                            if ($row['highest_score'] !== null) {
                                echo " | <span style='color: #007bff;'>Highest Score: {$row['highest_score']}</span>";
                            } else {
                                echo " | <span style='color: #6c757d;'>No score yet</span>";
                            }

                            // Display the latest attempt date
                            if ($row['latest_attempt'] !== null) {
                                echo " | <span style='color: #007bff;'>Last Attempt: " . date('Y-m-d', strtotime($row['latest_attempt'])) . "</span>";
                            } else {
                                echo " | <span style='color: #6c757d;'>No attempts yet</span>";
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='padding: 10px; text-align: center; border: 1px solid #ddd;'>No quizzes found.</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
