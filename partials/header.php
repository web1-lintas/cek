<header class="header">
  <div class="logo-group">
    <img src="/assets/jogja.png" alt="Logo Jogja">
    <img src="/assets/logo.png" alt="Logo DPUPKP">
  </div>
  <div class="title-group">
    <h1>SITIJO</h1>
    <span>Sistem Informasi Infrastruktur Kota Yogyakarta</span>
    <span class="subtext">Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman</span>
  </div>
  <?php if (isset($page) && $page === "peta"): ?>
    <div class="ms-auto">
      <a href="../index.php" class="btn-home">🏠 Beranda</a>
    </div>
  <?php endif; ?>
</header>

<style>
  :root {
    --primary: #003366;
    --secondary: #005B96;
    --accent: #f2b705;
    --text-dark: #333333;
    --bg-light: #F4F6F8;
    --font-main: 'Roboto', 'Inter', sans-serif;
  }

  .header {
    height: 80px;
    background: var(--primary);
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0 30px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    font-family: var(--font-main);
  }

  .logo-group {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo-group img {
    height: 50px;
    object-fit: contain;
  }

  .title-group {
    padding-left: 12px;
    display: flex;
    flex-direction: column;
    line-height: 1.3;
  }

  .title-group h1 {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: 1px;
    margin: 0;
    text-transform: uppercase;
    color: #fff;
  }

  .title-group span {
    font-size: 13px;
    font-weight: 500;
    color: #dbe7f3;
  }

  .title-group .subtext {
    font-size: 11px;
    color: #cbd8e5;
  }

  .btn-home {
    background-color: var(--accent);
    color: var(--primary);
    font-weight: 600;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    margin-left: auto;
  }

  .btn-home:hover {
    background-color: #e6ad00;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
  }

  @media (max-width: 1024px) {
    .title-group h1 {
      font-size: 22px;
    }

    .title-group span {
      font-size: 12px;
    }
  }

  @media (max-width: 768px) {
    .header {
      flex-direction: column;
      height: auto;
      padding: 12px;
      text-align: center;
    }

    .logo-group {
      justify-content: center;
    }

    .btn-home {
      margin-top: 8px;
    }
  }

  @media (max-width: 480px) {
    .logo-group img {
      height: 38px;
    }

    .title-group h1 {
      font-size: 18px;
    }

    .title-group span {
      font-size: 10px;
    }

    .btn-home {
      font-size: 12px;
      padding: 5px 8px;
    }
  }

</style>
