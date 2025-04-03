<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../css/ui.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
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
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .logo {
            background-color: white;
            padding: 20px;
            width: 150px;
            border-radius: 35px;
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
            <li style="text-align: center;"><img src="../logo/logo.svg" class="logo" alt=""></li>
                <li><a href="dashboard.php?sessionID=<?php echo urlencode($session_id); ?>">Dashboard</a></li>
                <li><a href="profile.php?sessionID=<?php echo urlencode($session_id); ?>">Profile</a></li>
                <li><a href="user_management.php?sessionID=<?php echo urlencode($session_id); ?>">User Management</a></li>
                <li><a href="../logout.php?sessionID=<?php echo urlencode($session_id); ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
    <h1>User Management</h1>
    <p>Manage users here.</p>
    <form action="user_management.php" method="GET" style="margin-bottom: 30px;">
    <a href="register.html?sessionID=<?php echo urlencode($session_id); ?>" class="button">Register New User</a><br>
        <input type="hidden" name="sessionID" value="<?php echo htmlspecialchars($session_id); ?>">
        <label for="search">Search Users:</label>
        <input type="text" id="search" name="search" placeholder="Enter username or email">
        <button type="submit">Search</button>
    </form>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Fetch users from the database
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $query = "SELECT * FROM users WHERE username LIKE ? OR email LIKE ?";
                $stmt = $conn->prepare($query);
                $searchTerm = '%' . $search . '%';
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>
                        <form action='../process/user_management_process.php' method='POST' style='display: inline;'>
                            <input type='hidden' name='sessionID' value='" . htmlspecialchars($session_id) . "'>
                            <input type='hidden' name='user_id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' name='action' value='edit'>Edit</button>
                        </form>
                        <form action='../process/user_management_process.php' method='POST' style='display: inline;'>
                            <input type='hidden' name='sessionID' value='" . htmlspecialchars($session_id) . "'>
                            <input type='hidden' name='user_id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' name='action' value='delete' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>
