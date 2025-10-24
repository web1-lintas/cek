<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../db/koneksi.php';
require_once __DIR__ . '/../../src/Shapefile/ShapefileAutoloader.php';
Shapefile\ShapefileAutoloader::register();
use Shapefile\ShapefileReader;
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Sesi pengguna berakhir. Silakan login kembali."]);
    exit;
}
$username = $_SESSION['user']['username'] ?? 'unknown';
$kelas = $_POST['kelas'] ?? '';
$subkelas = $_POST['subkelas'] ?? '';
if (!$kelas || !$subkelas) {
    echo json_encode(["status" => "error", "message" => "Kelas dan subkelas wajib diisi."]);
    exit;
}

if (empty($_FILES['shapefile_zip']['tmp_name'])) {
    echo json_encode(["status" => "error", "message" => "File ZIP belum diunggah."]);
    exit;
}
$tmpDir = __DIR__ . "/../temp_uploads/";
if (!file_exists($tmpDir)) mkdir($tmpDir, 0777, true);
$zipPath = $tmpDir . basename($_FILES['shapefile_zip']['name']);
move_uploaded_file($_FILES['shapefile_zip']['tmp_name'], $zipPath);
$extractDir = $tmpDir . uniqid("shp_") . "/";
mkdir($extractDir, 0777, true);
$zip = new ZipArchive();
if ($zip->open($zipPath) === TRUE) {
    $zip->extractTo($extractDir);
    $zip->close();
    unlink($zipPath);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal mengekstrak file ZIP."]);
    exit;
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($extractDir)
);
$shpFiles = [];
foreach ($iterator as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'shp') {
        $shpFiles[] = $file->getPathname();
    }
}
if (empty($shpFiles)) {
    echo json_encode(["status" => "error", "message" => "File .shp tidak ditemukan di dalam ZIP."]);
    exit;
}
$shpPath = $shpFiles[0];
try {
    $reader = new ShapefileReader($shpPath);
    $count = 0;
    $geomTypes = [];

    $db = $conn ?? $mysqli ?? null;
    if (!$db) {
        echo json_encode(["status" => "error", "message" => "Koneksi database tidak ditemukan."]);
        exit;
    }
    $tabel = $db->real_escape_string($subkelas);
    while ($record = $reader->fetchRecord()) {
        if ($record->isDeleted()) continue;
        $attributes = $record->getDataArray();
        $geomWKT = $record->getWKT();
        if (preg_match('/^([A-Z]+)/', $geomWKT, $matches)) {
            $geomType = strtoupper($matches[1]);
        } else {
            $geomType = "UNKNOWN";
        }
        $geomTypes[$geomType] = true;
        $allowed = ["POINT", "LINESTRING", "POLYGON", "MULTILINESTRING", "MULTIPOLYGON"];
        if (!in_array($geomType, $allowed)) continue;
        $colsDB = [];
        $res = $db->query("SHOW COLUMNS FROM `$tabel`");
        while ($r = $res->fetch_assoc()) {
            $colsDB[] = strtolower($r['Field']);
        }
        $cols = [];
        $vals = [];
        foreach ($attributes as $k => $v) {
            $kLower = strtolower($k);
            if (in_array($kLower, $colsDB)) {
                $cols[] = "`$kLower`";
                $vals[] = "'" . $db->real_escape_string($v) . "'";
            }
        }
        $cols[] = "geometri";
        $vals[] = "ST_GeomFromText('$geomWKT', 4326)";

        $sql = "INSERT INTO `$tabel` (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
        if ($db->query($sql)) $count++;
    }
    array_map('unlink', glob("$extractDir/*.*"));
    @rmdir($extractDir);
    $geomList = implode(", ", array_keys($geomTypes));
    $stmt = $db->prepare("INSERT INTO log_upload (username, kelas, subkelas, jumlah_data, jenis_geometri, waktu_upload)
                          VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssis", $username, $kelas, $subkelas, $count, $geomList);
    $stmt->execute();
    echo json_encode([
        "status" => "success",
        "message" => "✅ Berhasil menyimpan $count data ($geomList) ke tabel '$subkelas'."
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Gagal membaca shapefile: " . $e->getMessage()]);
}
?>
