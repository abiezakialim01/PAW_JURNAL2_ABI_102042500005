<?php
require 'koneksi.php';

$error = '';
$mata_kuliah_id = '';
$judul = '';
$instruksi = '';
$tenggat = '';
$status = 'Draf';

$stmt_mk = $conn->prepare("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama ASC");
$stmt_mk->execute();
$list_mk = $stmt_mk->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah_id = filter_var($_POST['mata_kuliah_id'] ?? '', FILTER_VALIDATE_INT);
    $judul = trim($_POST['judul'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $tenggat = trim($_POST['tenggat'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $allowed_status = ['Draf', 'Aktif', 'Selesai'];

    if (!$mata_kuliah_id) {
        $error = "Pilih mata kuliah yang valid.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $error = "Judul tugas harus 3-150 karakter.";
    } elseif (strlen($instruksi) < 3 || strlen($instruksi) > 2000) {
        $error = "Instruksi tugas harus 3-2000 karakter.";
    } elseif (empty($tenggat)) {
        $error = "Pilih tanggal tenggat yang valid.";
    } elseif (!in_array($status, $allowed_status)) {
        $error = "Status harus Draf, Aktif, atau Selesai.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $mata_kuliah_id, $judul, $instruksi, $tenggat, $status);
        
        if ($stmt->execute()) {
            header("Location: daftar_tugas.php");
            exit;
        } else {
            $error = "Gagal menyimpan data tugas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Tambah Tugas</h1>

        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Mata kuliah</label>
                <select name="mata_kuliah_id" class="form-control" required>
                    <option value="">Pilih Mata kuliah</option>
                    <?php while ($mk = $list_mk->fetch_assoc()): ?>
                        <option value="<?= $mk['id'] ?>" <?= $mata_kuliah_id == $mk['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mk['kode']) ?> - <?= htmlspecialchars($mk['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Judul tugas</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($judul) ?>" required>
            </div>
            <div class="form-group">
                <label>Instruksi tugas</label>
                <textarea name="instruksi" class="form-control" rows="5" required><?= htmlspecialchars($instruksi) ?></textarea>
            </div>
            <div class="form-group">
                <label>Tenggat</label>
                <input type="date" name="tenggat" class="form-control" value="<?= htmlspecialchars($tenggat) ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Draf" <?= $status === 'Draf' ? 'selected' : '' ?>>Draf</option>
                    <option value="Aktif" <?= $status === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Selesai" <?= $status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan data</button>
            <a href="daftar_tugas.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

</body>
</html>