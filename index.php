<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SITIJO</title>
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <style>
    html {
      scroll-behavior: smooth;
    }

    :root {
      --biru-pupr: #003366;
      --kuning-pupr: #f2b705;
      --abu-lembut: #f9fafc;
      --abu-teks: #555;
      --putih: #ffffff;
    }

    html,
    body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      overflow-y: auto;
      font-family: 'Roboto', sans-serif;
      background-color: var(--abu-lembut);
      color: var(--abu-teks);
    }

    .main {
      margin-left: 0;
      min-height: 100vh;
      margin-top: -3px;
    }

    .carousel-item {
      min-height: 40vh;
      background-size: cover;
      background-position: center;
    }

    .carousel-item.fullheight {
      height: 100vh;
      background-size: cover;
      background-position: center;
    }

    .carousel-caption {
      bottom: 30%;
    }

    .carousel-caption h1 {
      font-size: 50px;
      font-weight: bold;
      font-size: clamp(24px, 4vw, 55px);
      font-family: 'Montserrat', sans-serif
    }

    .carousel-caption p {
      font-size: 16px;
      font-family: 'Times New Roman', Times, serif;
    }

    .btn-explore {
      background-color: var(--kuning-pupr);
      color: var(--biru-pupr);
      border-radius: 20px;
      font-weight: 700;
      font-size: 13px;
      transition: all 0.3s ease-in-out;
    }

    .btn-explore:hover {
      background-color: #ffd84d;
      transform: translateY(-3px);
    }

    #map {
      width: 100%;
      max-width: 1100px;
      height: 500px;
      border-radius: 10px;
      margin: 0 auto 30px auto;
      background: #ccc;
    }

    #contact {
      font-family: 'Roboto', sans-serif;
      font-size: 17px;
      line-height: 1.4;
      color: #fff;
    }

    #contact h3 {
      font-weight: 700;
      font-size: 1.4rem;
      margin-bottom: 1rem;
    }

    #contact p {
      margin-bottom: 4px;
    }

    #contact .fw-bold {
      font-weight: 500;
      margin-top: 6px;
      margin-bottom: 2px;
    }

    #contact .mb-2 {
      margin-bottom: 4px !important;
    }

    #contact .mb-4 {
      margin-bottom: 8px !important;
    }

    #contact a {
      color: #1f0270ff;
      transition: color 0.3s ease;
    }

    #contact a:hover {
      color: #4e8cff;
    }

    .contact-fade {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.8s ease-out;
    }

    .contact-fade.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .kilas-box {
      background: #fff;
      border-radius: 40px;
      padding: 40px;
      max-width: 1000px;
      margin: 40px auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .kilas-box h2 {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .kilas-box p {
      color: #444;
      margin-bottom: 30px;
    }

    .data-items {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 40px;
      margin-top: 30px;
    }


    .item {
      text-align: center;
    }

    .item .icon {
      margin: 0 auto 15px auto;
      background: #f0f4ff;
      color: #0044cc;
      font-size: 30px;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }

    .item .icon:hover {
      transform: scale(1.15);
    }

    .item h3 {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
    }

    .item p {
      font-size: 14px;
      color: #666;
      margin: 5px 0 0 0;
    }

    .counter {
      font-size: 1.8rem;
      font-weight: 700;
      color: #001f54;
    }

    #contact small {
      font-size: 0.8rem;
      opacity: 0.8;
    }

    .footer-highlight {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 12px 10px;
      background: rgba(78, 140, 255, 0.1);
      border-radius: 6px;
      margin-top: 1rem;
      text-align: center;
    }

    .footer-highlight small {
      color: #ffffff;
      font-size: 0.85rem;
      font-weight: 400;
      padding: 4px 12px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 4px;
      box-shadow: 0 0 8px rgba(78, 140, 255, 0.3);
    }

    .animated-item {
      transition: all 0.3s ease;
      border-radius: 12px;
    }

    .animated-item:hover {
      background-color: #f8f9fa;
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .contact-info i:hover {
      animation: bounce 0.6s;
    }

    #organisasi .list-group-item {
      transition: all 0.3s ease;
      border: none;
      border-bottom: 1px solid #f1f1f1;
    }

    #organisasi .list-group-item:hover {
      background: linear-gradient(90deg, #f0f7ff, #ffffff);
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    #fiturCarousel .carousel-control-prev,
    #fiturCarousel .carousel-control-next {
      width: 5%;
    }

    #fiturCarousel .carousel-control-prev {
      left: -40px;
    }

    #fiturCarousel .carousel-control-next {
      right: -40px;
    }

    #fiturCarousel .carousel-control-prev-icon,
    #fiturCarousel .carousel-control-next-icon {
      background-size: 100%, 100%;
      width: 2rem;
      height: 2rem;
      filter: invert(1);
    }

    @media (max-width: 768px) {
      #map {
        height: 300px;
      }

      .carousel-caption {
        bottom: 15%;
        text-align: center;
      }

      .carousel-caption h1 {
        font-size: 24px;
      }

      .carousel-caption p {
        font-size: 14px;
      }

      .btn-explore {
        font-size: 12px;
        padding: 8px 16px;
      }

      #fiturCarousel .carousel-control-prev {
        left: -25px;
      }

      #fiturCarousel .carousel-control-next {
        right: -25px;
      }
    }

    @keyframes switchWord {

      0%,
      25% {
        transform: translateY(0);
      }

      50%,
      75% {
        transform: translateY(-1em);
      }

      100% {
        transform: translateY(0);
      }
    }

    @keyframes bounce {

      0%,
      20%,
      50%,
      80%,
      100% {
        transform: translateY(0);
      }

      40% {
        transform: translateY(-10px);
      }

      60% {
        transform: translateY(-5px);
      }
    }
  </style>
