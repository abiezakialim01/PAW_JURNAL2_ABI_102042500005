CREATE DATABASE IF NOT EXISTS db_lms;
USE db_lms;

CREATE TABLE mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
);

CREATE TABLE materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    FOREIGN KEY(mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

CREATE TABLE tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY(mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

-- Data Awal (Wajib minimal 2 data per tabel)
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
('PAW-101', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3),
('ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1);

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
(1, 'Dasar PHP', 'Membahas sintaks dasar PHP dan fungsi dasar.', 'https://www.php.net'),
(2, 'Konsep Algoritma', 'Membahas logika pemrograman dasar.', 'https://www.geeksforgeeks.org');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(1, 'Latihan CRUD', 'Buat fungsi CRUD sederhana tanpa framework.', '2026-11-01', 'Aktif'),
(2, 'Flowchart Dasar', 'Buat flowchart untuk algoritma pencarian.', '2026-11-05', 'Draf');