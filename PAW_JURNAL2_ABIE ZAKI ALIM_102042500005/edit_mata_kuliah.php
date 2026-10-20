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

$current = $res->fetch_assoc();
$error = '';
$info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_kode = trim($_POST['kode'] ?? '');
    $new_nama = trim($_POST['nama'] ?? '');
    $new_dosen = trim($_POST['dosen'] ?? '');
    $new_semester = filter_var($_POST['semester'] ?? '', FILTER_VALIDATE_INT);

    if ($new_kode === $current['kode'] && $new_nama === $current['nama'] && $new_dosen === $current['dosen'] && $new_semester == $current['semester']) {
        $info = "Tidak ada perubahan data.";
    } elseif (!preg_match('/^[A-Z0-9-]{3,20}$/', $new_kode)) {
        $error = "Kode harus 3-20 karakter (huruf kapital, angka, atau tanda -).";
    } elseif (strlen($new_nama) < 3 || strlen($new_nama) > 100) {
        $error = "Nama harus 3-100 karakter.";
    } elseif (strlen($new_dosen) < 3 || strlen($new_dosen) > 100) {
        $error = "Dosen harus 3-100 karakter.";
    } elseif ($new_semester === false || $new_semester < 1 || $new_semester > 14) {
        $error = "Semester antara 1 sampai 14.";
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ? AND id != ?");
        $stmt_check->bind_param("si", $new_kode, $id);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Kode mata kuliah sudah digunakan.";
        } else {
            $stmt_update = $conn->prepare("UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?");
            $stmt_update->bind_param("sssii", $new_kode, $new_nama, $new_dosen, $new_semester, $id);
            if ($stmt_update->execute()) {
                header("Location: daftar_mata_kuliah.php");
                exit;
            } else {
                $error = "Gagal memperbarui data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-box">
        <h1>Edit Mata Kuliah</h1>
        <?php if ($error): ?><div class="alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($info): ?><div class="alert-info"><?= htmlspecialchars($info) ?></div><?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Kode Mata Kuliah</label>
                <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($_POST['kode'] ?? $current['kode']) ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Mata Kuliah</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($_POST['nama'] ?? $current['nama']) ?>" required>
            </div>
            <div class="form-group">
                <label>Dosen Pengampu</label>
                <input type="text" name="dosen" class="form-control" value="<?= htmlspecialchars($_POST['dosen'] ?? $current['dosen']) ?>" required>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" class="form-control" value="<?= htmlspecialchars($_POST['semester'] ?? $current['semester']) ?>" required min="1" max="14">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="daftar_mata_kuliah.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>