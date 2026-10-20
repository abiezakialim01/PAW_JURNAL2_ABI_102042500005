<?php
require 'koneksi.php';

$id = filter_var($_REQUEST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_mata_kuliah.php");
    exit;
}

// 1. Ambil data mata kuliah
$stmt = $conn->prepare("SELECT nama FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    header("Location: daftar_mata_kuliah.php");
    exit;
}
$data = $res->fetch_assoc();
$stmt->close(); // <-- TUTUP STATEMENT PERTAMA DI SINI

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Cek relasi di tabel materi
    $stmt_materi = $conn->prepare("SELECT id FROM materi WHERE mata_kuliah_id = ?");
    $stmt_materi->bind_param("i", $id);
    $stmt_materi->execute();
    $has_materi = $stmt_materi->get_result()->num_rows > 0;
    $stmt_materi->close(); // <-- TUTUP STATEMENT KEDUA DI SINI

    // 3. Cek relasi di tabel tugas
    $stmt_tugas = $conn->prepare("SELECT id FROM tugas WHERE mata_kuliah_id = ?");
    $stmt_tugas->bind_param("i", $id);
    $stmt_tugas->execute();
    $has_tugas = $stmt_tugas->get_result()->num_rows > 0;
    $stmt_tugas->close(); // <-- TUTUP STATEMENT KETIGA DI SINI

    // 4. Validasi Hapus sesuai ketentuan relasi jurnal
    if ($has_materi || $has_tugas) {
        $error = "Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. Hapus data terkait terlebih dahulu.";
    } else {
        $stmt_del = $conn->prepare("DELETE FROM mata_kuliah WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        if ($stmt_del->execute()) {
            $stmt_del->close();
            header("Location: daftar_mata_kuliah.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-box">
        <h1>Mata kuliah</h1>
        <?php if ($error): ?><div class="alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <h3>Hapus <?= htmlspecialchars($data['nama']) ?>?</h3>
        <p style="margin-bottom: 20px; color: #64748b;">Data baru dihapus setelah tombol konfirmasi dikirim.</p>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Ya, hapus data</button>
            <a href="daftar_mata_kuliah.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>