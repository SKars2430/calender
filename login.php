<?php 
session_start(); 

if (isset($_POST['login'])) { 

    // Connect to the database 
    $mysqli = new mysqli("localhost", "root", "", "login_system"); 

    // Check for errors 
    if ($mysqli->connect_error) { 
        die("Connection failed: " . $mysqli->connect_error); 
    } 

    // Prepare and bind the SQL statement 
    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?"); 

    // Get the form data 
    $username = $_POST['username']; 
    $password = $_POST['password']; 

    $stmt->bind_param("s", $username); 

    // Execute the SQL statement 
    $stmt->execute(); 
    $stmt->store_result(); 

    // Check if the user exists 
    if ($stmt->num_rows > 0) { 

        // Bind the result to variables 
        $stmt->bind_result($id, $hashed_password); 

        // Fetch the result 
        $stmt->fetch(); 

        // Debugging outputs
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password) . "<br>";
        echo "Hashed Password from DB: " . htmlspecialchars($hashed_password) . "<br>";

        // Verify the password 
        if (password_verify($password, $hashed_password)) { 

            // Set the session variables 
            $_SESSION['loggedin'] = true; 
            $_SESSION['id'] = $id; 
            $_SESSION['username'] = $username; 

            // Redirect to the user's dashboard 
            header("Location: index.php"); 
            exit; 
        } else { 
            echo "Incorrect password!"; 
        } 
    } else { 
        echo "User not found!"; 
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input id="username" name="username" required="" type="text" />
        <label for="password">Password:</label> 
        <input id="password" name="password" required="" type="password" />
        <input name="login" type="submit" value="Login" />
    </form>
    <button onclick="window.location.href='register.php'">Need an account? Register here</button>
</body>
</html>
