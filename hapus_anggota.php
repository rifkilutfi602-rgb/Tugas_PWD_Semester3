//Rifky Luthfi Hanafi
//250202058
//TI-2B
<?php
// hapus_anggota.php - Menghapus data anggota berdasarkan id (POST)
require_once __DIR__ . "/koneksi.php";

// Halaman ini hanya boleh dibuka melalui pengiriman form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: data_anggota.php");
    exit;
}

$id_no = $_POST["id"] ?? "";

if (!ctype_digit((string)$id_no)) {
    die("ID anggota tidak valid");
}

$sql  = "DELETE FROM anggota WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt, "i", $id_no);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
    header("Location: data_anggota.php");
    exit;
} else {
    die("Gagal menghapus data: " . htmlspecialchars(mysqli_stmt_error($stmt)));
}