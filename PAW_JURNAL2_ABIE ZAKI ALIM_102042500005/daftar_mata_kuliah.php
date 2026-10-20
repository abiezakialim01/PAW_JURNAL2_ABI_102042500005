<?php
require 'koneksi.php';

$search = trim($_GET['search'] ?? '');

$query = "SELECT id, kode, nama, dosen, semester FROM mata_kuliah";
if ($search !== '') {
    $query .= " WHERE kode LIKE ? OR nama LIKE ?";
}
$query .= " ORDER BY nama ASC";

$stmt = $conn->prepare($query);
if ($search !== '') {
    $param = "%$search%";
    $stmt->bind_param("ss", $param, $param);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Daftar Mata Kuliah</h1>

    <div class="search-bar">
        <a href="index.php" class="btn btn-secondary">← Beranda</a>
        <a href="tambah_mata_kuliah.php" class="btn btn-primary">+ Tambah Mata Kuliah</a>
        <form method="GET" action="" style="display: flex; gap: 10px; margin-left: auto;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <?php if ($search !== ''): ?>
                <a href="daftar_mata_kuliah.php" class="btn btn-danger">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($search !== ''): ?>
        <p style="margin-bottom: 10px; color: #64748b;">Menampilkan <?= $result->num_rows ?> data.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Mata kuliah</th>
                <th>Dosen / Semester</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kode']) ?> - <?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['dosen']) ?> / Semester <?= htmlspecialchars($row['semester']) ?></td>
                        <td>
                            <a href="detail_mata_kuliah.php?id=<?= $row['id'] ?>" class="action-link">Detail</a>
                            <a href="edit_mata_kuliah.php?id=<?= $row['id'] ?>" class="action-link">Edit</a>
                            <a href="hapus_mata_kuliah.php?id=<?= $row['id'] ?>" class="action-link" style="color: #dc2626;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #dc2626; font-weight: bold;">
                        Data tidak ditemukan.<br>
                        <?php if ($search !== ''): ?>
                            <a href="daftar_mata_kuliah.php" class="btn btn-secondary" style="margin-top: 10px;">Reset Pencarian</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>