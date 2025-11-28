<?php
// auth/seed_user.php
// Fail ini untuk create user contoh dalam jadual `users`
// Lepas berjaya, boleh padam / rename fail ini demi keselamatan.

require_once '../config.php';

// =======================
// 1. SET MAKLUMAT USER CONTOH
// =======================

// Contoh guru
$username_guru   = 'cikguDemo';
$password_guru   = 'demo1234';       // password PLAIN yang aieen akan guna untuk login
$role_guru       = 'guru';

// Contoh pelajar
$username_pelajar = 'muridDemo';
$password_pelajar = 'demo1234';      // boleh sama senang nak ingat
$role_pelajar     = 'pelajar';

// Hash password
$hash_guru    = password_hash($password_guru, PASSWORD_DEFAULT);
$hash_pelajar = password_hash($password_pelajar, PASSWORD_DEFAULT);

// =======================
// 2. FUNGSI KECIL UNTUK SEED USER
// =======================

function seedUser($conn, $username, $hash, $role) {
    // check kalau username dah wujud
    $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    $result = $check->get_result();
    $exists = $result->num_rows > 0;
    $check->close();

    if ($exists) {
        echo "User '{$username}' sudah wujud, tak perlu insert lagi.<br>";
        return;
    }

    // kalau tak wujud, insert
    $stmt = $conn->prepare("
        INSERT INTO users (username, password_hash, role, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param('sss', $username, $hash, $role);

    if ($stmt->execute()) {
        echo "Berjaya create user '{$username}' dengan role '{$role}'.<br>";
    } else {
        echo "Gagal create user '{$username}': " . $stmt->error . "<br>";
    }

    $stmt->close();
}

// =======================
// 3. JALANKAN SEED UNTUK GURU & PELAJAR
// =======================

seedUser($conn, $username_guru, $hash_guru, $role_guru);
seedUser($conn, $username_pelajar, $hash_pelajar, $role_pelajar);

echo "<hr>";
echo "Sekarang cuba login di <a href='index.php'>halaman login</a>.<br>";
echo "Guru → username: <b>{$username_guru}</b> | password: <b>{$password_guru}</b><br>";
echo "Pelajar → username: <b>{$username_pelajar}</b> | password: <b>{$password_pelajar}</b><br>";
