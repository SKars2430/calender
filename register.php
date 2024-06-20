<?php 
if (isset($_POST['register'])) { 

    // Connect to the database 
    $mysqli = new mysqli("localhost", "root", "", "login_system"); 

    // Check for errors 
    if ($mysqli->connect_error) { 
        die("Connection failed: " . $mysqli->connect_error); 
    } 

    // Prepare and bind the SQL statement 
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)"); 

    // Get the form data 
    $username = $_POST['username']; 
    $email = $_POST['email']; 
    $password = $_POST['password']; 

    // Hash the password 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

    /* Debug output
    echo "Original Password: " . $password . "<br>";
    echo "Hashed Password: " . $hashed_password . "<br>";
    */
    
    $stmt->bind_param("sss", $username, $email, $hashed_password); 

    // Execute the SQL statement 
    if ($stmt->execute()) { 
        echo "New account created successfully!"; 
    } else { 
        echo "Error: " . $stmt->error; 
    } 

    // Close the connection 
    $stmt->close(); 
    $mysqli->close(); 
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="register.php" method="post">
        <label for="username">Username:</label> 
        <input id="username" name="username" required="" type="text" />
        <label for="email">Email:</label>
        <input id="email" name="email" required="" type="email" />
        <label for="password">Password:</label>
        <input id="password" name="password" required="" type="password" />
        <input name="register" type="submit" value="Register" />
    </form>
    <button onclick="window.location.href='login.php'">Go to Login</button>
</body>
</html>
