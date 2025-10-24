<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include __DIR__ . '/../db/koneksi.php'; 

$subkelas = $_POST['subkelas'] ?? '';
$id = $_POST['id'] ?? '';
$idField = $_POST['id_field'] ?? '';
$nama = trim($_POST['nama_penghapus'] ?? '');
$unit = trim($_POST['unit_penghapus'] ?? '');
$alasan = trim($_POST['alasan_penghapusan'] ?? '');
$captcha_input = strtoupper(trim($_POST['captcha_input'] ?? ''));
$captcha_session = strtoupper($_SESSION['captcha_code'] ?? '');

if (!$subkelas || !$id || !$idField) {
    die("<script>alert('Data tidak lengkap.'); history.back();</script>");
}

if (!$nama || !$alasan) {
    die("<script>alert('Nama, unit, dan alasan wajib diisi.'); history.back();</script>");
}

if ($captcha_input !== $captcha_session) {
    die("<script>alert('Kode verifikasi salah!'); history.back();</script>");
}


$subkelas_safe = mysqli_real_escape_string($conn, $subkelas);
$id_safe = mysqli_real_escape_string($conn, $id);
$idField_safe = mysqli_real_escape_string($conn, $idField);
$cekField = mysqli_query($conn, "SHOW COLUMNS FROM `$subkelas_safe` LIKE '$idField_safe'");
if (mysqli_num_rows($cekField) === 0) {
    die("<script>alert('Kolom ID tidak ditemukan di tabel $subkelas_safe'); history.back();</script>");
}
$query = "DELETE FROM `$subkelas_safe` WHERE `$idField_safe` = '$id_safe'";
if (mysqli_query($conn, $query)) {
    $nama_safe = mysqli_real_escape_string($conn, $nama);
    $unit_safe = mysqli_real_escape_string($conn, $unit);
    $alasan_safe = mysqli_real_escape_string($conn, $alasan);

    $log_query = "
        INSERT INTO log_penghapusan (subkelas, id_data, nama_penghapus, unit_penghapus, alasan_penghapusan)
        VALUES ('$subkelas_safe', '$id_safe', '$nama_safe', '$unit_safe', '$alasan_safe')
    ";
    mysqli_query($conn, $log_query);

    echo "<script>
        alert('✅ Data berhasil dihapus oleh $nama. \\nBidang: $unit. \\nAlasan: $alasan');
        window.location.href='update_data.php?subkelas=$subkelas_safe';
    </script>";
} else {
    echo "<script>alert('❌ Gagal menghapus data: " . mysqli_error($conn) . "'); history.back();</script>";
}
?>
