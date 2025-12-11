<?php
require_once '../config.php';

header("Content-Type: application/json");

if (!isset($_GET['u']) || trim($_GET['u']) == "") {
    echo json_encode([
        "status" => "empty",
        "label" => "—",
        "level" => null
    ]);
    exit;
}

$username = trim($_GET['u']);

$stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($level);

if ($stmt->num_rows > 0) {
    $stmt->fetch();

    // label text untuk UI
    $label = match($level) {
        "guru" => "Guru 👨‍🏫",
        "pelajar" => "Pelajar 👧🧒",
        "admin" => "Admin 🔧",
        default => ucfirst($level)
    };

    echo json_encode([
        "status" => "found",
        "label" => $label,
        "level" => $level
    ]);

} else {
    echo json_encode([
        "status" => "notfound",
        "label" => "Tidak ditemui",
        "level" => null
    ]);
}
