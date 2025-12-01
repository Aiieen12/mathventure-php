<?php
require_once '../config.php';

if (!isset($_GET['u'])) {
    echo "—";
    exit;
}

$username = $_GET['u'];

// semak di DB
$stmt = $conn->prepare("SELECT user_level FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($level);

if ($stmt->num_rows > 0) {
    $stmt->fetch();

    if ($level == "guru") echo "Guru 👨‍🏫";
    else if ($level == "pelajar") echo "Pelajar 👧🧒";
    else echo ucfirst($level);

} else {
    echo "Tidak ditemui";
}
