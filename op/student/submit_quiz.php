<?php
// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    session_start();
    $session_id = $_GET['sessionID'] ?? session_id(); // Get session_id from URL or fallback to PHP session

    // Check if session_id exists in the database
    $session_check_query = "SELECT COUNT(*) FROM login_attempts WHERE session_id = :session_id";
    $session_check_stmt = $pdo->prepare($session_check_query);
    $session_check_stmt->execute(['session_id' => $session_id]);
    $session_exists = $session_check_stmt->fetchColumn() > 0;

    if (!$session_exists) {
        // Insert session_id into the database
        $insert_session_query = "INSERT INTO login_attempts (session_id, created_at) VALUES (:session_id, NOW())";
        $insert_session_stmt = $pdo->prepare($insert_session_query);
        $insert_session_stmt->execute(['session_id' => $session_id]);
    }

    // Query the database to find the user_id associated with the session_id
    $query = "SELECT user_id FROM login_attempts WHERE session_id = :session_id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['session_id' => $session_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_id = $result['user_id'] ?? null;
    $quiz_id = $_POST['quiz_id'] ?? null; // Ensure the key matches the form input name
    $score = $_POST['score'] ?? null;

    // Debugging: Log the received POST data
    error_log("Received POST data: " . print_r($_POST, true));
    error_log("Parsed values - user_id: $user_id, quiz_id: $quiz_id, score: $score");


    if (empty($quiz_id) || $score === null) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit;
    }

    // Validate that quiz_id and score are numeric
    if (!is_numeric($quiz_id) || !is_numeric($score)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input: quiz_id and score must be numeric.']);
        exit;
    }

    // Insert the score into the database
    $insert_query = "INSERT INTO scores (user_id, quiz_id, score, created_at) VALUES (:user_id, :quiz_id, :score, NOW())";
    $insert_stmt = $pdo->prepare($insert_query);
    $params = [
        'user_id' => $user_id,
        'quiz_id' => $quiz_id,
        'score' => $score
    ];

    if ($insert_stmt->execute($params)) {
        echo "<script>
            alert('Quiz submitted successfully!');
            window.location.href = 'quiz.php?sessionID=" . urlencode($session_id) . "';
        </script>";
    } else {
        echo "<script>
            alert('Failed to record score.');
            window.location.href = 'quiz.php?sessionID=" . urlencode($session_id) . "';
        </script>";
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>