<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmpassword = htmlspecialchars($_POST['confirmpassword']);
 
    if (empty($name) || empty($email) || empty($password) || empty($confirmpassword)) {
        echo "<h2 class='text-red-500 font-bold text-center'>Error: All fields must be filled.</h2>";
    } 
    elseif ($password !== $confirmpassword) {
        echo "<h2 class='text-red-500 font-bold text-center'>Error: Passwords do not match.</h2>";
    }
    else {
        echo "<h2 class='text-green-500 font-bold text-center'>Account Created</h2>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Email:</strong> $email</p>";
    }
} else {
    echo "<h2 class='text-red-500 font-bold text-center'>Invalid Request</h2>";
}
?>