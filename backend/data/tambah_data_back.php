<?php
include 'koneksi.php';

$kelas_subkelas = [
    "jaringan_jalan" => ["jalan_kota", "jalan_lingkungan", "trotoar"],
    "jaringan_listrik" => ["tegangan_menengah", "tegangan_rendah", "tiang_listrik", "rumah_kabel", "trafo_listrik"],
    "jaringan_pdam" => ["jaringan_pdam"],
    "bangunan" => ["bangunan"],
    "sungai" => ["sungai"],
    "infrastruktur_pendukung" => ["reklame"],
    "jaringan_ipal" => ["manhole_ipal"],
    "jaringan_drainase" => ["manhole_drainase"],
    "atribut_jalan" => ["lampu_jalan", "rambu_lalin", "kamera_pengawas", "cermin_jalan", "hidran"]
];

$kelas = $_GET['kelas'] ?? '';
$subkelas = $_GET['subkelas'] ?? '';
$kelas = strtolower(trim($kelas));
$subkelas = strtolower(trim($subkelas));

$fields = [];
if ($kelas && $subkelas && isset($kelas_subkelas[$kelas]) && in_array($subkelas, $kelas_subkelas[$kelas], true)) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$subkelas`");
    if ($result) {
        while ($col = mysqli_fetch_assoc($result)) {
            if (strtolower($col['Field']) !== 'geometri') {
                $fields[] = $col['Field'];
            }
        }
    }
}
function detectIdField($fields) {
    foreach ($fields as $f) {
        if (str_starts_with(strtolower($f), 'id')) return $f;
    }
    return $fields[0] ?? 'id';
}

$idField = detectIdField($fields);
$errors = [];
$success = '';
$polygon_wkt = '';
$foto = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = [];
    foreach ($fields as $f) {
        $postData[$f] = htmlspecialchars(trim($_POST[$f] ?? ''));
    }
    $polygon_wkt = htmlspecialchars(trim($_POST['polygon_wkt'] ?? ''));
    if (isset($_FILES['shapefile']) && $_FILES['shapefile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/shapefile/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $tmpFile = $_FILES['shapefile']['tmp_name'];
        $fileExt = strtolower(pathinfo($_FILES['shapefile']['name'], PATHINFO_EXTENSION));
        if ($fileExt !== 'zip') {
            $errors[] = "File shapefile harus berupa ZIP.";
        } else {
            $targetPath = $uploadDir . uniqid('shp_') . '.zip';
            move_uploaded_file($tmpFile, $targetPath);
            $extractDir = $uploadDir . uniqid('extract_') . '/';
            mkdir($extractDir, 0755, true);
            $zip = new ZipArchive();
            if ($zip->open($targetPath) === TRUE) {
                $zip->extractTo($extractDir);
                $zip->close();
                $shp_files = glob($extractDir . '*.shp');
                if (count($shp_files) > 0) {
                    $shp_file_path = $shp_files[0];
                    $geojson_path = $extractDir . 'output.geojson';
                    $cmd = "ogr2ogr -f GeoJSON " . escapeshellarg($geojson_path) . " " . escapeshellarg($shp_file_path) . " 2>&1";
                    exec($cmd, $output, $return_var);
                    if ($return_var === 0 && file_exists($geojson_path)) {
                        $geojson = json_decode(file_get_contents($geojson_path), true);
                        if (isset($geojson['features'][0]['geometry'])) {
                            $geometry = $geojson['features'][0]['geometry'];
                            $type = strtoupper($geometry['type']);
                            $coords = $geometry['coordinates'];
                            switch ($type) {
                                case 'POINT':
                                    $polygon_wkt = "POINT(" . implode(" ", $coords) . ")";
                                    break;
                                case 'LINESTRING':
                                    $polygon_wkt = "LINESTRING(" . implode(",", array_map(fn($c) => implode(" ", $c), $coords)) . ")";
                                    break;
                                case 'POLYGON':
                                    $rings = array_map(fn($ring) => "(" . implode(",", array_map(fn($c) => implode(" ", $c), $ring)) . ")", $coords);
                                    $polygon_wkt = "POLYGON(" . implode(",", $rings) . ")";
                                    break;
                            }
                        } else {
                            $errors[] = "GeoJSON tidak memiliki geometri.";
                        }
                    } else {
                        $errors[] = "Gagal konversi shapefile ke GeoJSON.";
                    }
                } else {
                    $errors[] = "File .shp tidak ditemukan dalam ZIP.";
                }
            } else {
                $errors[] = "Gagal ekstrak file ZIP.";
            }
        }
    }

    
    if (empty($errors)) {
        $fieldsInsert = implode(',', array_merge($fields, ['geometri']));
        $placeholders = implode(',', array_fill(0, count($fields)+2, '?'));
        $types = str_repeat('s', count($fields)) . 'ss'; 
        $values = array_values($postData);
        $values[] = $polygon_wkt;
        $values[] = $foto;
        $sql = "INSERT INTO `$subkelas` ($fieldsInsert) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                $success = "Data berhasil ditambahkan.";
            } else {
                $errors[] = $stmt->error;
            }
        } else {
            $errors[] = $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Data <?= htmlspecialchars($subkelas) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="https://unpkg.com/wellknown/wellknown.js"></script>
<style>#map{height:350px;}</style>
</head>
<body>
<div class="container my-4">
<h2>Tambah Data <?= ucfirst(str_replace('_',' ',$subkelas)) ?></h2>

<?php if($errors): ?>
<div class="alert alert-danger">
    <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
</div>
<?php endif; ?>
<?php if($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Kelas</label>
            <select id="kelas" name="kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelas_subkelas as $k=>$subs): ?>
                <option value="<?= $k ?>" <?= $k==$kelas?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$k)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Subkelas</label>
            <select id="subkelas" name="subkelas" class="form-select" required>
                <option value="">-- Pilih Subkelas --</option>
                <?php if($kelas && isset($kelas_subkelas[$kelas])): ?>
                    <?php foreach($kelas_subkelas[$kelas] as $s): ?>
                        <option value="<?= $s ?>" <?= $s==$subkelas?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <?php foreach($fields as $f): ?>
        <div class="col-md-6">
            <label class="form-label"><?= ucfirst(str_replace('_',' ',$f)) ?></label>
            <input type="text" class="form-control" name="<?= $f ?>" value="<?= htmlspecialchars($_POST[$f] ?? '') ?>" />
        </div>
        <?php endforeach; ?>
        <div class="col-md-12">
            <label class="form-label">Upload Shapefile (ZIP)</label>
            <input type="file" name="shapefile" class="form-control" accept=".zip" />
        </div>

        <div class="col-md-12">
            <label class="form-label">Upload Foto</label>
            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
        </div>

        <div class="col-md-12">
            <label class="form-label">Gambar Poligon Manual</label>
            <div id="map"></div>
            <input type="hidden" id="polygon_wkt" name="polygon_wkt" value="<?= htmlspecialchars($polygon_wkt) ?>" />
        </div>

        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </div>
</form>
</div>

<script>
document.getElementById('kelas').addEventListener('change', function() {
    var kelas = this.value;
    var subkelasSelect = document.getElementById('subkelas');
    subkelasSelect.innerHTML = '<option value="">-- Pilih Subkelas --</option>';

    var kelasSubs = <?php echo json_encode($kelas_subkelas); ?>;
    if(kelas && kelasSubs[kelas]){
        kelasSubs[kelas].forEach(function(s){
            var opt = document.createElement('option');
            opt.value = s;
            opt.text = s.replace(/_/g,' ');
            subkelasSelect.add(opt);
        });
    }
});
var map = L.map('map').setView([-7.57,110.82],13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

var drawControl = new L.Control.Draw({
    edit: { featureGroup: drawnItems },
    draw: { polygon:true, polyline:false, circle:false, rectangle:false, marker:false, circlemarker:false }
});
map.addControl(drawControl);

map.on(L.Draw.Event.CREATED, function(e){
    drawnItems.clearLayers();
    var layer = e.layer;
    drawnItems.addLayer(layer);
    document.getElementById('polygon_wkt').value = wellknown.stringify(layer.toGeoJSON().geometry);
});

map.on('draw:edited', function(e){
    e.layers.eachLayer(function(layer){
        document.getElementById('polygon_wkt').value = wellknown.stringify(layer.toGeoJSON().geometry);
    });
});

map.on('draw:deleted', function(e){
    document.getElementById('polygon_wkt').value = '';
});
</script>
</body>
</html>
