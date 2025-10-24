<?php
include '../../backend/db/koneksi.php';

$subkelas = $_POST['subkelas'] ?? $_GET['subkelas'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? '';
$id = trim($id);

$subkelas_map = [
    "Rambu Lalu Lintas" => "rambu_lalin",
    "Rantai Pasok" => "rantai_pasok",
    "Cermin Jalan" => "cermin_jalan",
    "Kamera Pengawas" => "kamera_pengawas",
    "Lampu Jalan" => "lampu_jalan",
    "Lampu Lalu Lintas" => "lampu_lalin",
    "Titik Halte" => "titik_halte",
    "Jalur Halte Trans" => "jalur_halte",
    "Inlet" => "inlet",
    "Manhole Drainase" => "manhole_drainase",
    "Sumur Resapan" => "sumur_resapan",
    "Zona Drainase" => "zona_drainase",
    "Kabel Fiber Optik" => "kabel_fo",
    "Manhole Fiber Optik" => "manhole_fo",
    "Tiang Fiber Optik" => "tiang_fo",
    "Manhole Instalasi Pengolahan Air Limbah" => "manhole_ipal",
    "Pipa Glontor" => "pipa_glontor",
    "Pipa Induk" => "pipa_induk",
    "Pipa Lateral" => "pipa_lateral",
    "Hidran" => "hidran",
    "Jalan Kota" => "jalan_kota",
    "Jalan Lingkungan" => "jalan_lingkungan",
    "Jalan Tunanetra" => "jalan_tunanetra",
    "Jembatan" => "jembatan",
    "Trotoar" => "trotoar",
    "Jaringan Kabel Listrik Tegangan Menengah" => "tegangan_menengah",
    "Jaringan Kabel Listrik Tegangan Rendah" => "tegangan_rendah",
    "Rumah Kabel" => "rumah_kabel",
    "Tiang Listrik" => "tiang_listrik",
    "Trafo Listrik" => "trafo_listrik",
    "Jaringan PDAM" => "jaringan_pdam",
    "Sungai" => "sungai",
    "Reklame" => "reklame",
    "Titik Bench Mark" => "titik_bm",

];
$subkelas = trim($subkelas);
if (array_key_exists($subkelas, $subkelas_map)) {
    $subkelas = $subkelas_map[$subkelas];
} else {
    $subkelas = strtolower(str_replace(' ', '_', $subkelas));
}
$subkelas_list = [
    "bangunan",
    "cermin_jalan",
    "hidran",
    "inlet",
    "jalan_kota",
    "jalan_lingkungan",
    "jalan_tunanetra",
    "jalur_halte",
    "jembatan",
    "jaringan_pdam",
    "kabel_fo",
    "kamera_pengawas",
    "lampu_jalan",
    "lampu_lalin",
    "manhole_drainase",
    "manhole_fo",
    "manhole_ipal",
    "pipa_glontor",
    "pipa_induk",
    "pipa_lateral",
    "rambu_lalin",
    "rantai_pasok",
    "reklame",
    "rumah_kabel",
    "sumur_resapan",
    "sungai",
    "tegangan_menengah",
    "tegangan_rendah",
    "tiang_fo",
    "tiang_listrik",
    "titik_bm",
    "titik_halte",
    "trafo_listrik",
    "trotoar",
    "zona_drainase"
];
if (!in_array($subkelas, $subkelas_list, true)) {
    die("<div style='text-align:center;margin-top:50px;color:red;'>Subkelas tidak valid ($subkelas).</div>");
}

$pesan = '';
$data_edit = [];
$fields = [];
$res = mysqli_query($conn, "SELECT * FROM `$subkelas` LIMIT 1");
if ($res) {
    $fields = array_keys(mysqli_fetch_assoc($res));
} else {
    die("Gagal mengambil struktur tabel: " . mysqli_error($conn));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $pk = $_POST['pk'];
    $id_post = $_POST['id'];
    $set = [];
    foreach ($_POST as $field => $val) {
        if (in_array($field, ['update', 'id', 'pk', 'subkelas'])) continue;
        if ($field === 'geometri') continue;
        $val = mysqli_real_escape_string($conn, $val);
        $set[] = "`$field` = '$val'";
    }
    $setStr = implode(',', $set);
    $query = "UPDATE `$subkelas` SET $setStr WHERE `$pk` = '$id_post'";
    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success mt-3'>✅ Data berhasil diperbarui.</div>";
    } else {
        $pesan = "<div class='alert alert-danger mt-3'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
    }
    $id = $id_post;
}
if ($id) {
    $q = mysqli_query($conn, "SELECT * FROM `$subkelas` WHERE `{$fields[0]}` = '$id'");
    $data_edit = mysqli_fetch_assoc($q);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data <?= ucfirst(str_replace('_', ' ', $subkelas)) ?> - Dinas PUPR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }

        header {
            background-color: #004aad;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: 500;
        }

        input[readonly] {
            background-color: #e9ecef;
        }

        .btn-primary {
            background-color: #004aad;
            border-color: #004aad;
        }

        .btn-primary:hover {
            background-color: #003b8e;
        }

        .back-link {
            text-decoration: none;
            color: #004aad;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 50px;
            padding: 15px;
            font-size: 14px;
            text-align: center;
            color: #777;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <?php include "../../partials/sidebar.php"; ?>
    <?php include "../../partials/header.php"; ?>
    <header>
        <h2>✏️ Edit Data <?= ucfirst(str_replace('_', ' ', $subkelas)) ?> - Dinas PUPR Kota Yogyakarta</h2>
    </header>

    <div class="container my-5">
        <div class="card p-4">
            <h5 class="fw-semibold mb-3">Formulir Edit Data</h5>
            <?= $pesan ?>
            <?php if ($data_edit): ?>
                <form method="POST" action="">
                    <input type="hidden" name="subkelas" value="<?= htmlspecialchars($subkelas) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($data_edit[$fields[0]]) ?>">
                    <input type="hidden" name="pk" value="<?= htmlspecialchars($fields[0]) ?>">
                    <div class="row">
                        <?php foreach ($data_edit as $field => $value): ?>
                            <?php if ($field === 'geometri') continue; ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= ucfirst(str_replace('_', ' ', $field)) ?></label>
                                <?php if (strtolower($field) === strtolower($fields[0])): ?>
                                    <input type="text" name="<?= htmlspecialchars($field) ?>"
                                        value="<?= htmlspecialchars($value) ?>"
                                        class="form-control" readonly>
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($field) ?>"
                                        value="<?= htmlspecialchars($value) ?>"
                                        class="form-control">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" name="update" class="btn btn-primary px-4">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning mt-3">⚠️ Data tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        © <?= date('Y') ?> Dinas Pekerjaan Umum, Perumahan, dan Kawasan Permukiman Kota Yogyakarta
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>