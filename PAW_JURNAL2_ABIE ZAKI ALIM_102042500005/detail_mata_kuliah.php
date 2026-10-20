<?php
require 'koneksi.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_mata_kuliah.php");
    exit;
}

$stmt = $conn->prepare("SELECT kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: daftar_mata_kuliah.php");
    exit;
}

$data = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-box">
        <h1>Detail Mata Kuliah</h1>
        <p style="margin-bottom: 10px;"><strong>Kode:</strong> <?= htmlspecialchars($data['kode']) ?></p>
        <p style="margin-bottom: 10px;"><strong>Nama Mata Kuliah:</strong> <?= htmlspecialchars($data['nama']) ?></p>
        <p style="margin-bottom: 10px;"><strong>Dosen Pengampu:</strong> <?= htmlspecialchars($data['dosen']) ?></p>
        <p style="margin-bottom: 20px;"><strong>Semester:</strong> <?= htmlspecialchars($data['semester']) ?></p>

        <div>
            <a href="edit_mata_kuliah.php?id=<?= $id ?>" class="btn btn-primary">Edit data</a>
            <a href="daftar_mata_kuliah.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>
</html>