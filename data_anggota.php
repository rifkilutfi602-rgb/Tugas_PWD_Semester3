<?php
require_once __DIR__ . "/koneksi.php";

if (!isset($koneksi)) {
    die("Koneksi database belum tersedia.");
}

$sql = "SELECT * FROM anggota ORDER BY nama_lengkap ASC";
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die("Data gagal ditampilkan: " . htmlspecialchars(mysqli_error($koneksi)));
}

$jumlah_data = mysqli_num_rows($result);

// Kelas warna badge sesuai jenis anggota
function kelas_badge($jenis)
{
    switch ($jenis) {
        case "Mahasiswa":
            return "badge-mhs";
        case "Dosen":
            return "badge-dsn";
        case "Tenaga Kependidikan":
            return "badge-tendik";
        default:
            return "badge-lain";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Anggota Perpustakaan</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 32px 16px;
        background: #f3f6fb;
        color: #553dab;
    }
    .container { max-width: 1100px; margin: 0 auto; }

    .header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    h1 { margin: 0; color: #1f3864; font-size: 26px; }
    .subtitle { margin: 4px 0 0; color: #607d8b; font-size: 14px; }

    .btn {
        display: inline-block;
        padding: 10px 18px;
        background: #2f73e9;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: bold;
    }
    .btn:hover { background: #2c4f8f; }

    .ringkasan {
        display: inline-block;
        background: #fff;
        border-left: 5px solid #1f3864;
        padding: 10px 18px;
        margin-bottom: 16px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        font-size: 14px;
    }
    .ringkasan strong { font-size: 20px; color: #1f3864; margin-left: 6px; }

    .tabel-wrap {
        overflow-x: auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    table { width: 100%; border-collapse: collapse; min-width: 760px; }
    thead th {
        background: #008e40;
        color: #fff;
        text-align: left;
        padding: 12px 14px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #e8edf3;
        font-size: 14px;
        vertical-align: top;
    }
    tbody tr:nth-child(even) { background: #f8fafd; }
    tbody tr:hover { background: #e9f0fb; }
    td.no { width: 48px; color: #78909c; text-align: center; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }
    .badge-mhs { background: #e3f2fd; color: #1565c0; }
    .badge-dsn { background: #e6f4ea; color: #2e7d32; }
    .badge-tendik { background: #fff4e0; color: #ef6c00; }
    .badge-lain { background: #eceff1; color: #546e7a; }

    .btn-hapus {
        padding: 6px 12px;
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-hapus:hover { background: #962d22; }

    .kosong { text-align: center; padding: 40px 16px; color: #78909c; }
</style>
</head>
<body>
<div class="container">

    <div class="header">
        <div>
            <h1>Data Anggota Perpustakaan</h1>
            <p class="subtitle">Daftar anggota yang sudah terdaftar</p>
        </div>
        <a class="btn" href="form_anggota.php">+ Tambah Anggota</a>
    </div>

    <div class="ringkasan">Jumlah Data: <strong><?php echo $jumlah_data; ?></strong></div>

    <div class="tabel-wrap">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Jenis Anggota</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($jumlah_data === 0) { ?>
                <tr>
                    <td colspan="8" class="kosong">Belum ada data anggota.</td>
                </tr>
            <?php } else {
                $no = 1;
                while ($baris = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td class="no"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($baris["no_anggota"]); ?></td>
                    <td><?php echo htmlspecialchars($baris["nama_lengkap"]); ?></td>
                    <td><?php echo htmlspecialchars($baris["email"]); ?></td>
                    <td><?php echo htmlspecialchars($baris["no_hp"]); ?></td>
                    <td>
                        <span class="badge <?php echo kelas_badge($baris["jenis_anggota"]); ?>">
                            <?php echo htmlspecialchars($baris["jenis_anggota"]); ?>
                        </span>
                    </td>
                    <td><?php echo nl2br(htmlspecialchars($baris["alamat"])); ?></td>
                    <td>
                        <a class="btn" href="edit_anggota.php?id=<?php echo (int)$baris['id']; ?>">Edit</a>
                        <form action="hapus_anggota.php" method="POST" style="display:inline;"
                              onsubmit="return confirm('Anda yakin mau hapus?');">
                            <input type="hidden" name="id" value="<?php echo (int)$baris['id']; ?>">
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php }
            } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
<?php mysqli_close($koneksi); ?>