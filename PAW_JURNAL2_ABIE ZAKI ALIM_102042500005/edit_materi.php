<?php
require 'koneksi.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: daftar_materi.php");
    exit;
}

$stmt = $conn->prepare("SELECT mata_kuliah_id, judul, isi, tautan FROM materi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    header("Location: daftar_materi.php");
    exit;
}

$current = $res->fetch_assoc();
$stmt->close();

$stmt_mk = $conn->prepare("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama ASC");
$stmt_mk->execute();
$list_mk = $stmt_mk->get_result();

$error = '';
$info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_mk_id = filter_var($_POST['mata_kuliah_id'] ?? '', FILTER_VALIDATE_INT);
    $new_judul = trim($_POST['judul'] ?? '');
    $new_isi = trim($_POST['isi'] ?? '');
    $new_tautan = trim($_POST['tautan'] ?? '');

    $valid_link = preg_match('/^https?:\/\/.+$/i', $new_tautan);

    if ($new_mk_id == $current['mata_kuliah_id'] && $new_judul === $current['judul'] && $new_isi === $current['isi'] && $new_tautan === $current['tautan']) {
        $info = "Tidak ada perubahan data.";
    } elseif (!$new_mk_id) {
        $error = "Pilih mata kuliah yang valid.";
    } elseif (strlen($new_judul) < 3 || strlen($new_judul) > 150) {
        $error = "Judul materi harus 3-150 karakter.";
    } elseif (strlen($new_isi) < 3 || strlen($new_isi) > 2000) {
        $error = "Isi materi harus 3-2000 karakter.";
    } elseif (strlen($new_tautan) > 255 || !$valid_link) {
        $error = "Gunakan tautan http/https yang valid.";
    } else {
        $stmt_update = $conn->prepare("UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?");
        $stmt_update->bind_param("isssi", $new_mk_id, $new_judul, $new_isi, $new_tautan, $id);
        
        if ($stmt_update->execute()) {
            $stmt_update->close();
            header("Location: daftar_materi.php");
            exit;
        } else {
            $error = "Gagal memperbarui materi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-box">
        <h1>Edit Materi</h1>

        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="alert-info"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Mata kuliah</label>
                <select name="mata_kuliah_id" class="form-control" required>
                    <?php while ($mk = $list_mk->fetch_assoc()): ?>
                        <?php $selected = ($mk['id'] == ($new_mk_id ?? $current['mata_kuliah_id'])) ? 'selected' : ''; ?>
                        <option value="<?= $mk['id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($mk['kode']) ?> - <?= htmlspecialchars($mk['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Judul materi</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($_POST['judul'] ?? $current['judul']) ?>" required>
            </div>
            <div class="form-group">
                <label>Isi materi</label>
                <textarea name="isi" class="form-control" rows="5" required><?= htmlspecialchars($_POST['isi'] ?? $current['isi']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Tautan referensi</label>
                <input type="text" name="tautan" class="form-control" value="<?= htmlspecialchars($_POST['tautan'] ?? $current['tautan']) ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="daftar_materi.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

</body>
</html>