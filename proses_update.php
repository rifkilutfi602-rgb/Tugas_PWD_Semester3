<?php

require_once __DIR__ . "/koneksi.php";

// Memeriksa metode pengiriman data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Mengambil data dari form
    $id = $_POST['id'] ?? '';
    $no_anggota = trim($_POST['no_anggota'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $jenis_anggota = trim($_POST['jenis_anggota'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    // Validasi ID
    if (!ctype_digit((string) $id) || (int) $id <= 0) {
        die("ID anggota tidak valid.");
    }

    // Validasi data kosong
    if (
        $no_anggota == "" ||
        $nama_lengkap == "" ||
        $email == "" ||
        $no_hp == "" ||
        $jenis_anggota == "" ||
        $alamat == ""
    ) {
        die("Semua data wajib diisi.");
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid.");
    }

    // Validasi jenis anggota
    $jenis_valid = [
        "Mahasiswa",
        "Dosen",
        "Tenaga Kependidikan"
    ];

    if (!in_array($jenis_anggota, $jenis_valid, true)) {
        die("Jenis anggota tidak valid.");
    }

    // Query UPDATE menggunakan Prepared Statement
    $query = "UPDATE anggota SET
              no_anggota = ?,
              nama_lengkap = ?,
              email = ?,
              no_hp = ?,
              jenis_anggota = ?,
              alamat = ?
              WHERE id = ?";

    // Menyiapkan statement
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {

        // Mengikat parameter
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $no_anggota,
            $nama_lengkap,
            $email,
            $no_hp,
            $jenis_anggota,
            $alamat,
            $id
        );

        // Menjalankan query
        if (mysqli_stmt_execute($stmt)) {

            echo "<script>
                    alert('Data anggota berhasil diperbarui!');
                    window.location.href = 'data_anggota.php';
                  </script>";

        } else {

            echo "<script>
                    alert('Data anggota gagal diperbarui!');
                    window.location.href = 'data_anggota.php';
                  </script>";

        }

        // Menutup statement
        mysqli_stmt_close($stmt);

    } else {

        echo "Gagal menyiapkan query: " . mysqli_error($koneksi);

    }

    // Menutup koneksi database
    mysqli_close($koneksi);

} else {

    // Mengarahkan kembali jika bukan POST
    header("Location: data_anggota.php");
    exit;

}

?>