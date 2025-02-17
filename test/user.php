<?php
$host = "localhost";
$dbname = "masterlist_db";  // Make sure this is correct
$user = "postgres";
$password = "your_password";

// Establishing connection
try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userbirthday = $_POST['userbirthday'];
    $email = $_POST['email'];  // Get the email input
    $group = $_POST['group']; // Get the selected group
    
    // Generate User ID
    $year = date("Y");
    $month_day = date("md"); // Get MMDD
    $userIndex = str_pad(rand(0, 9999999999), 10, "0", STR_PAD_LEFT); // 10-digit random number
    $uniqueID = rand(1000, 9999); // Unique control number (random 4-digit)
    $userID = "{$year}-{$group}{$userIndex}-{$uniqueID}-{$month_day}";

    $sql = "INSERT INTO masterlist (userID, fname, lname, username, password, userbirthday, email) 
            VALUES (:userID, :fname, :lname, :username, :password, :userbirthday, :email)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':userID' => $userID,
        ':fname' => $fname,
        ':lname' => $lname,
        ':username' => $username,
        ':password' => $password,
        ':userbirthday' => $userbirthday,
        ':email' => $email // Include email in the query
    ]);
}


// Handle user deletion
if (isset($_POST['delete'])) {
    $userID = $_POST['userID'];
    $sql = "DELETE FROM masterlist WHERE userID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':userID' => $userID]);
}

// Fetch all users to display in the table
$query = $conn->query("SELECT * FROM masterlist");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Manage Users</h1>

    <!-- User Input Form -->
    <form method="POST">
        <label>First Name: </label>
        <input type="text" name="fname" required><br><br>

        <label>Last Name: </label>
        <input type="text" name="lname" required><br><br>

        <label>Email: </label>
        <input type="email" name="email" required><br><br>

        <label>Username: </label>
        <input type="text" name="username" required><br><br>

        <label>Password: </label>
        <input type="password" name="password" required><br><br>

        <label>Birthday: </label>
        <input type="date" name="userbirthday" required><br><br>

        <label>Group: </label>
        <select name="group" required>
            <option value="ST">Student</option>
            <option value="STC">Student Teacher</option>
            <option value="TC">Teacher</option>
            <option value="AD">Admin/Developer</option>
        </select><br><br>

        <button type="submit" name="submit">Submit</button>
    </form>

    <hr>

    <!-- Users Table -->
    <h2>Users List</h2>
    <table>
        <tr>
        <tr>
        <th>UserID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Birthday</th>
        <th>Email</th>
        <th>Date Created</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['userID'] ?></td>
        <td><?= $user['fname'] ?></td>
        <td><?= $user['lname'] ?></td>
        <td><?= $user['username'] ?></td>
        <td><?= $user['userbirthday'] ?></td>
        <td><?= $user['email'] ?></td> <!-- Display email here -->
        <td><?= $user['datecreated'] ?></td>
        <td>
            <!-- Edit Button -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
                <button type="submit" name="edit">Edit</button>
            </form>
            <!-- View Button -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
                <button type="submit" name="view">View</button>
            </form>
            <!-- Delete Button -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
                <button type="submit" name="delete">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>

</body>
</html>
