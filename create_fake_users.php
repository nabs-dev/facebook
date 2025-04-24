<?php
include 'db.php';
$names = ['Ayesha', 'Zainab', 'Ali', 'Usman', 'Fatima', 'Hassan', 'Sara', 'Bilal', 'Noor', 'Hamza'];
$emails = ['ayesha@gmail.com','zainab@gmail.com','ali@gmail.com','usman@gmail.com','fatima@gmail.com','hassan@gmail.com','sara@gmail.com','bilal@gmail.com','noor@gmail.com','hamza@gmail.com'];
$pass = password_hash("123456", PASSWORD_DEFAULT);

for ($i = 0; $i < 10; $i++) {
    $name = $names[$i];
    $email = $emails[$i];
    $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$pass')");
}
echo "10 fake users added!";
?>
