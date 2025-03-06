<?php
// Define the passcode
define('ACCESS_PASSCODE', 'your_secure_passcode');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $passcode = $_POST['passcode'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verify the passcode
    if ($passcode === ACCESS_PASSCODE) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Here you would typically save the username and hashed password to a database
        // For demonstration, we'll just display a success message
        echo "Admin registered successfully!";
    } else {
        echo "Invalid passcode!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
</head>
<body>
    <h2>Admin and Teacher Registration</h2>
    <form method="post" action="">
        <label for="passcode">Passcode:</label>
        <input type="password" id="passcode" name="passcode" required><br><br>
        
        <label for="teacherID">Teacher ID:</label>
        <input type="text" id="teacherID" name="teacherID" required><br><br>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <label for="teacher_emailadd">Email Address:</label>
        <input type="email" id="teacher_emailadd" name="teacher_emailadd" required><br><br>
        
        <label for="teacher_phonenum">Phone Number:</label>
        <input type="text" id="teacher_phonenum" name="teacher_phonenum" required><br><br>
        
        <label for="teacher_fname">First Name:</label>
        <input type="text" id="teacher_fname" name="teacher_fname" required><br><br>
        
        <label for="teacher_lname">Last Name:</label>
        <input type="text" id="teacher_lname" name="teacher_lname" required><br><br>
        
        <label for="teacher_mname">Middle Name:</label>
        <input type="text" id="teacher_mname" name="teacher_mname"><br><br>
        
        <label for="teacher_post_nominal">Post Nominal:</label>
        <input type="text" id="teacher_post_nominal" name="teacher_post_nominal"><br><br>
        
        <label for="teacher_birthdate">Birthdate:</label>
        <input type="date" id="teacher_birthdate" name="teacher_birthdate" required><br><br>
        
        <label for="teacher_address">Address:</label>
        <input type="text" id="teacher_address" name="teacher_address" required><br><br>
        
        <label for="teacher_gender">Gender:</label>
        <select id="teacher_gender" name="teacher_gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br><br>
        
        <label for="teacher_faculty">Faculty:</label>
        <input type="text" id="teacher_faculty" name="teacher_faculty" required><br><br>
        
        <label for="group">Group:</label>
        <input type="text" id="group" name="group" required><br><br>
        
        <input type="submit" value="Register">
    </form>
</body>
</html>