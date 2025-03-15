<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    if (!empty($name) && !empty($email)) {
        echo "<h2>Form Submitted Successfully!</h2>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Email:</strong> $email</p>";
    } else {
        echo "<h2>Error: All fields are required.</h2>";
    }
} else {
    echo "<h2>Invalid Request</h2>";
}
?>
