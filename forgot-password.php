<?php
require_once '../config.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $check = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $upd->bind_param("ss", $new_pass, $email);
        $upd->execute();
        $msg = "Kata laluan berjaya ditukar! <a href='../index.php'>Log Masuk</a>";
    } else {
        $msg = "Emel tidak ditemui.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title><link rel="stylesheet" href="../asset/css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f0f2f5;">
    <form method="POST" style="background:white; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
        <h2>Set Semula Kata Laluan</h2>
        <p><?php echo $msg; ?></p>
        <input type="email" name="email" placeholder="Emel Berdaftar" required style="width:100%; margin-bottom:10px; padding:10px;">
        <input type="password" name="new_password" placeholder="Kata Laluan Baru" required style="width:100%; margin-bottom:10px; padding:10px;">
        <button type="submit" style="width:100%; padding:10px; background:#2ecc71; color:white; border:none; border-radius:5px;">Tukar Kata Laluan</button>
    </form>
</body>
</html>