<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . "/../db/koneksi.php");

$sql = "SELECT id_kel, luas_kel, kelurahan, kemantren, kota, provinsi, negara, ST_AsGeoJSON(geometri) AS geojson 
        FROM batas_administrasi";
$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["error" => "Query gagal: " . $conn->error]);
    exit;
}
$features = [];
while ($row = $result->fetch_assoc()) {
    $geometry = json_decode($row['geojson']);
    unset($row['geojson']);
    $features[] = [
        "type" => "Feature",
        "geometry" => $geometry,
        "properties" => $row
    ];
}
echo json_encode([
    "type" => "FeatureCollection",
    "features" => $features
], JSON_UNESCAPED_UNICODE);

?>
