<?php
// Include database connection
require_once 'db.php';

// Check if $pdo is defined and valid
if (!isset($pdo) || !$pdo) {
    die('Database connection error.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $quiz_id = $_POST['quiz_id'] ?? 0;

    // Validate required fields
    if (empty($title) || empty($quiz_id)) {
        echo "<script>alert('Title and Quiz ID are required.');</script>";
        exit;
    }

    // Insert quiz assignment into the database
    $stmt = $pdo->prepare("
        INSERT INTO quiz_assignments (title, description, created_at, updated_at, quiz_id)
        VALUES (?, ?, NOW(), NOW(), ?)
    "); // Removed file-related columns
    if (!$stmt->execute([$title, $description, $quiz_id])) {
        echo "<script>alert('Error inserting quiz assignment: " . implode(', ', $stmt->errorInfo()) . "');</script>";
        exit;
    }

    $assignment_id = $pdo->lastInsertId();

    // Assign quiz to all users in the "student" group
    $stmt = $pdo->prepare("
        SELECT id FROM users WHERE user_group = 'student'
    "); // Removed reference to quizzes.availableForUser
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $user_id = $row['id']; // Updated to fetch user ID directly from users table

        $assign_stmt = $pdo->prepare("
            INSERT INTO user_quiz_assignments (assignment_id, user_id, assigned_at)
            VALUES (?, ?, NOW())
        ");
        $assign_stmt->execute([$assignment_id, $user_id]);
    }

    echo "<script>alert('Quiz assignment created and assigned successfully.');</script>";
} else {
    // Fetch users in the "student" group
    $stmt = $pdo->prepare("
        SELECT id, fname, lname FROM users WHERE user_group = 'student'
    ");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate dropdown options for users
    $user_options = '';
    foreach ($result as $row) {
        $user_options .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</option>';
    }

    // Fetch quizzes for the dropdown
    $quiz_stmt = $pdo->prepare("
        SELECT id, title, description FROM quizzes
    "); // Changed 'name' to 'title' to match the correct column name
    $quiz_stmt->execute();
    $quiz_result = $quiz_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate dropdown options for quiz titles
    $quiz_options = '';
    $quiz_data = [];
    foreach ($quiz_result as $quiz) {
        $quiz_options .= '<option value="' . $quiz['id'] . '">' . htmlspecialchars($quiz['title']) . '</option>'; // Updated 'name' to 'title'
        $quiz_data[$quiz['id']] = ['description' => $quiz['description']];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
    <script>
        const quizData = <?php echo json_encode($quiz_data); ?>;
        function updateQuizDetails() {
            const quizId = document.getElementById('title').value;
            document.getElementById('quiz_id').value = quizId;
            document.getElementById('description').value = quizData[quizId]?.description || '';
        }
    </script>
</head>
<body>
    <form action="assign_quiz.php" method="POST">
        <h1>Assign Quiz</h1>
        <label for="title">Title:</label>
        <select id="title" name="title" onchange="updateQuizDetails()" required>
            <option value="">Select a quiz</option>
            <?php echo $quiz_options; ?>
        </select><br>

        <label for="quiz_id">Quiz ID:</label>
        <input type="text" id="quiz_id" name="quiz_id" readonly><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" readonly></textarea><br>

        <label for="user">Assign to User:</label>
        <select id="user" name="user">
            <option value="">Select a student</option>
            <?php echo $user_options; ?>
        </select><br>

        <button type="submit">Assign Quiz</button>
    </form>
</body>
</html>