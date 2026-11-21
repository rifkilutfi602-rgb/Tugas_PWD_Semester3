<?php
// proses_anggota.php - Memvalidasi data form, lalu menyimpannya dengan INSERT

// Halaman ini hanya boleh dibuka melalui pengiriman form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: form_anggota.php");
    exit;
}

// 1. Ambil data dan bersihkan spasi di awal/akhir
$no_anggota    = trim($_POST["no_anggota"]    ?? "");
$nama_lengkap  = trim($_POST["nama_lengkap"]  ?? "");
$email         = trim($_POST["email"]         ?? "");
$no_hp         = trim($_POST["no_hp"]         ?? "");
$jenis_anggota = trim($_POST["jenis_anggota"] ?? "");
$alamat        = trim($_POST["alamat"]        ?? "");

// 2. Validasi di sisi server
$daftar_jenis = array("Mahasiswa", "Dosen", "Tenaga Kependidikan");
$errors = array();

// Nomor anggota: wajib, hanya angka, maksimal 15 digit
if ($no_anggota === "") {
    $errors[] = "Nomor anggota tidak boleh kosong.";
} elseif (!ctype_digit($no_anggota)) {
    $errors[] = "Nomor anggota hanya boleh berisi angka.";
} elseif (strlen($no_anggota) > 15) {
    $errors[] = "Nomor anggota maksimal 15 digit.";
}

// Nama lengkap: wajib, maksimal 100 karakter
if ($nama_lengkap === "") {
    $errors[] = "Nama lengkap tidak boleh kosong.";
} elseif (strlen($nama_lengkap) > 100) {
    $errors[] = "Nama lengkap maksimal 100 karakter.";
}

// Email: wajib, format valid, maksimal 100 karakter
if ($email === "") {
    $errors[] = "Email tidak boleh kosong.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
} elseif (strlen($email) > 100) {
    $errors[] = "Email maksimal 100 karakter.";
}

// Nomor HP: wajib, hanya angka, 10 sampai 13 digit
if ($no_hp === "") {
    $errors[] = "Nomor HP tidak boleh kosong.";
} elseif (!ctype_digit($no_hp)) {
    $errors[] = "Nomor HP hanya boleh berisi angka.";
} elseif (strlen($no_hp) < 10 || strlen($no_hp) > 13) {
    $errors[] = "Nomor HP harus 10 sampai 13 digit.";
}

// Jenis anggota: wajib dipilih, harus salah satu pilihan yang sah
if ($jenis_anggota === "") {
    $errors[] = "Jenis anggota wajib dipilih.";
} elseif (!in_array($jenis_anggota, $daftar_jenis, true)) {
    $errors[] = "Jenis anggota tidak valid.";
}

// Alamat: wajib
if ($alamat === "") {
    $errors[] = "Alamat tidak boleh kosong.";
}

// 3. Bila semua data valid, simpan ke database
$berhasil = false;
$pesan_db = "";

if (empty($errors)) {
    require_once __DIR__ . "/koneksi.php";

    $sql = "INSERT INTO anggota (no_anggota, nama_lengkap, email, no_hp, jenis_anggota, alamat) "
         . "VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt, "ssssss",
            $no_anggota, $nama_lengkap, $email, $no_hp, $jenis_anggota, $alamat
        );

        if (mysqli_stmt_execute($stmt)) {
            $berhasil = true;
        } else {
            $pesan_db = mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $pesan_db = mysqli_error($koneksi);
    }
    mysqli_close($koneksi);

    // 4. Bila berhasil, kembali ke form dengan notifikasi
    if ($berhasil) {
        header("Location: form_anggota.php?status=sukses");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Penyimpanan Data</title>
<style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    h1 { color: #1f3864; }
    .gagal { background: #fdecea; border-left: 5px solid #c0392b;
             padding: 10px; }
</style>
</head>
<body>
<?php if (!empty($errors)) { ?>

    <h1>Data Tidak Valid</h1>
    <p class="gagal">Data belum dapat disimpan. Perbaiki kesalahan:</p>
    <ul>
    <?php foreach ($errors as $pesan) { ?>
        <li><?php echo htmlspecialchars($pesan); ?></li>
    <?php } ?>
    </ul>

<?php } else { ?>

    <h1>Data Gagal Disimpan</h1>
    <p class="gagal">Terjadi kesalahan saat menyimpan data ke database.</p>
    <p>Pesan dari MySQL: <?php echo htmlspecialchars($pesan_db); ?></p>

<?php } ?>

<p><a href="javascript:history.back()">Kembali dan perbaiki data</a></p>
</body>
</html>