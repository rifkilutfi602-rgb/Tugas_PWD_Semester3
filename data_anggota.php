<?php
require_once __DIR__ . "/koneksi.php";

if (!isset($koneksi)) {
    die("Koneksi database belum tersedia.");
}

$sql = "SELECT * FROM anggota";
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die("Data gagal ditampilkan: " . mysqli_error($koneksi));
}

$jumlah_data = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota Perpustakaan</title>
</head>
<body>
    <h1>Data Anggota Perpustakaan</h1>

    <p>Jumlah Data: <?php echo $jumlah_data; ?></p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No.</th>
            <th>No. Anggota</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Jenis Anggota</th>
            <th>Alamat</th>
        </tr>

        <?php
        $no = 1;
        while ($baris = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($baris["no_anggota"]); ?></td>
            <td><?php echo htmlspecialchars($baris["nama_lengkap"]); ?></td>
            <td><?php echo htmlspecialchars($baris["email"]); ?></td>
            <td><?php echo htmlspecialchars($baris["no_hp"]); ?></td>
            <td><?php echo htmlspecialchars($baris["jenis_anggota"]); ?></td>
            <td><?php echo htmlspecialchars($baris["alamat"]); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>