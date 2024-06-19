<?php
// include database connection
include 'dbConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['mail'];
    $subscribe = isset($_POST['subscribe']) ? 1 : 0;
    $agree = isset($_POST['agree']) ? 1 : 0;

    // Save to database
    $query = "INSERT INTO subscribers (name, email, subscribe, agree) VALUES (:name, :email, :subscribe, :agree)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subscribe', $subscribe);
    $stmt->bindParam(':agree', $agree);

    if ($stmt->execute()) {
        echo "You have successfully subscribed!";
    } else {
        echo "There was an error with your subscription.";
    }
}

