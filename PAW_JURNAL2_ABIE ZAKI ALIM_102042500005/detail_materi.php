<?php
require 'koneksi.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_materi.php");
    exit;
}

$query = "SELECT m.id, m.judul, m.isi, m.tautan, mk.kode, mk.nama AS nama_mk 
          FROM materi m 
          JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id 
          WHERE m.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    header("Location: daftar_materi.php");
    exit;
}

$data = $res->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Materi</h1>
        <h3><?= htmlspecialchars($data['judul']) ?></h3>
        <p style="margin-bottom: 15px;"><strong>Mata kuliah:</strong> <?= htmlspecialchars($data['kode']) ?> - <?= htmlspecialchars($data['nama_mk']) ?></p>
        
        <div style="margin-bottom: 15px;">
            <strong>Isi materi:</strong>
            <p style="margin-top: 5px; white-space: pre-line;"><?= htmlspecialchars($data['isi']) ?></p>
        </div>

        <p style="margin-bottom: 20px;">
            <strong>Tautan referensi:</strong><br>
            <a href="<?= htmlspecialchars($data['tautan']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($data['tautan']) ?></a>
        </p>

        <div>
            <a href="edit_materi.php?id=<?= $id ?>" class="btn btn-primary">Edit data</a>
            <a href="daftar_materi.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

</body>
</html>