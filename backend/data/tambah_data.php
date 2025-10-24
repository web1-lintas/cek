<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../backend/auth/signin.php?redirect=/web_pupr/backend/data/tambah_data.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Utilitas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f0f2f5;
            padding: 20px;
            color: #212529;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex: 1 1 45%;
            min-width: 320px;
        }

        h4 {
            text-align: center;
            font-size: 28px;
            color: #000000ff;
            margin-bottom: 25px;
            margin-top: 0;
        }

        h5 {
            font-size: 20px;
            color: #003366;
            margin-bottom: 15px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 8px;
        }

        a.download-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #003366;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        a.download-btn:hover {
            background: #003366;
        }

        select,
        input[type="file"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #003366;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background: #1c2f70;
        }

        #subkelasSelect {
            max-height: 160px;
            overflow-y: auto;
        }

        .accordion-item {
            background-color: #003366;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 6px;
            overflow: hidden;
            transition: all 0.2s ease-in-out;
        }

        .accordion-header {
            padding: 8px 14px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            background-color: #003366;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
        }

        .accordion-header:hover {
            background-color: #00227b;
        }

        .accordion-header i {
            transition: transform 0.3s ease;
        }

        .accordion-header.active i {
            transform: rotate(180deg);
        }

        .accordion-content {
            background: #f2f4ff;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.35s ease, opacity 0.3s ease;
            padding: 5 15px;
            list-style: none;
            margin: 0;
        }

        .accordion-content.show {
            opacity: 1;
            padding: 10px 16px 16px;
        }

        .accordion-content li {
            margin: 6px 0;
        }

        .accordion-content a {
            text-decoration: none;
            color: #ffffffff;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            padding: 4px 0;
            transition: color 0.2s;
        }

        .accordion-content a:hover {
            color: #0044ff;
        }
    </style>
</head>

<body style="margin-top:80px; margin-left:80px;">

    <?php include "../../partials/header.php"; ?>
    <?php include "../../partials/sidebar.php"; ?>

    <div class="container">
         
        <div class="card">
            <h4>📂 Unduh Template Shapefile</h4>
            <p>Pilih format shapefile sesuai kelas dan subkelas berikut:</p>
            <p style="font-size: 11px; margin-top: -10px">*Untuk kolom ID mohon dikosongkan saja saat mengunggah file yang telah ditambahkan</p>
            <div id="accordionContainer">
                <?php
                $base_path = "/web_pupr/assets/form";
                $kelas_subkelas = [
                    "atribut_jalan" => ["cermin_jalan", "kamera_pengawas", "lampu_jalan", "lampu_lalin", "rambu_lalin"],
                    "halte" => ["titik_halte", "jalur_halte"],
                    "infrastruktur_pendukung" => ["hidran", "rantai_pasok", "reklame", "titik_bm"],
                    "jaringan_drainase" => ["inlet", "manhole_drainase", "sumur_resapan", "zona_drainase"],
                    "jaringan_fiber_optik" => ["kabel_fo", "manhole_fo", "tiang_fo"],
                    "jaringan_ipal" => ["manhole_ipal", "pipa_glontor", "pipa_induk", "pipa_lateral"],
                    "jaringan_jalan" => ["jalan_kota", "jalan_lingkungan", "jalan_tunanetra", "jembatan", "trotoar"],
                    "jaringan_listrik" => ["rumah_kabel", "tegangan_menengah", "tegangan_rendah", "tiang_listrik", "trafo_listrik"],
                    "jaringan_pdam" => ["jaringan_pdam"],
                    "sungai" => ["sungai"]
                ];
                foreach ($kelas_subkelas as $kelas => $subs): ?>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <?= ucfirst(str_replace('_', ' ', $kelas)) ?>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <ul class="accordion-content">
                            <?php foreach ($subs as $sub): ?>
                                <li>
                                    <a href="<?= "$base_path/$sub.zip" ?>" class="download-btn" download>
                                        <i class="bi bi-file-earmark-zip-fill"></i> <?= $sub ?>.zip
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <h4>🗳️ Tambah Data Utilitas</h4>
            <label><b>Kelas</b></label>
            <select id="kelasSelect">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_subkelas as $kelas => $subs): ?>
                    <option value="<?= $kelas ?>"><?= ucfirst(str_replace('_', ' ', $kelas)) ?></option>
                <?php endforeach; ?>
            </select>
            <div id="subkelasContainer">
                <label><b>Subkelas</b></label>
                <select id="subkelasSelect">
                    <option value="">-- Pilih Subkelas --</option>
                </select>
            </div>
            <label><b>Unggah Shapefile (ZIP)</b></label>
            <input type="file" id="shapefileInput" accept=".zip">
