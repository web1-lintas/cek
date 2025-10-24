<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/../logs/php_error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!function_exists('json_response')) {
    function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

include_once(__DIR__ . "/../db/koneksi.php");
$subkelas_param = isset($_GET['subkelas']) ? strtolower(trim($_GET['subkelas'])) : null;

function getStyles()
{
    $basePath = "/assets";
    return [
        "atribut_jalan" => [
            "cermin_jalan" => [
                "type" => "point",
                "marker" => "$basePath/cermin_jalan.png",
            ],
            "lampu_jalan" => [
                "type" => "point",
                "marker" => "$basePath/lampu_jalan.png",
            ],
            "lampu_lalu_lintas" => [
                "type" => "point",
                "marker" => "$basePath/lampu_lalin.png",
            ],
            "kamera_pengawas" => [
                "type" => "point",
                "marker" => "$basePath/kamera_pengawas.png",
            ],
            "rambu_lalu_lintas" => [
                "type" => "point",
                "marker" => "$basePath/rambu_lalin.png",
            ],
        ],
        "bangunan" => [
            "bangunan" => [
                "type"      => "polygon",
                "color"     => "rgba(255, 116, 92, 0.8)",
                "fillColor" => "rgba(255,140,66,0.35)",
                "fillOpacity" => 0.35,
                "weight"    => 1.5
            ]
        ],
        "halte" => [
            "titik_halte" => [
                "type"  => "point",
                "marker" => "$basePath/titik_halte.png",
            ],
            "jalur_halte_trans" => [
                "type"  => "multilinestring",
                "color" => "rgba(251, 114, 29, 0.8)",
                "weight" => 2.5,
                "opacity" => 0.85,
                "dashArray" => "6 3",
            ]
        ],
        "jaringan_drainase" => [
            "inlet" => [
                "type" => "point",
                "marker" => "$basePath/inlet.png",
            ],
            "manhole_drainase" => [
                "type" => "point",
                "marker" => "$basePath/manhole_drainase.png",
            ],
            "sumur_resapan" => [
                "type" => "point",
                "marker" => "$basePath/sumur_resapan.png",
            ],
            "zona_drainase" => [
                "type"   => "line",
                "color"  => "#195dfeff",
                "weight" => 1.5
            ],
        ],
        "jaringan_fiber_optik" => [
            "kabel_fiber_optik" => [
                "type"   => "line",
                "color" => "#000000ff",
                "weight" => 2,
                "opacity" => 0.9
            ],
            "manhole_fiber_optik" => [
                "type" => "point",
                "marker" => "$basePath/manhole_fo.png",
            ],
            "tiang_fiber_optik" => [
                "type" => "point",
                "marker" => "$basePath/tiang_fo.png",
            ],
        ],
    ];
}

function normalizeKey($str)
{
    return strtolower(str_replace(" ", "_", trim($str)));
}

function getStyle($kelas, $subkelas)
{
    $styles = getStyles();
    $kelas = normalizeKey($kelas);
    $subkelas = $subkelas ? normalizeKey($subkelas) : $kelas;
    $styleData = $styles[$kelas][$subkelas] ?? null;
    if (!$styleData) {
        error_log("❌ Style tidak ditemukan: [$kelas][$subkelas]");
        return ["color" => "#444", "weight" => 1];
    }
    if (isset($styleData['styles']) && is_array($styleData['styles'])) {
        $first = $styleData['styles'][0];
        $first['type'] = $styleData['type'] ?? "line";
        return $first;
    }
    return $styleData;
}

function getUtilitas($conn, $table, $fields, $kelas)
{
    $cols = implode(", ", $fields);
    $sql = "SELECT $cols, ST_AsGeoJSON(geometri) AS geojson FROM $table";
    $result = $conn->query($sql);
    $features = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $subkelas = strtolower(trim($row['subkelas'] ?? 'lainnya'));
            $style = getStyle($kelas, $subkelas);

            $geometry = null;
            if (!empty($row['geojson']) && is_string($row['geojson'])) {
                $geometry = json_decode($row['geojson']);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("⚠️ Gagal decode GeoJSON ($table): " . json_last_error_msg());
                    $geometry = null;
                }
            }
            unset($row['geojson']);
            if ($geometry && isset($geometry->type)) {
                if ($geometry->type === "GeometryCollection") {
                    if (
                        !isset($geometry->geometries) ||
                        !is_array($geometry->geometries) ||
                        count($geometry->geometries) === 0
                    ) {
                        continue;
                    }
                }
                $allowedTypes = ["Point", "MultiPoint", "LineString", "MultiLineString", "Polygon", "MultiPolygon", "GeometryCollection"];
                if (!in_array($geometry->type, $allowedTypes)) {
                    error_log("⚠️ Geometry type tidak dikenal ($table): " . $geometry->type);
                    continue;
                }
                $features[] = [
                    "type" => "Feature",
                    "geometry" => $geometry,
                    "properties" => $row,
                    "style" => $style
                ];
            }
        }
    }
    return [
        "type" => "FeatureCollection",
        "features" => $features
    ];
}

