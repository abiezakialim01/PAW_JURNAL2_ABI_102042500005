<?php
require 'koneksi.php';

$res_mk = $conn->query("SELECT COUNT(*) AS total FROM mata_kuliah");
$count_mk = $res_mk->fetch_assoc()['total'];

$res_materi = $conn->query("SELECT COUNT(*) AS total FROM materi");
$count_materi = $res_materi->fetch_assoc()['total'];

$res_tugas = $conn->query("SELECT COUNT(*) AS total FROM tugas");
$count_tugas = $res_tugas->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Learning Management System</h1>
    <p style="margin-bottom: 25px; color: #64748b;">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <div class="card-grid">
        <div class="card">
            <h3>Mata kuliah</h3>
            <div class="count"><?= $count_mk ?></div>
            <a href="daftar_mata_kuliah.php" class="btn btn-primary" style="margin-top: 15px;">Kelola Mata Kuliah</a>
        </div>
        <div class="card">
            <h3>Materi</h3>
            <div class="count"><?= $count_materi ?></div>
            <a href="daftar_materi.php" class="btn btn-primary" style="margin-top: 15px;">Kelola Materi</a>
        </div>
        <div class="card">
            <h3>Tugas</h3>
            <div class="count"><?= $count_tugas ?></div>
            <a href="daftar_tugas.php" class="btn btn-primary" style="margin-top: 15px;">Kelola Tugas</a>
        </div>
    </div>

</body>
</html>