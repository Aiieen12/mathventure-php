<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form
    $firstname        = trim($_POST['firstname']);
    $lastname         = trim($_POST['lastname']);
    $dob              = $_POST['dob'] ?? null;
    $class            = trim($_POST['class']);
    $year_level       = $_POST['year_level'] ?? null;
    $bio              = trim($_POST['bio']);

    $username         = trim($_POST['username']);
    $password         = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi asas
    if ($password !== $password_confirm) {
        $_SESSION['error'] = "Kata laluan dan sahihan tidak sama.";
        header("Location: ../register-student.php");
        exit;
    }

    if ($firstname === '' || $lastname === '' || $username === '') {
        $_SESSION['error'] = "Sila isi semua maklumat wajib.";
        header("Location: ../register-student.php");
        exit;
    }

    // Escape untuk SQL
    $username_sql   = mysqli_real_escape_string($conn, $username);
    $firstname_sql  = mysqli_real_escape_string($conn, $firstname);
    $lastname_sql   = mysqli_real_escape_string($conn, $lastname);
    $class_sql      = mysqli_real_escape_string($conn, $class);
    $bio_sql        = mysqli_real_escape_string($conn, $bio);

    $dob_sql        = $dob ? "'" . mysqli_real_escape_string($conn, $dob) . "'" : "NULL";
    $year_level_sql = $year_level !== '' ? (int)$year_level : "NULL";

    // Semak username unik
    $check = mysqli_query($conn, "SELECT id_user FROM users WHERE username = '$username_sql' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Username sudah digunakan. Sila pilih yang lain.";
        header("Location: ../register-student.php");
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $password_hash_sql = mysqli_real_escape_string($conn, $password_hash);

    // 1) Masuk ke jadual users (role = pelajar)
    $sql_user = "
        INSERT INTO users (username, password_hash, role)
        VALUES ('$username_sql', '$password_hash_sql', 'pelajar')
    ";

    if (!mysqli_query($conn, $sql_user)) {
        $_SESSION['error'] = "Ralat semasa simpan ke jadual users: " . mysqli_error($conn);
        header("Location: ../register-student.php");
        exit;
    }

    $id_user = mysqli_insert_id($conn);

    // 2) Masuk ke jadual student
    $sql_student = "
        INSERT INTO student (id_user, firstname, lastname, dob, class, year_level, bio, avatar)
        VALUES (
            $id_user,
            '$firstname_sql',
            '$lastname_sql',
            $dob_sql,
            '$class_sql',
            $year_level_sql,
            '$bio_sql',
            NULL
        )
    ";

    if (!mysqli_query($conn, $sql_student)) {
        // rollback kalau gagal
        mysqli_query($conn, "DELETE FROM users WHERE id_user = $id_user");
        $_SESSION['error'] = "Ralat semasa simpan ke jadual student: " . mysqli_error($conn);
        header("Location: ../register-student.php");
        exit;
    }

    // Berjaya
    $_SESSION['success'] = "Pendaftaran pelajar berjaya! Sila log masuk.";
    header("Location: ../index.php");
    exit;

} else {
    header("Location: ../register-student.php");
    exit;
}
