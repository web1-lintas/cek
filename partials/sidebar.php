<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
  }

  .sidebar {
    position: fixed;
    top: 85px;
    left: 10px;
    width: 55px;
    height: calc(100vh - 95px);
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(8px);
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    padding: 0;
    display: flex;
    flex-direction: column;
    z-index: 999;
    transition: width 0.3s ease;
    overflow-y: auto;
  }

  .sidebar:hover {
    width: 187px;
  }

  .sidebar a {
    position: relative;
    display: flex;
    align-items: center;
    color: #213878;
    font-weight: bold;
    padding: 15px 15px;
    text-decoration: none;
    font-size: 13px;
    transition: background 0.2s;
  }

  .sidebar a:hover {
    background: rgba(13, 71, 161, 0.1);
  }

  .sidebar i {
    font-size: 20px;
    min-width: 25px;
    text-align: center;
  }

  .sidebar a i {
    color: #0d47a1 !important;
  }

  .sidebar:not(:hover) a span {
    display: none;
  }

  .sidebar:not(:hover) a:hover::after {
    content: attr(data-label);
    position: absolute;
    left: 75px;
    background: #333333;
    color: #ffffff;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
    padding: 2px 6px;
  }

  .submenu {
    display: none;
    flex-direction: column;
    background-color: transparent;
    margin-left: 10px;
    margin-top: -5px;
    overflow: hidden;
    transition: all 0.25s ease;
  }

  .submenu a {
    font-size: 13px;
    font-weight: 600;
    padding: 10px 18px;
    color: #213878;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .submenu a:hover {
    background: rgba(13, 71, 161, 0.1);
    padding-left: 22px;
  }

  .submenu.show {
    display: flex;
    animation: fadeIn 0.3s ease forwards;
  }

  .burger-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    background: #FFCC00;
    border: none;
    padding: 8px 12px;
    font-size: 20px;
    border-radius: 15px;
    z-index: 1000;
    cursor: pointer;
    display: none;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-4px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 576px) {
    .sidebar {
      top: 130px;
      left: -210px;
      bottom: -50px;
      height: 700px;
      border-radius: 0 12px 12px 0;
      transition: left 0.3s ease;
    }

    .sidebar.show {
      left: 0;
    }

    .burger-btn {
      margin-top: 55px;
      margin-left: -7px;
      display: block;
    }
  }
</style>

<button class="burger-btn d-md-none" onclick="toggleSidebar()">☰</button>
<div class="sidebar" id="sidebar">
  <a href="/index.php" data-label="Beranda"><i class="bi bi-house-door-fill"></i><span> Beranda</span></a>
  <?php if (isset($_SESSION['user'])): ?>
    <a href="/frontend/jenisdata.php" data-label="Jenis Data"><i class="bi bi-database-fill"></i><span> Jenis Data</span></a>
  <?php else: ?>
    <a href="#" data-bs-toggle="modal" data-bs-target="#signinModal" data-label="Jenis Data">
      <i class="bi bi-database-fill"></i><span> Jenis Data</span>
    </a>
  <?php endif; ?>
  <a href="#" data-label="Peta" id="petaLink">
  <i class="bi bi-map-fill"></i><span> Data Infrastruktur ▼</span>
</a>

<div class="submenu" id="submenuPeta">
  <?php if (isset($_SESSION['user'])): ?>
    <a href="/frontend/peta.php"><i class="bi bi-geo-alt-fill"></i> Peta Infrastruktur</a>
    <a href="/backend/data/update_data.php"><i class="bi bi-pencil-square"></i> Update Data</a>
    <a href="/backend/data/tambah_data.php"><i class="bi bi-plus-circle-fill"></i> Tambah Data</a>
    <a href="/backend/data/download_data.php"><i class="bi bi-download"></i> Download Data</a>
  <?php else: ?>
    <a href="#" data-bs-toggle="modal" data-bs-target="#signinModal"><i class="bi bi-geo-alt-fill"></i> Peta Infrastruktur</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#signinModal"><i class="bi bi-pencil-square"></i> Update Data</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#signinModal"><i class="bi bi-plus-circle-fill"></i> Tambah Data</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#signinModal"><i class="bi bi-download"></i> Download Data</a>
  <?php endif; ?>
</div>
  <a href="/frontend/panduan.php" data-label="Panduan Penggunaan">
    <i class="bi bi-book-fill"></i><span> Panduan</span>
  </a>
  <?php if (isset($_SESSION['user'])): ?>
    <a href="/backend/auth/signout.php" data-label="Sign Out"><i class="bi bi-box-arrow-right"></i><span> Sign Out</span></a>
  <?php else: ?>
    <a href="/backend/auth/signin.php" data-label="Sign In"><i class="bi bi-box-arrow-in-right"></i><span> Sign In</span></a>
  <?php endif; ?>
</div>

<?php if (!isset($_SESSION['user'])): ?>
  <div class="modal fade" id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="signinModalLabel">
            <i class="bi bi-info-circle-fill"></i> Informasi
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body text-center">
          <p>Silakan Anda melakukan <b>Sign In</b> untuk mengakses fitur ini.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <a href="backend/auth/signin.php?redirect=/index.php" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
  }
  document.getElementById('petaLink').addEventListener('click', function(event) {
    event.preventDefault();
    const submenu = document.getElementById('submenuPeta');
    const arrow = this.querySelector('span');
    const isOpen = submenu.classList.toggle('show');
    arrow.innerHTML = isOpen ? ' Peta ▲' : ' Peta ▼';
  });
  document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const submenu = document.getElementById('submenuPeta');
    const arrow = document.querySelector('#petaLink span');
    if (!sidebar.contains(e.target)) {
      submenu.classList.remove('show');
      arrow.innerHTML = ' Peta ▼';
    }
  });
  document.querySelectorAll('#submenuPeta a').forEach(link => {
    link.addEventListener('click', () => {
      document.getElementById('submenuPeta').classList.remove('show');
      document.querySelector('#petaLink span').innerHTML = ' Peta ▼';
    });
  });

</script>
