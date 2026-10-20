<?php
require 'koneksi.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_tugas.php");
    exit;
}

$query = "SELECT t.id, t.judul, t.instruksi, t.tenggat, t.status, mk.kode, mk.nama AS nama_mk 
          FROM tugas t 
          JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id 
          WHERE t.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: daftar_tugas.php");
    exit;
}

$data = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Tugas</h1>
        <h3><?= htmlspecialchars($data['judul']) ?></h3>
        <p style="margin-bottom: 10px;"><strong>Mata kuliah:</strong> <?= htmlspecialchars($data['kode']) ?> - <?= htmlspecialchars($data['nama_mk']) ?></p>
        <p style="margin-bottom: 10px;"><strong>Tenggat:</strong> <?= htmlspecialchars($data['tenggat']) ?></p>
        <p style="margin-bottom: 15px;"><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></p>
        
        <div style="margin-bottom: 20px;">
            <strong>Instruksi:</strong>
            <p style="margin-top: 5px; white-space: pre-line;"><?= htmlspecialchars($data['instruksi']) ?></p>
        </div>

        <div>
            <a href="edit_tugas.php?id=<?= $id ?>" class="btn btn-primary">Edit data</a>
            <a href="daftar_tugas.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

</body>
</html>