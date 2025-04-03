<?php
// Database connection
include_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $id = $_POST['id'] ?? null;

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO syllabus (title, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $description);
            $stmt->execute();
        } elseif ($action === 'edit' && $id) {
            $stmt = $conn->prepare("UPDATE syllabus SET title = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $id);
            $stmt->execute();
        } elseif ($action === 'delete' && $id) {
            $stmt = $conn->prepare("DELETE FROM syllabus WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

// Fetch syllabus entries
$result = $conn->query("SELECT * FROM syllabus");
$syllabus = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus Manager</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f7f3e9;
            margin: 0;
            padding: 0;
            color: #4b3832;
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
        h1 {
            color: #5d4037;
            font-size: 24px;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff8e1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #6d4c41;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #5d4037;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #795548;
            color: #fff;
        }
        td {
            color: #4b3832;
        }
        .logo {
            background-color: #fff;
            padding: 15px;
            border-radius: 50%;
            margin-bottom: 10px;
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
        <h1>Syllabus Manager</h1>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <button type="submit">Add Syllabus</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($syllabus as $entry): ?>
                    <tr>
                        <td><?= $entry['id'] ?></td>
                        <td><?= $entry['title'] ?></td>
                        <td><?= $entry['description'] ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                <input type="text" name="title" value="<?= $entry['title'] ?>" required>
                                <textarea name="description" required><?= $entry['description'] ?></textarea>
                                <button type="submit">Edit</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