function gabungPerKelas($subcollections)
{
    $allFeatures = [];
    foreach ($subcollections as $fc) {
        if (!empty($fc['features'])) {
            $allFeatures = array_merge($allFeatures, $fc['features']);
        }
    }
    return [
        "type" => "FeatureCollection",
        "features" => $allFeatures
    ];
}

$response = [
    //atribut jalan
    "cermin_jalan" => getUtilitas($conn, "cermin_jalan", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "kamera_pengawas" => getUtilitas($conn, "kamera_pengawas", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "lampu_jalan" => getUtilitas($conn, "lampu_jalan", ["id", "kelas", "subkelas", "kode", "kondisi", "pondasi", "daya_lampu", "teknologi"], "atribut_jalan"),
    "lampu_lalin" => getUtilitas($conn, "lampu_lalin", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "rambu_lalin" => getUtilitas($conn, "rambu_lalin", ["id", "kelas", "subkelas"], "atribut_jalan"),
    //bangunan
    "bangunan" => getUtilitas($conn, "bangunan", ["id", "kelas", "subkelas", "kelurahan", "kemantren"], "bangunan"),
    //halte
    "titik_halte" => getUtilitas($conn, "titik_halte", ["id", "kelas", "subkelas", "nama"], "halte"),
    "jalur_halte" => getUtilitas($conn, "jalur_halte", ["id", "kelas", "subkelas", "nama"], "halte"),
    //jaringan drainase
    "inlet" => getUtilitas($conn, "inlet", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "manhole_drainase" => getUtilitas($conn, "manhole_drainase", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "sumur_resapan" => getUtilitas($conn, "sumur_resapan", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "zona_drainase" => getUtilitas($conn, "zona_drainase", ["id", "kelas", "subkelas", "fungsi", "arah", "tipe", "kondisi", "dimensi", "zona"], "jaringan_drainase"),
    //jaringan fo
    "kabel_fiber_optik" => getUtilitas($conn, "kabel_fo", ["id", "kelas", "subkelas", "provider"], "jaringan_fiber_optik"),
    "manhole_fiber_optik" => getUtilitas($conn, "manhole_fo", ["id", "kelas", "subkelas"], "jaringan_fiber_optik"),
    "tiang_fiber_optik" => getUtilitas($conn, "tiang_fo", ["id", "kelas", "subkelas", "provider"], "jaringan_fiber_optik"),

];
if ($subkelas_param) {
    foreach ($response as $layer => $fc) {
        foreach ($fc['features'] as $feat) {
            if (
                isset($feat['properties']['subkelas']) &&
                strtolower($feat['properties']['subkelas']) === $subkelas_param
            ) {
                $filtered[] = $feat;
            }
        }
    }

    json_response([
        "type" => "FeatureCollection",
        "features" => $filtered ?? []
    ]);
}

json_response($response);


