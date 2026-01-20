<?php
// Ambil data user berdasarkan session ID
$userId = $_SESSION['id_user']; 

$sql = "SELECT * FROM student WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Sediakan variable untuk digunakan di sidebar
$username_display = $user_data['username']; // Contoh: anis21
$level_display = $user_data['level'];      // Contoh: Level 1
$avatar_display = $user_data['avatar_url']; // URL gambar dari DB

// Kira progress bar
$current_xp = $user_data['current_xp'];
$max_xp = 100; // Anda boleh setkan ikut level
$progress_percent = ($current_xp / $max_xp) * 100;
?>