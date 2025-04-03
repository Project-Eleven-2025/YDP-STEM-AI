<?php
// Require PHPWord via Composer: composer require phpoffice/phpword
require '../../vendor/autoload.php'; // Adjusted the path to point to the correct location

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\TextRun;

// ------------------------------
// 1. File Upload & Format Guard
// ------------------------------

// Allowed file extensions
$allowed_extensions = ['docx', 'jpg', 'jpeg', 'png'];

if (!isset($_FILES['quizFile'])) {
    die("No file uploaded.");
}

$uploadedFile = $_FILES['quizFile'];
$filename = $uploadedFile['name'];
$tmpPath = $uploadedFile['tmp_name'];

// Validate the file extension (case-insensitive)
$fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowed_extensions)) {
    die("Error: Only DOCX or image files (JPG, JPEG, PNG) are allowed.");
}

// ------------------------------
// 2. Helper Functions to Parse DOCX Elements
// ------------------------------

/**
 * Recursively parses an element and returns its content as HTML.
 */
function parseElement($element) {
    $content = "";
    
    // If the element is a simple text element
    if (method_exists($element, 'getText')) {
        $content .= $element->getText();
    }
    // If the element is a table, convert it to an HTML table
    elseif ($element instanceof Table) {
        $content .= parseTable($element);
    }
    // If the element is an image, convert it to an HTML <img> tag with base64 data
    elseif ($element instanceof Image) {
        $content .= parseImage($element);
    }
    // If the element is a TextRun or another container with child elements
    elseif (method_exists($element, 'getElements')) {
        foreach ($element->getElements() as $child) {
            $content .= parseElement($child);
        }
    }
    
    return $content;
}

/**
 * Converts a PHPWord Table element to an HTML table.
 */
function parseTable(Table $table) {
    $html = "<table border='1' style='border-collapse: collapse;'>";
    foreach ($table->getRows() as $row) {
        $html .= "<tr>";
        foreach ($row->getCells() as $cell) {
            $cellContent = "";
            foreach ($cell->getElements() as $cellElement) {
                $cellContent .= parseElement($cellElement);
            }
            $html .= "<td style='padding: 5px;'>" . $cellContent . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

/**
 * Converts a PHPWord Image element to an HTML <img> tag.
 */
function parseImage(Image $image) {
    $src = $image->getSource();
    if (file_exists($src)) {
        $imageData = base64_encode(file_get_contents($src));
        $mimeType = mime_content_type($src);
        return "<img src='data:$mimeType;base64,$imageData' alt='Image' style='max-width:100%;'/>";
    } else {
        return "<img src='$src' alt='Image' style='max-width:100%;'/>";
    }
}

// ------------------------------
// 3. Process DOCX File & Parse Quiz Data
// ------------------------------
$quizData = []; // This will hold an array of quiz items

if ($fileExtension === 'docx') {
    try {
        $phpWord = IOFactory::load($tmpPath);
    } catch (Exception $e) {
        die("Error loading DOCX file: " . $e->getMessage());
    }
    
    // Extract all content as HTML (this will include text, tables, images, etc.)
    $allContent = "";
    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            $allContent .= parseElement($element) . "\n";
        }
    }
    
    // Now split the content into lines using newlines
    $lines = explode("\n", $allContent);
    $currentQuestion = null;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        
        // Check for the "question:" marker
        if (stripos($line, 'question:') === 0) {
            if ($currentQuestion !== null) {
                $quizData[] = $currentQuestion;
            }
            $currentQuestion = [
                'question' => trim(substr($line, strlen('question:'))),
                'answer' => '',
                'choices' => [], // Add choices array
                'requires_manual_import' => false
            ];
        }
        // Check for the "choice:[" marker
        elseif (stripos($line, 'choice:[') === 0 && $currentQuestion !== null) {
            $choicesContent = trim(substr($line, strlen('choice:['), -1)); // Remove "choice:[" and trailing "]"
            $choices = explode('|', $choicesContent); // Split choices by "|"
            $currentQuestion['choices'] = array_map('trim', $choices); // Add trimmed choices to the array
        }
        // Check for the "answer:" marker
        elseif (stripos($line, 'answer:') === 0 && $currentQuestion !== null) {
            $answerContent = trim(substr($line, strlen('answer:')));
            if (strtolower($answerContent) === '[import]') {
                $currentQuestion['requires_manual_import'] = true;
                $currentQuestion['answer'] = '';
            } else {
                $currentQuestion['answer'] = $answerContent;
                $currentQuestion['requires_manual_import'] = false;
            }
        }
    }
    if ($currentQuestion !== null) {
        $quizData[] = $currentQuestion;
    }
    
    // For debugging, you can output the quiz data:
    // echo "<pre>" . print_r($quizData, true) . "</pre>";
    
} elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
    // For image uploads, handle separately (for example, store them in an uploads folder)
    $destination = 'uploads/' . basename($filename);
    if (move_uploaded_file($tmpPath, $destination)) {
        echo "Image uploaded successfully to " . $destination;
    } else {
        die("Error uploading image.");
    }
    // Exit because further quiz parsing is intended for DOCX files
    exit;
}

// ------------------------------
// 4. Upload Quiz Data with Course Metadata to MySQL
// ------------------------------

// Sample course metadata (you might gather these from a form via $_POST)
$courseID      = isset($_POST['courseID']) ? $_POST['courseID'] : uniqid('course_');
$label         = isset($_POST['label']) ? $_POST['label'] : 'Sample Quiz Course';
$type          = isset($_POST['type']) ? $_POST['type'] : 'quiz'; // e.g., lesson, quiz, assessment, memo
$created_by    = isset($_POST['created_by']) ? $_POST['created_by'] : 'teacher123';
$date_created  = date('Y-m-d H:i:s');
$access_control = isset($_POST['access_control']) ? $_POST['access_control'] : 'public';

// Convert the quiz data array to JSON for storage
$quizDataJson = json_encode($quizData);

try {
    // Connect to MySQL database using PDO
    $pdo = new PDO('mysql:host=localhost;dbname=yourdbname;charset=utf8', 'yourusername', 'yourpassword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        INSERT INTO course (courseID, label, type, date_created, created_by, access_control, quiz_data)
        VALUES (:courseID, :label, :type, :date_created, :created_by, :access_control, :quiz_data)
    ");
    
    $stmt->execute([
        ':courseID' => $courseID,
        ':label' => $label,
        ':type' => $type,
        ':date_created' => $date_created,
        ':created_by' => $created_by,
        ':access_control' => $access_control,
        ':quiz_data' => $quizDataJson
    ]);
    
    echo "Course quiz data uploaded successfully with Course ID: " . $courseID;
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
