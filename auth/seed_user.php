<?php
include '../config.php';

// User pertama (guru)
$username = "cikguManaf";
$password = "123456";          // password login
$role     = "guru";

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// elak duplicate
$check = mysqli_query($conn, "SELECT id_user FROM users WHERE username='$username' LIMIT 1");
if (mysqli_num_rows($check) > 0) {
    echo "Username sudah wujud.";
    exit;
}

$sql = "INSERT INTO users (username, password_hash, role)
        VALUES ('$username', '$password_hash', '$role')";

if (mysqli_query($conn, $sql)) {
    echo "User berjaya dicipta!<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
} else {
    echo "Ralat: " . mysqli_error($conn);
}
