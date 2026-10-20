<?php
require 'koneksi.php';

$search = trim($_GET['search'] ?? '');
$filter_mk = filter_var($_GET['filter_mk'] ?? '', FILTER_VALIDATE_INT);

// Ambil daftar mata kuliah untuk filter dropdown
$stmt_list_mk = $conn->prepare("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama ASC");
$stmt_list_mk->execute();
$list_mk = $stmt_list_mk->get_result();

// Query pencarian materi
$query = "SELECT m.id, m.judul, mk.kode, mk.nama AS nama_mk 
          FROM materi m 
          JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id 
          WHERE 1=1";

$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND m.judul LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}
if ($filter_mk) {
    $query .= " AND m.mata_kuliah_id = ?";
    $params[] = $filter_mk;
    $types .= "i";
}
$query .= " ORDER BY m.id DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Materi</h1>

    <div class="search-bar">
        <a href="index.php" class="btn btn-secondary">← Beranda</a>
        <a href="tambah_materi.php" class="btn btn-primary">+ Tambah Materi</a>
        
        <form method="GET" action="" style="display: flex; gap: 10px; margin-left: auto; align-items: center;">
            <select name="filter_mk" class="form-control">
                <option value="">Semua mata kuliah</option>
                <?php while ($mk = $list_mk->fetch_assoc()): ?>
                    <option value="<?= $mk['id'] ?>" <?= $filter_mk == $mk['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mk['kode']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="search" class="form-control" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <?php if ($search !== '' || $filter_mk): ?>
                <a href="daftar_materi.php" class="btn btn-danger">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($search !== '' || $filter_mk): ?>
        <p style="margin-bottom: 10px; color: #64748b;">Menampilkan <?= $result->num_rows ?> data.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Mata kuliah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['kode']) ?> - <?= htmlspecialchars($row['nama_mk']) ?></td>
                        <td>
                            <a href="detail_materi.php?id=<?= $row['id'] ?>" class="action-link">Detail</a>
                            <a href="edit_materi.php?id=<?= $row['id'] ?>" class="action-link">Edit</a>
                            <a href="hapus_materi.php?id=<?= $row['id'] ?>" class="action-link" style="color: #dc2626;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #dc2626; font-weight: bold;">
                        Data tidak ditemukan.<br>
                        <?php if ($search !== '' || $filter_mk): ?>
                            <a href="daftar_materi.php" class="btn btn-secondary" style="margin-top: 10px;">Reset Pencarian</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>