<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {
            // login berjaya
            $_SESSION['user_id']   = $user['id_user'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            if ($user['role'] === 'pelajar') {
                header("Location: ../dashboard-student.php");
            } else {
                header("Location: ../dashboard-teacher.php");
            }
            exit;

        } else {
            $_SESSION['error'] = "Kata laluan tidak tepat.";
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemui.";
    }

    header("Location: ../index.php");
    exit;

} else {
    header("Location: ../index.php");
    exit;
}
