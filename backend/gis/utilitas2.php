<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/../logs/php_error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include_once(__DIR__ . "/../db/koneksi.php");

if (!function_exists('json_response')) {
    function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function normalizeKey($str)
{
    return strtolower(str_replace(" ", "_", trim($str)));
}

function getStyles()
{
    $basePath = "/assets";
    return [
        "infrastruktur_pendukung" => [
            "hidran" => ["type" => "point", "marker" => "$basePath/hidran.png"],
            "rantai_pasok" => ["type" => "point", "marker" => "$basePath/rantai_pasok.png"],
            "reklame" => ["type" => "point", "marker" => "$basePath/reklame.png"],
            "titik_bench_mark" => ["type" => "point", "marker" => "$basePath/titik_bm.png"],
        ],
        "jaringan_ipal" => [
            "manhole_instalasi_pengolahan_air_limbah" => [
                "type" => "point",
                "marker" => "$basePath/manhole_ipal.png",
                "size" => [10, 10]
            ],
            "pipa_glontor" => ["type" => "line", "color" => "#19a880ff", "weight" => 2],
            "pipa_induk" => ["type" => "line", "color" => "#5691a8ff", "weight" => 2],
            "pipa_lateral" => ["type" => "line", "color" => "#028ebdff", "weight" => 2],
        ],
        "jaringan_jalan" => [
            "jalan_kota" => ["type" => "polygon", "color" => "#000000b2", "weight" => 0.8, "opacity" => 0.8, "fillColor" => "#e3e3e3c5", "fillOpacity" => 0.8],
            "jalan_lingkungan" => ["type" => "line", "color" => "#977f56dc", "weight" => 2],
            "jalan_tunanetra" => ["type" => "multipolygon", "color" => "#fbff00dc", "weight" => 2],
            "jembatan" => ["type" => "point", "marker" => "$basePath/jembatan.png", "size" => [20, 20]],
            "trotoar" => ["type" => "polygon", "color" => "grey"],
        ],
        "jaringan_listrik" => [
            "jaringan_kabel_listrik_tegangan_menengah" => ["type" => "line", "color" => "#8d8b08ff", "weight" => 2],
            "jaringan_kabel_listrik_tegangan_rendah" => ["type" => "line", "color" => "#adb162", "weight" => 2],
            "rumah_kabel" => ["type" => "point", "marker" => "$basePath/rumah_kabel.png", "size" => [20, 20]],
            "tiang_listrik" => ["type" => "point", "marker" => "$basePath/tiang_listrik.png", "size" => [20, 20]],
            "trafo_listrik" => ["type" => "point", "marker" => "$basePath/trafo_listrik.png", "size" => [20, 20]],
        ],
        "jaringan_pdam" => [
            "jaringan_pdam" => ["type" => "line", "color" => "rgba(0, 255, 242, 1)", "weight" => 2],
        ],
        "sungai" => [
            "sungai" => ["type" => "line", "color" => "rgba(53, 194, 255, 1)", "weight" => 2],
        ],
    ];
}

function getStyle($kelas, $subkelas)
{
    $styles = getStyles();
    $kelas = normalizeKey($kelas);
    $subkelas = normalizeKey($subkelas);
    return $styles[$kelas][$subkelas] ?? ["color" => "#666", "weight" => 1];
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
    $all = [];
    foreach ($subcollections as $fc) {
        if (!empty($fc['features'])) {
            $all = array_merge($all, $fc['features']);
        }
    }
    return ["type" => "FeatureCollection", "features" => $all];
}

$response = [
    //infrastruktur pendukung
    "hidran" => getUtilitas($conn, "hidran", ["id", "kelas", "subkelas"], "infrastruktur_pendukung"),
    "rantai_pasok" => getUtilitas($conn, "rantai_pasok", ["id", "kelas", "subkelas", "jenis", "nama", "bentuk", "alamat", "no_telp"], "infrastruktur_pendukung"),
    "reklame" => getUtilitas($conn, "reklame", ["id_reklame", "kelas", "subkelas", "keterangan"], "infrastruktur_pendukung"),
    "titik_bm" => getUtilitas($conn, "titik_bm", ["id", "kelas", "subkelas", "nama", "easting", "northing", "tinggi"], "infrastruktur_pendukung"),
    //jaringan ipal
    "manhole_ipal" => getUtilitas($conn, "manhole_ipal", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_glontor" => getUtilitas($conn, "pipa_glontor", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_induk" => getUtilitas($conn, "pipa_induk", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_lateral" => getUtilitas($conn, "pipa_lateral", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    //jaringan jalan
    "jalan_kota" => getUtilitas($conn, "jalan_kota", ["id_jalan", "nama_jalan", "material", "lebar_min", "lebar_max", "lebar_rata", "panjang", "kelurahan", "kemantren", "kelas", "subkelas"], "jaringan_jalan"),
    "jalan_lingkungan" => getUtilitas($conn, "jalan_lingkungan", ["id_jalan", "nama_jalan", "panjang", "kelurahan", "kemantren", "kelas", "subkelas"], "jaringan_jalan"),
    "jalan_tunanetra" => getUtilitas($conn, "jalan_tunanetra", ["id", "lebar", "kelurahan", "kecamatan", "kelas", "subkelas"], "jaringan_jalan"),
    "jembatan" => getUtilitas($conn, "jembatan", ["id", "kelas", "subkelas", "nama", "longitude", "latitude"], "jaringan_jalan"),
    "trotoar" => getUtilitas($conn, "trotoar", ["id_trotoar", "nama_jalan", "lebar_tro", "kelurahan", "kemantren", "kelas", "subkelas"], "jaringan_jalan"),
    //jaringan listrik
    "tegangan_menengah" => getUtilitas($conn, "tegangan_menengah", ["id_tm", "kelas", "subkelas", "klasifikas", "posisi_fas", "ukuran_kaw", "nama_gi"], "jaringan_listrik"),
    "tegangan_rendah" => getUtilitas($conn, "tegangan_rendah", ["id_tr", "kelas", "subkelas", "klasifikas", "posisi_fas", "ukuran_kaw", "nama_gi", "bahan_kawa", "deskripsi"], "jaringan_listrik"),
    "rumah_kabel" => getUtilitas($conn, "rumah_kabel", ["id", "kelas", "subkelas"], "jaringan_listrik"),
    "tiang_listrik" => getUtilitas($conn, "tiang_listrik", ["id", "kelas", "subkelas"], "jaringan_listrik"),
    "trafo_listrik" => getUtilitas($conn, "trafo_listrik", ["id", "kelas", "subkelas"], "jaringan_listrik"),
    //jaringan pdam
    "jaringan_pdam" => getUtilitas($conn, "jaringan_pdam", ["id_pdam", "kelas", "subkelas", "diameter", "jenis"], "jaringan_pdam"),
    //sungai
    "sungai" => getUtilitas($conn, "sungai", ["id", "kelas", "subkelas", "nama", "panjang"], "sungai"),
    
];

if (isset($_GET['subkelas'])) {
    $param = strtolower(trim($_GET['subkelas']));
    $map = [
        'manhole_ipal' => 'manhole instalasi pengolahan air limbah',
        'pipa_induk' => 'pipa induk',
        'pipa_glontor' => 'pipa glontor',
        'pipa_lateral' => 'pipa lateral',
        'jalan_kota' => 'jalan kota',
        'jalan_lingkungan' => 'jalan lingkungan',
        'jalan_tunanetra' => 'jalan tunanetra',
        'jembatan' => 'jembatan',
        'trotoar' => 'trotoar',
        'tegangan_menengah' => 'jaringan kabel listrik tegangan menengah',
        'tegangan_rendah' => 'jaringan kabel listrik tegangan rendah',
        'rumah_kabel' => 'rumah kabel',
        'tiang_listrik' => 'tiang listrik',
        'trafo_listrik' => 'trafolistrik',
        'jaringan_pdam' => 'jaringan pdam',
        'sungai' => 'sungai',
        'hidran' => 'hidran',
        'rantai_pasok' => 'rantai pasok',
        'reklame' => 'reklame',
        'titik_bm' => 'titik bench mark',
    ];

    $subkelas_cari = $map[$param] ?? $param;
    $filtered = [];

    foreach ($response as $layer => $fc) {
        foreach ($fc['features'] as $feat) {
            $sub = strtolower($feat['properties']['subkelas'] ?? '');
            if ($sub === $subkelas_cari || $layer === $param) {
                $filtered[] = $feat;
            }
        }
    }

    json_response(["type" => "FeatureCollection", "features" => $filtered]);
}

json_response($response);