</head>


<body>
  <?php
  include $_SERVER['DOCUMENT_ROOT'] . "/partials/header.php";
  include $_SERVER['DOCUMENT_ROOT'] . "/partials/sidebar.php";
  ?>

  <div class="main">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active fullheight" style="background-image:url('assets/background.jpg');">
          <div class="carousel-caption text-start">
            <h1>Satu Data, Satu Peta Infrastruktur Kota Yogyakarta</h1>
            <p>Sistem Informasi Infrastruktur Kota Yogyakarta (SITIJO) merupakan platform digital internal Dinas Pekerjaan Umum, Perumahan dan Kawasan Permukiman Kota Yogyakarta yang menyajikan data spasial dan data atribut penunjang infrastruktur Kota Yogyakarta.</p>
            <?php if (isset($_SESSION['user'])): ?>
              <button onclick="window.location.href='/frontend/peta.php'" class="btn-explore">Explore Peta</button>
            <?php else: ?>
              <button onclick="window.location.href='/backend/auth/signin.php'" class="btn-explore">Explore Peta</button>
            <?php endif; ?>

          </div>
        </div>
        <div class="carousel-item fullheight" style="background-image:url('assets/background.jpg');">
          <div class="carousel-caption">
            <h1>Kolaborasi dan Integrasi Data</h1>
            <p>SITIJO berbasis Website based Geographic Information System (WEBGIS) dapat digunakan sebagai sarana koordinasi antar bidang di lingkup DPUPKP Kota Yogyakarta untuk pengelolaan data infrastruktur di Kota Yogyakarta.</p>
          </div>
        </div>
        <div class="carousel-item fullheight" style="background-image:url('assets/background.jpg');">
          <div class="carousel-caption text-end">
            <h1>Akses Terbatas dan Terlindungi</h1>
            <p>Platform digital ini hanya dapat diakses oleh bidang dan pejabat terkait di lingkup DPUPKP Kota Yogyakarta, bukan untuk publik.</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>

  <section id="fitur" class="py-5" style="background: #ffffffff;" data-aos="fade-up">
    <div class="container text-center">
      <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">
        <strong>FITUR</strong> yang Tersedia pada <strong>#</strong>
        <span style="display:inline-block; position:relative; width:150px; text-align: left; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
          <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
            <span style="color: #213878; display:block; height:1em;">WEBGIS</span>
            <span style="color: #FFCC00; display:block; height:1em;">SITIJO</span>
          </span>
        </span>
      </h2>
      <div id="fiturCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="row g-4 justify-content-center">
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-building-fill" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Profil DPUPKP dan Bidang Terkait</h5>
                    <h6 style="font-size: 13px;">
                      Informasi profil Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman serta bidang-bidang yang terkait.
                    </h6>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-database-fill-gear" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Ketersediaan Data Infrastruktur</h5>
                    <h6 style="font-size: 13px;">
                      Informasi jenis data infrastruktur yang tersedia pada WEBGIS SITIJO meliputi deskripsi kelas data, subkelas data, progres ketersediaan data, dan statistika progres ketersediaan data.
                    </h6>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-layers-fill" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Visualisasi Peta Infrastruktur</h5>
                    <h6 style="font-size: 13px;">
                      Menampilkan visualisasi layer infrastruktur mencakup Atribut Jalan, Bangunan, Halte, Infrastruktur Pendukung, Jaringan Drainase, Jaringan Fiber Optik, Jaringan IPAL, Jaringan Jalan, Jaringan Listrik, Jaringan PDAM, dan Sungai.
                    </h6>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="carousel-item">
            <div class="row g-4 justify-content-center">
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-search" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Fitur Pencarian Jalan & Wilayah</h5>
                    <h6 style="font-size: 13px;">
                      Fitur pencarian interaktif berdasarkan nama jalan, kemantren, dan kelurahan di Kota Yogyakarta.
                    </h6>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-pencil-square" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Fitur Update Data Infrastruktur</h5>
                    <h6 style="font-size: 13px;">
                      Fitur update data infrastruktur memungkinkan pengguna untuk melakukan pengeditan maupun penghapusan data.
                    </h6>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-trash3-fill" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Fitur Tambah Data Infrastruktur</h5>
                    <h6 style="font-size: 13px;">
                      Fitur tambah data infrastruktur memungkinkan pengguna untuk melakukan penambahan data infrastruktur baru.
                    </h6>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="carousel-item">
            <div class="row g-4 justify-content-center">
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-download" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Fitur Download Data Infrastruktur</h5>
                    <h6 style="font-size: 13px;">
                      Fitur download data infrastruktur memungkinkan pengguna dapat mengunduh data terpilih sesuai kebutuhan analisis atau pelaporan.
                    </h6>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card text-center shadow-sm border-0 rounded-4 h-100 p-4">
                  <i class="bi bi-person-lock" style="font-size:45px; color: var(--biru-pupr);"></i>
                  <div class="card-body">
                    <h5 class="fw-bold">Akses Terproteksi</h5>
                    <h6 style="font-size: 13px;">
                      Setiap pengguna memiliki hak akses sesuai bidang, memastikan keamanan data infrastruktur.
                    </h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#fiturCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon custom-arrow" aria-hidden="true"></span>
          <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#fiturCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon custom-arrow" aria-hidden="true"></span>
          <span class="visually-hidden">Selanjutnya</span>
        </button>
      </div>
    </div>
  </section>

  <section id="organisasi" class="py-5" style="background-color: #eaf5ffaa">
    <div class="container" style="margin-left: 100px;">
      <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">
        <strong>BIDANG TERKAIT</strong> dengan Data Infrastruktur pada <strong>#</strong>
        <span style="display:inline-block; position:relative; width:150px; text-align: left; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
          <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
            <span style="color: #213878; display:block; height:1em;">WEBGIS</span>
            <span style="color: #FFCC00; display:block; height:1em;">SITIJO</span>
          </span>
        </span>
      </h2>
      <div class="row align-items-center">
        <div class="col-md-6" data-aos="fade-right">
          <h5 class="fw-bold mb-2">Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman Kota Yogyakarta</h5>
          <p class="mb-3" style="text-align: justify">
            Berdasarkan <strong>Peraturan Wali Kota Yogyakarta Nomor 17 Tahun 2024 tentang Perubahan atas Peraturan Wali Kota Yogyakarta Nomor 37 Tahun 2023</strong>, bahwa susunan organisasi pada Dinas Pekerjaan Umum, Perumahan dan Kawasan Permukiman Kota Yogyakarta.
          </p>
          <p style="text-align: justify;">
            Terdapat beberapa bidang yang berkaitan dengan penyajian data
            geospasial pada Sistem Informasi Infrastruktur Kota Yogyakarta (SITIJO).
          </p>
        </div>
        <div class="col-md-6 mt-4 mt-md-0" data-aos="fade-left">
          <div class="d-flex flex-column gap-4">
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-building-check"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">Bidang Penataan Bangunan</h6>
                <p class="mb-0 small text-muted">Menangani perencanaan, penataan, pengelolaan, dan pembinaan terhadap bangunan gedung serta tata ruang di wilayah Kota Yogyakarta.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-cone-striped"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">Bidang Pengendalian dan Pembinaan Jasa Konstruksi</h6>
                <p class="mb-0 small text-muted">Melaksanakan pembinaan, pengawasan, dan pengendalian terhadap pelaksanaan kegiatan jasa konstruksi di wilayah Kota Yogyakarta.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-house-door"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">Bidang Perumahan & Kawasan Permukiman</h6>
                <p class="mb-0 small text-muted">Bertanggung jawab dalam perencanaan, pembangunan, dan peningkatan kualitas perumahan serta kawasan permukiman.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">Bidang Jalan & Jembatan</h6>
                <p class="mb-0 small text-muted">Menangani pembangunan, peningkatan, dan pemeliharaan infrastruktur jalan serta jembatan di wilayah Kota Yogyakarta.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-droplet"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">Bidang SDA & Drainase</h6>
                <p class="mb-0 small text-muted">Mengatur, mengelola, dan memelihara jaringan drainase serta sumber daya air guna mencegah genangan dan banjir.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background:#fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-recycle"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">UPT Pengelolaan Air Limbah</h6>
                <p class="mb-0 small text-muted">Mengelola pengumpulan, pengolahan, dan pembuangan air limbah domestik untuk menjaga kebersihan dan kesehatan lingkungan.</p>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                style="width:60px;height:60px;background: #fffbdaff;color: var(--biru-pupr);font-size:28px;">
                <i class="bi bi-lightbulb"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" style="color: #213878;">UPT Penerangan Jalan Umum</h6>
                <p class="mb-0 small text-muted">Mengelola pengoperasian, pemeliharaan, dan peningkatan sarana penerangan jalan umum di seluruh wilayah kota.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="kilasdata" class="kilas-data">
    <div class="kilas-box">
      <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">Kilas
        <strong>BATASAN DATA</strong> pada <strong>#</strong>
        <span style="display:inline-block; position:relative; width:150px; text-align: left; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
          <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
            <span style="color: #213878; display:block; height:1em;">WEBGIS</span>
            <span style="color: #FFCC00; display:block; height:1em;">SITIJO</span>
          </span>
        </span>
      </h2>
      <p>
        Kota Yogyakarta secara astronomis terletak di antara 7°15'24" sampai dengan 7°49'26" Lintang Selatan dan 110°24'19" sampai dengan 110°28'53" Bujur Timur, dengan ketinggian rata-rata 114 m di atas permukaan laut.
      </p>
      <div class="data-items" style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap; text-align:center; margin-top:20px;">
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <i class="bi bi-globe-asia-australia"></i>
          </div>
          <h3 class="counter" data-target="32.82">0</h3>
          <p>Luas Wilayah (km²)</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <i class="bi bi-map"></i>
          </div>
          <h3 class="counter" data-target="14">0</h3>
          <p>Kecamatan</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <i class="bi bi-buildings"></i>
          </div>
          <h3 class="counter" data-target="45">0</h3>
          <p>Kelurahan</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <i class="bi bi-house-door"></i>
          </div>
          <h3 class="counter" data-target="616">0</h3>
          <p>RW</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <i class="bi bi-people"></i>
          </div>
          <h3 class="counter" data-target="2535">0</h3>
          <p>RT</p>
        </div>
      </div>

      <script>
        document.querySelectorAll('.icon').forEach(icon => {
          icon.addEventListener('mouseenter', () => {
            icon.style.transform = 'translateY(-8px) scale(1.1)';
          });
          icon.addEventListener('mouseleave', () => {
            icon.style.transform = 'translateY(0) scale(1)';
          });
        });
      </script>
    </div>
  </section>

  <script>
    function animateCounter(counter) {
      const target = +counter.getAttribute("data-target");
      const duration = 2000;
      const frameRate = 60;
      const totalFrames = Math.round((duration / 1000) * frameRate);
      let frame = 0;
      const start = 0;
      const increment = (target - start) / totalFrames;
      const timer = setInterval(() => {
        frame++;
        const current = start + increment * frame;
        if (target % 1 === 0) {
          counter.textContent = Math.floor(current);
        } else {
          counter.textContent = current.toFixed(2);
        }
        if (frame >= totalFrames) {
          clearInterval(timer);
          counter.textContent = target;
        }
      }, 1000 / frameRate);
    }
    document.addEventListener("DOMContentLoaded", () => {
      const counters = document.querySelectorAll(".counter");
      counters.forEach(counter => {
        animateCounter(counter);
      });
    });
  </script>

  <section id="contact" class="py-5" style="background:var(--biru-pupr);color:#fff;">
    <div class="container">
      <div class="row align-items-center">
        <h2 style="display:flex; justify-content:center; align-items:center; gap:0.5rem; margin-bottom:2rem; margin-left: 400px; font-family:'Montserrat', sans-serif; font-weight:700;">
          <strong>Kontak</strong><strong>#</strong>
          <span style="display:inline-block; position:relative; width:1000px; height:1em; overflow:hidden;">
            <span style="position:absolute; left:50%; transform:translateX(-50%); animation:switchWord 4s infinite; font-weight:bold; text-align:left;margin-left:-470px;">
              <span style="color: #ffa200ff; display:block; height:1em;">DPUPKP</span>
              <span style="color: #FFCC00; display:block; height:1em;">KOTA YOGYAKARTA</span>
            </span>
          </span>
        </h2>

        <div class="col-lg-6 mb-4">
          <div class="contact-info" style="margin-left: 40px;">
            <p class="mb-3 fw-bold contact-fade">
              <i class="fas fa-building me-2 text-primary"></i>
              Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman<br>
              Pemerintah Kota Yogyakarta
            </p>

            <p class="mb-3 contact-fade">
              <i class="fas fa-map-marker-alt me-2 text-warning"></i>
              Jl. Kenari No. 56, Kelurahan Muja Muju, Kecamatan Umbulharjo,<br>
              Kota Yogyakarta, Daerah Istimewa Yogyakarta 55165
            </p>

            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-phone-alt me-2 text-success"></i>
              Telepon
            </p>
            <p class="mb-3 contact-fade">(0274) 515867, (0274) 586795, (0274) 515866</p>

            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-fax me-2 text-info"></i>
              Fax
            </p>
            <p class="mb-3 contact-fade">(0274) 586795</p>

            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-sms me-2 text-danger"></i>
              Hot Line SMS
            </p>
            <p class="mb-3 contact-fade">08122780001</p>

            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-envelope me-2 text-light"></i>
              Email
            </p>
            <p class="mb-3 contact-fade">puperkim@jogjakota.go.id</p>

            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-envelope-open-text me-2 text-light"></i>
              Hot Line Email
            </p>
            <p class="mb-4 contact-fade">upik@jogjakota.go.id</p>
          </div>
        </div>

        <div class="col-lg-6 text-center">
          <iframe src="https://www.google.com/maps/embed?pb=!4v1760506376645!6m8!1m7!1sOfaHpDghWUdfRJ6CkyIGIA!2m2!1d-7.799226170639185!2d110.3908016988914!3f179.78600593360554!4f-24.951894744737785!5f0.4000000000000002"
            width="100%"
            height="450"
            style="border:0;border-radius:10px;"
            allow="accelerometer; gyroscope; fullscreen"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>

    <div class="text-center mt-3">
      <small class="d-block mb-2">Ikuti Kami:</small>
      <a href="https://pu.jogjakota.go.id/" class="text-white me-3"><i class="fas fa-globe fa-lg"></i></a>
      <a href="https://www.instagram.com/dpupkpkotajogja" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
      <a href="https://youtube.com/@dinaspupkpkotayogyakarta" class="text-white"><i class="fab fa-youtube fa-lg"></i></a>
    </div>
    </div>
    </div>
    <footer class="text-center py-4" style="background: var(--biru-pupr); color: white;">
      <div class="container">
        <p class="mb-1 fw-bold">© 2025 Dinas Pekerjaan Umum, Perumahan dan Kawasan Permukiman Kota Yogyakarta</p>
      </div>
    </footer>
    </div>
  </section>
  <script>
    const sectionIds = ['fitur', 'organisasi', 'kilasdata'];
    const sections = sectionIds.map(id => document.getElementById(id)).filter(el => el);
    sections.forEach((section, index) => {
      const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
          section.classList.add('aos-animate');
          if (section.id === 'kilasdata' && !counterStarted) {
            runCounter();
            counterStarted = true;
          }
        }
      }, {
        threshold: 0.15
      });
      observer.observe(section);
    });
    window.addEventListener('load', () => {
      AOS.init({
        duration: 800,
        once: true
      });
    });
    const counters = document.querySelectorAll('.counter');
    let counterStarted = false;

    function runCounter() {
      counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const isDecimal = counter.getAttribute('data-target').includes(".");
        const steps = 300;
        let count = 0;
        let increment = target / steps;
        const update = () => {
          count += increment;
          if (count < target) {
            counter.innerText = isDecimal ? count.toFixed(2) : Math.floor(count);
            requestAnimationFrame(update);
          } else {
            counter.innerText = isDecimal ? target.toFixed(2) : target;
          }
        };
        update();
      });
    }

    function smoothScrollTo(targetY, duration = 4000) {
      const startY = window.scrollY;
      const distance = targetY - startY;
      let startTime = null;

      function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
      }

      function animation(currentTime) {
        if (!startTime) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        window.scrollTo(0, startY + distance * easeInOutCubic(progress));
        if (progress < 1) requestAnimationFrame(animation);
      }
      requestAnimationFrame(animation);
    }
    const observerOptions = {
      threshold: 0.15
    };
    sections.forEach((section) => {
      const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
          section.classList.add('aos-animate');
          if (section.id === 'kilasdata' && !counterStarted) {
            runCounter();
            counterStarted = true;
          }
        }
      }, {
        threshold: 0.15
      });
      observer.observe(section);
    });
    const contactItems = document.querySelectorAll('.contact-fade');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        } else {
          entry.target.classList.remove('visible');
        }
      });
    }, {
      threshold: 0.1
    });

    contactItems.forEach(item => {
      observer.observe(item);
    });
    AOS.init({
      duration: 800,
      once: true
    });
  </script>
</body>


</html>
