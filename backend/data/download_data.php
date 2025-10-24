<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '1024M');

if (isset($_GET['download']) && isset($_GET['subkelas'])) {
    $subkelas = trim($_GET['subkelas']); 
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $basePath = dirname($_SERVER['PHP_SELF']);
    $basePath = str_replace('/data', '', $basePath);
    $url1 = $protocol . $host . $basePath . "/gis/utilitas1.php";
    $url2 = $protocol . $host . $basePath . "/gis/utilitas2.php";
    $tmp = __DIR__ . "/tmp_" . uniqid();
    mkdir($tmp, 0777, true);
    $geojsonFile = "$tmp/$subkelas.geojson";
    $handle = fopen($geojsonFile, 'w');
    fwrite($handle, '{"type":"FeatureCollection","features":[');
    $first = true;
    $found = false;

    foreach ([$url1, $url2] as $url) {
        $json = @file_get_contents($url);
        if (!$json) continue;
        $decoded = json_decode($json, true);
        if (!$decoded) continue;

        foreach ($decoded as $layer => $fc) {
            foreach ($fc['features'] ?? [] as $feat) {
                if (isset($feat['properties']['subkelas']) && strcasecmp($feat['properties']['subkelas'], $subkelas) === 0) {
                    if (!$first) fwrite($handle, ',');
                    fwrite($handle, json_encode($feat, JSON_UNESCAPED_UNICODE));
                    $first = false;
                    $found = true;
                }
            }
        }
    }

    fwrite($handle, ']}');
    fclose($handle);

    if (!$found) die("❌ Tidak ada data untuk subkelas: $subkelas");
    $cmd = "ogr2ogr -f \"ESRI Shapefile\" -lco ENCODING=UTF-8 \"$tmp\" \"$geojsonFile\"";
    shell_exec($cmd);
    $zipPath = "$tmp/$subkelas.zip";
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        foreach (glob("$tmp/*.{shp,shx,dbf,prj,cpg}", GLOB_BRACE) as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
    }

    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=$subkelas.zip");
    header("Content-Length: " . filesize($zipPath));
    readfile($zipPath);
    foreach (glob("$tmp/*") as $f) unlink($f);
    rmdir($tmp);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        h2 {
            color: #003366;
            margin-top: 10px;
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 30px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
  background-color: #003366 !important; 
  border-color: #170080ff !important;
}

.btn-primary:hover {
  background-color: #040067ff !important;
  border-color: #0b0070ff !important;
}
</style>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Download Data Utilitas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light" style="margin-top: 50px; margin-left: 70px">
  <?php include "../../partials/header.php"; ?>
  <?php include "../../partials/sidebar.php"; ?>
  <div class="container py-5">
    <div class="card shadow p-4">
      <h4 class="mb-4 text-center" style="font-weight: bold; font-size: 30px; margin: 0">📦 Download Data Utilitas</h4>
      <form method="GET" id="formDownload">
        <div class="mb-3">
          <div class="card p-4">
          <h6 class="mb-3 fw-semibold">Pilih Kelas dan Subkelas</h5>
          <label class="form-label fw-semibold">Kelas</label>
          <select class="form-select" id="kelas" required>
            <option value="">-- Pilih Kelas --</option>
            <option value="atribut_jalan">Atribut Jalan</option>
            <option value="halte">Halte</option>
            <option value="infrastruktur_pendukung">Infrastruktur Pendukung</option>
            <option value="jaringan_drainase">Jaringan Drainase</option>
            <option value="jaringan_fiber_optik">Jaringan Fiber Optik</option>
            <option value="jaringan_ipal">Jaringan Instalasi Pengolahan Air Limbah</option>
            <option value="jaringan_jalan">Jaringan Jalan</option>
            <option value="jaringan_listrik">Jaringan Listrik</option>
            <option value="jaringan_pdam">Jaringan Perusahaan Daerah Air Minum</option>
            <option value="sungai">Sungai</option>
          </select>
      

        <div class="mb-3">
          <label class="form-label fw-semibold">Subkelas</label>
          <select class="form-select" name="subkelas" id="subkelas" required>
            <option value="">-- Pilih Subkelas --</option>
          </select>
        </div>

        <button type="submit" name="download" value="1" class="btn btn-primary w-100">
          💾 Download ZIP
        </button>
      </form>
    </div>
  </div>
  </div>

  <script>
    const subkelasMap = {
      atribut_jalan: [
        { val: 'Cermin Jalan', text: 'Cermin Jalan' },
        { val: 'Kamera Pengawas', text: 'Kamera Pengawas' },
        { val: 'Lampu Jalan', text: 'Lampu Jalan' },
        { val: 'Lampu Lalu Lintas', text: 'Lampu Lalu Lintas' },
        { val: 'Rambu Lalu Lintas', text: 'Rambu Lalu Lintas' }
      ],
      halte: [
        { val: 'Jalur Halte Trans', text: 'Jalur Halte Trans' },
        { val: 'Titik Halte', text: 'Titik Halte' }
        
      ],
      jaringan_drainase: [
        { val: 'Inlet', text: 'Inlet' },
        { val: 'Manhole Drainase', text: 'Manhole Drainase' },
        { val: 'Sumur Resapan', text: 'Sumur Resapan' },
        { val: 'Zona Drainase', text: 'Zona Drainase' }
      ],
      jaringan_fiber_optik: [
        { val: 'Kabel Fiber Optik', text: 'Kabel Fiber Optik' },
        { val: 'Manhole Fiber Optik', text: 'Manhole Fiber Optik' },
        { val: 'Tiang Fiber Optik', text: 'Tiang Fiber Optik' }
      ],
      jaringan_ipal: [
        { val: 'Manhole Instalasi Pengolahan Air Limbah', text: 'Manhole Instalasi Pengolahan Air Limbah' },
        { val: 'Pipa Glontor', text: 'Pipa Glontor' },
        { val: 'Pipa Induk', text: 'Pipa Induk' },
        { val: 'Pipa Lateral', text: 'Pipa Lateral' }
      ],
      jaringan_jalan: [
        { val: 'Jalan Kota', text: 'Jalan Kota' },
        { val: 'Jalan Lingkungan', text: 'Jalan Lingkungan' },
        { val: 'Jalan Tunanetra', text: 'Jalan Tunanetra' },
        { val: 'Jembatan', text: 'Jembatan' },
        { val: 'Trotoar', text: 'Trotoar' }
      ],
      jaringan_listrik: [
        { val: 'Jaringan Kabel Listrik Tegangan Menengah', text: 'Jaringan Kabel Listrik Tegangan Menengah' },
        { val: 'Jaringan Kabel Listrik Tegangan Rendah', text: 'Jaringan Kabel Listrik Tegangan Rendah' },
        { val: 'Rumah Kabel', text: 'Rumah Kabel' },
        { val: 'Tiang Listrik', text: 'Tiang Listrik' },
        { val: 'Trafo Listrik', text: 'Trafo Listrik' }
      ],
      jaringan_pdam: [
        { val: 'Jaringan PDAM', text: 'Jaringan Perusahaan Daerah Air Minum' }
      ],
      infrastruktur_pendukung: [
        { val: 'Hidran', text: 'Hidran' },
        { val: 'Rantai Pasok', text: 'Rantai Pasok' },
        { val: 'Reklame', text: 'Reklame' },
        { val: 'Titik Bench Mark', text: 'Titik Bench Mark' }
      ],
      sungai: [
        { val: 'Sungai', text: 'Sungai' }
      ]
    };

    document.getElementById('kelas').addEventListener('change', function() {
      const kelas = this.value;
      const subkelasSelect = document.getElementById('subkelas');
      subkelasSelect.innerHTML = '<option value="">-- Pilih Subkelas --</option>';

      if (kelas && subkelasMap[kelas]) {
        subkelasMap[kelas].forEach(opt => {
          const option = document.createElement('option');
          option.value = opt.val;
          option.textContent = opt.text;
          subkelasSelect.appendChild(option);
        });
      }
    });
  </script>
</body>
</html>