<button id="uploadBtn">Upload dan Simpan</button>

    <div id="uploadStatus" style="margin-top:10px; font-weight:600;"></div>
        </div>
    </div>
    <script>
        const kelasSubkelasMap = <?php echo json_encode($kelas_subkelas); ?>;
        const kelasSelect = document.getElementById("kelasSelect");
        const subkelasContainer = document.getElementById("subkelasContainer");
        const subkelasSelect = document.getElementById("subkelasSelect");

        kelasSelect.addEventListener("change", () => {
            const kelas = kelasSelect.value;
            subkelasSelect.innerHTML = '<option value="">-- Pilih Subkelas --</option>';
            if (kelas && kelasSubkelasMap[kelas]) {
                kelasSubkelasMap[kelas].forEach(sub => {
                    const opt = document.createElement("option");
                    opt.value = sub;
                    opt.textContent = sub.replace(/_/g, " ");
                    subkelasSelect.appendChild(opt);
                });
                subkelasContainer.style.display = "block";
                subkelasContainer.style.opacity = "0";
                setTimeout(() => {
                    subkelasContainer.style.transition = "opacity 0.3s ease";
                    subkelasContainer.style.opacity = "1";
                }, 50);

            } else {
                subkelasContainer.style.display = "none";
            }
        });
        document.querySelectorAll(".accordion-header").forEach(header => {
            header.addEventListener("click", () => {
                const panel = header.nextElementSibling;
                const icon = header.querySelector("i");
                document.querySelectorAll(".accordion-content").forEach(p => {
                    if (p !== panel) {
                        p.style.maxHeight = null;
                        p.classList.remove("show");
                        p.previousElementSibling.classList.remove("active");
                        p.previousElementSibling.querySelector("i").style.transform = "rotate(0deg)";
                    }
                });
                header.classList.toggle("active");
                if (panel.classList.contains("show")) {
                    panel.style.maxHeight = null;
                    panel.classList.remove("show");
                    icon.style.transform = "rotate(0deg)";
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    panel.classList.add("show");
                    icon.style.transform = "rotate(180deg)";
                }
            });
        });
        document.getElementById("uploadBtn").addEventListener("click", async () => {
  const kelas = document.getElementById("kelasSelect").value;
  const subkelas = document.getElementById("subkelasSelect").value;
  const fileInput = document.getElementById("shapefileInput");
  const statusEl = document.getElementById("uploadStatus");
  if (!kelas || !subkelas) {
      statusEl.innerHTML = "<span style='color:red;'>⚠️ Pilih kelas dan subkelas terlebih dahulu.</span>";
      return;
  }
  if (!fileInput.files.length) {
      statusEl.innerHTML = "<span style='color:red;'>⚠️ Pilih file ZIP shapefile terlebih dahulu.</span>";
      return;
  }
  const formData = new FormData();
  formData.append('kelas', kelas);
  formData.append('subkelas', subkelas);
  formData.append('shapefile_zip', fileInput.files[0]);

  statusEl.innerHTML = "<span style='color:#001561;'>⏳ Mengunggah dan memproses data...</span>";

  try {
      const response = await fetch("upload_shapefile.php", { method: "POST", body: formData });
      const text = await response.text();
      console.log("SERVER RESPONSE:", text);

      let data;
      try {
          data = JSON.parse(text);
      } catch {
          throw new Error("Respons server bukan JSON yang valid. Lihat console untuk detail.");
      }

      if (data.status === "success") {
          statusEl.innerHTML = `<span style='color:green;'>✅ ${data.message}</span>`;
          alert("✅ " + data.message);
      } else {
          statusEl.innerHTML = `<span style='color:red;'>❌ ${data.message}</span>`;
          alert("❌ " + data.message);
      }
  } catch (err) {
      console.error("Upload error:", err);
      statusEl.innerHTML = "<span style='color:red;'>❌ Gagal mengunggah file. Periksa koneksi atau server.</span>";
  }
});
    </script>

</body>

</html>