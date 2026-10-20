<?php
require 'koneksi.php';

$id = filter_var($_REQUEST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_tugas.php");
    exit;
}

$stmt = $conn->prepare("SELECT judul FROM tugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: daftar_tugas.php");
    exit;
}

$data = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt_del = $conn->prepare("DELETE FROM tugas WHERE id = ?");
    $stmt_del->bind_param("i", $id);
    if ($stmt_del->execute()) {
        header("Location: daftar_tugas.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Tugas</h1>

        <h3>Hapus <?= htmlspecialchars($data['judul']) ?>?</h3>
        <p style="margin-bottom: 20px; color: #64748b;">Data baru dihapus setelah tombol konfirmasi dikirim.</p>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Ya, hapus data</button>
            <a href="daftar_tugas.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

</body>
</html>