<!-- <nav class="navbar navbar-expand-lg border-bottom border-body shadow-sm">
  <div class="container-fluid"> -->
    <!-- Teks SIASHAF hanya untuk mobile view -->
    <!-- <a class="navbar-brand d-lg-none" href="/">SIASHAF</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01"> -->
      <!-- Teks Hidden brand tetap tampil pada layar besar -->
      <!-- <a class="navbar-brand d-none d-lg-block" href="/">SIASHAF</a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/admin/beranda">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Master</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/admin/master/users">User</a></li>
            <li><a class="dropdown-item" href="/admin/master/guru">Guru</a></li>
            <li><a class="dropdown-item" href="/admin/master/murid">Santri</a></li>
            <li><a class="dropdown-item" href="/admin/master/hafalan">Hafalan</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/admin/absensi" >Absensi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/admin/hafalan" >Hafalan</a>
        </li>
      </ul>
      <form class="d-flex" role="search">
        <a class="btn btn-danger fw-bold rounded-pill" href="/users/logout">Logout</a>
      </form>
    </div>
  </div>
</nav> -->

<!-- Baru -->
   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #667eea, #764ba2);">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="/admin/beranda">
        <i class="fas fa-book me-2"></i>Admin Absensi Hafalan
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="/admin/beranda"><i class="fas fa-home me-1"></i>Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-chart-line me-1"></i>Laporan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-cog me-1"></i>Pengaturan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/users/logout"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 mb-3 order-first">
        <div class="card shadow-sm border-0">
          <div class="card-body p-3">
            <h5 class="card-title fw-bold text-primary mb-3"><i class="fas fa-bars me-2"></i>Menu Admin</h5>
            <ul class="list-group list-group-flush">
              <!-- Dashboard -->
              <li class="list-group-item border-0 mb-2">
                <a href="/admin/beranda" class="text-decoration-none text-dark">
                  <i class="fas fa-home me-2"></i>Dashboard
                </a>
              </li>

              <!-- Master -->
              <li class="list-group-item border-0 mb-2">
                <a href="#masterMenu" class="text-decoration-none text-dark d-flex justify-content-between align-items-center" data-bs-toggle="collapse">
                  <span>
                    <i class="fas fa-database me-2"></i>Master
                  </span>
                  <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="list-group list-group-flush collapse" id="masterMenu">
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/master/users" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-user me-2"></i>Data User
                    </a>
                  </li>
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/master/guru" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-user-tie me-2"></i>Data Ustads
                    </a>
                  </li>
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/master/murid" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-users me-2"></i>Data Santri
                    </a>
                  </li>
                  <li class="list-group-item border-0 mb-2">
                    <a href="kelas-master.html" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-school me-2"></i>Data Kelas
                    </a>
                  </li>
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/master/hafalan" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-book me-2"></i>Data Hafalan
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Absensi -->
              <li class="list-group-item border-0 mb-2">
                <a href="#absensiMenu" class="text-decoration-none text-dark d-flex justify-content-between align-items-center" data-bs-toggle="collapse">
                  <span>
                    <i class="fas fa-clipboard-list me-2"></i>Absensi
                  </span>
                  <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="list-group list-group-flush collapse" id="absensiMenu">
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/absensi" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-user-check me-2"></i>Kehadiran
                    </a>
                  </li>
                  <li class="list-group-item border-0 mb-2">
                    <a href="/admin/hafalan" class="text-decoration-none text-dark ps-4">
                      <i class="fas fa-book-open me-2"></i>Hafalan
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Statistik -->
              <li class="list-group-item border-0 mb-2">
                <a href="statistik.html" class="text-decoration-none text-dark">
                  <i class="fas fa-chart-bar me-2"></i>Statistik
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

