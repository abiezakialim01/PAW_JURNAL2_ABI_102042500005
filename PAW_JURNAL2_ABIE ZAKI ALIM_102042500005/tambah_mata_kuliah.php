<?php
require 'koneksi.php';

$error = '';
$kode = '';
$nama = '';
$dosen = '';
$semester = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $dosen = trim($_POST['dosen'] ?? '');
    $semester = filter_var($_POST['semester'] ?? '', FILTER_VALIDATE_INT);

    // Validasi input sesuai kriteria jurnal
    if (!preg_match('/^[A-Z0-9-]{3,20}$/', $kode)) {
        $error = "Kode harus 3-20 karakter (huruf kapital, angka, atau tanda -).";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $error = "Nama mata kuliah harus 3-100 karakter.";
    } elseif (strlen($dosen) < 3 || strlen($dosen) > 100) {
        $error = "Nama dosen harus 3-100 karakter.";
    } elseif ($semester === false || $semester < 1 || $semester > 14) {
        $error = "Semester harus berupa angka antara 1 sampai 14.";
    } else {
        // Cek apakah kode sudah digunakan (UNIQUE)
        $stmt = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Kode mata kuliah sudah digunakan.";
        } else {
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $kode, $nama, $dosen, $semester);
            
            if ($stmt->execute()) {
                header("Location: daftar_mata_kuliah.php");
                exit;
            } else {
                $error = "Gagal menyimpan data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Tambah Mata Kuliah</h1>

        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Kode Mata Kuliah</label>
                <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($kode) ?>" required placeholder="Contoh: PAW-101">
            </div>
            <div class="form-group">
                <label>Nama Mata Kuliah</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required placeholder="Contoh: Pengembangan Aplikasi Website">
            </div>
            <div class="form-group">
                <label>Dosen Pengampu</label>
                <input type="text" name="dosen" class="form-control" value="<?= htmlspecialchars($dosen) ?>" required placeholder="Contoh: Dosen PAW">
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" class="form-control" value="<?= htmlspecialchars($semester) ?>" required min="1" max="14">
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan data</button>
            <a href="daftar_mata_kuliah.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

</body>
</html>