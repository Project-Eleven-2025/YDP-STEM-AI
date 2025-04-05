<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #444;
        }
        .question-container {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .question-container p {
            font-weight: bold;
        }
        label {
            display: block;
            margin: 5px 0;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .score {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quiz Form</h1>
        <?php
        // Retrieve quizID and sessionID from the URL (GET parameters)
        $quizID = htmlspecialchars($_GET['quizID'] ?? $_POST['quizID'] ?? ''); // Fallback to POST if not in GET
        $sessionId = htmlspecialchars($_GET['sessionID'] ?? $_POST['sessionID'] ?? ''); // Fallback to POST if not in GET

        if (empty($quizID)) {
            echo '<p class="error">Error: quizID is missing. Please ensure it is provided in the URL or form submission.</p>';
            exit; // Stop execution if quizID is missing
        }
        ?>
        <form id="quizForm" action="submit_quiz.php" method="POST">
           
            <input type="hidden" name="quiz_id" value="<?php echo $quizID; ?>">
            <input type="hidden" name="score" value="<?php echo htmlspecialchars($_POST['score'] ?? ''); ?>">
            <input type="hidden" name="sessionID" value="<?php echo $sessionId; ?>"> <!-- Persist sessionID -->
            <?php
            // Include the database connection file
            require_once 'db.php'; // Adjust the path as needed

            $quizData = null;

            try {
                $userId = htmlspecialchars($_POST['user_id'] ?? ''); // Ensure user_id is provided
                // quizID is already retrieved above
                $query = "SELECT file_data 
                          FROM quizzes 
                          WHERE id = :quizID "; // Ensure the correct table name is used
                $stmt = $pdo->prepare($query); // Use prepared statement for security
                $stmt->execute(['quizID' => $quizID]); // Correctly bind the quizID parameter

                if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $fileData = $row['file_data'];
                    $quizData = ['questions' => []];

                    // Match all questions, choices, and answers
                    preg_match_all('/question:\s*(.+)/', $fileData, $questionMatches);
                    preg_match_all('/choice:\s*\[(.*?)\]/s', $fileData, $choicesMatches);
                    preg_match_all('/answer:\s*(.+)/', $fileData, $answerMatches);

                    // Iterate through matched questions
                    foreach ($questionMatches[1] as $index => $questionText) {
                        $choices = [];
                        if (!empty($choicesMatches[1][$index])) {
                            $rawChoices = explode('|', $choicesMatches[1][$index]); // Split choices by '|'
                            foreach ($rawChoices as $choice) {
                                $trimmedChoice = trim($choice); // Trim whitespace around each choice
                                if (!empty($trimmedChoice)) {
                                    $choices[] = $trimmedChoice; // Add cleaned choice to the array
                                }
                            }
                        }

                        $quizData['questions'][] = [
                            'question' => trim($questionText),
                            'choices' => $choices,
                            'answer' => trim($answerMatches[1][$index] ?? '')
                        ];
                    }
                }
            } catch (PDOException $e) {
                echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }

            if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                $totalScore = 0; // Initialize total score
                foreach ($quizData['questions'] as $index => $question) {
                    echo '<div class="question-container">';
                    echo '<p>Question ' . ($index + 1) . ': ' . htmlspecialchars($question['question']) . '</p>';
                    if (isset($question['choices']) && is_array($question['choices'])) {
                        foreach ($question['choices'] as $choice) {
                            $trimmedChoice = trim($choice); // Trim whitespace around each choice
                            if (!empty($trimmedChoice)) { // Ensure the choice is not empty
                                $value = substr($trimmedChoice, 0, 1); // Extract the letter (e.g., "A")
                                echo '<label>';
                                echo '<input type="radio" name="question' . ($index + 1) . '" value="' . htmlspecialchars($value) . '"> ';
                                echo htmlspecialchars($trimmedChoice); // Display the choice text
                                echo '</label><br>';
                            }
                        }
                    }
                    echo '</div>';
                }

                // Calculate the total score after form submission
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    foreach ($quizData['questions'] as $index => $question) {
                        $submittedAnswer = $_POST['question' . ($index + 1)] ?? ''; // Get the submitted answer
                        if (trim($submittedAnswer) === $question['answer']) { // Compare with the correct answer
                            $totalScore++; // Increment score for correct answer
                        }
                    }

                    // Fill the score input field with the calculated total score
                    echo '<script>
                        document.querySelector("input[name=\'score\']").value = ' . $totalScore . ';
                    </script>';

                    // Redirect to the same page with the total score in the URL
                    $redirectUrl = $_SERVER['PHP_SELF'] . '?quizID=' . urlencode($quizID) . '&sessionID=' . urlencode($sessionId) . '&score=' . $totalScore;
                    header('Location: ' . $redirectUrl);
                    exit; // Stop further execution after redirect
                }

                // Display the total score if it exists in the URL
                if (isset($_GET['score'])) {
                    echo '<p class="score">Total Score: ' . htmlspecialchars($_GET['score']) . '</p>';
                }
            } else {
                echo '<p class="error">No questions are currently available. Please check back later or contact the administrator for assistance.</p>';
            }
            ?>
            <button type="button" onclick="calculateScore()">Submit</button>
        </form>
        <script>
            function calculateScore() {
                let totalScore = 0;
                const questions = <?php echo json_encode($quizData['questions']); ?>;
                questions.forEach((question, index) => {
                    const selectedAnswer = document.querySelector(`input[name="question${index + 1}"]:checked`);
                    if (selectedAnswer && selectedAnswer.value.trim() === question.answer.trim()) {
                        totalScore++;
                    }
                });
                document.querySelector('input[name="score"]').value = totalScore; // Set the score field
                document.getElementById('quizForm').submit(); // Submit the form
            }
        </script>
        <p style="text-align: center; margin-top: 20px;">
            <a href="quiz.php?sessionID=<?php echo urlencode($sessionId); ?>" style="color: #007bff; text-decoration: none;">Back to Quiz List</a>
        </p>
    </div>
</body>
</html>
