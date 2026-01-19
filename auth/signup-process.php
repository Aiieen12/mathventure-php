// Contoh logik semasa pelajar daftar akaun
$username = $_POST['username'];
$class_input = $_POST['class']; // Pelajar pilih kelas semasa daftar

// Masukkan ke table users dahulu, kemudian:
$stmt = $conn->prepare("INSERT INTO student (id_user, class, level, current_xp, coins, lives) VALUES (?, ?, 1, 0, 0, 5)");
$stmt->bind_param("is", $new_id_user, $class_input);
$stmt->execute();