<!-- Baru -->
       <!-- Navbar -->
       <nav
      class="navbar navbar-expand-lg navbar-dark shadow-sm"
      style="background: linear-gradient(135deg, #667eea, #764ba2)"
    >
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
          <i class="bi bi-cash-coin me-2"></i>Debt Management System
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link active" href="/debts"
                ><i class="bi bi-speedometer2 me-1"></i>Dashboard</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/reports"
                ><i class="bi bi-file-earmark-bar-graph me-1"></i>Laporan</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/logout"
                ><i class="bi bi-box-arrow-right me-1"></i>Logout</a
              >
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
              <h5 class="card-title fw-bold text-primary mb-3">
                <i class="bi bi-menu-button me-2"></i>Menu Utama
              </h5>
              <ul class="list-group list-group-flush">
                <!-- Dashboard -->
                <li class="list-group-item border-0 mb-2">
                  <a href="/debts" class="text-decoration-none text-dark">
                    <i class="bi bi-house-door me-2"></i>Dashboard
                  </a>
                </li>

                <!-- Data Master -->
                <li class="list-group-item border-0 mb-2">
                  <a
                    href="#masterMenu"
                    class="text-decoration-none text-dark d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                  >
                    <span>
                      <i class="bi bi-database me-2"></i>Data Master
                    </span>
                    <i class="bi bi-chevron-down"></i>
                  </a>
                  <ul
                    class="list-group list-group-flush collapse"
                    id="masterMenu"
                  >
                    <li class="list-group-item border-0 mb-2">
                      <a
                        href="/debts"
                        class="text-decoration-none text-dark ps-4"
                      >
                        <i class="bi bi-journal-text me-2"></i>Data Hutang
                      </a>
                    </li>
                    <li class="list-group-item border-0 mb-2">
                      <a
                        href="/agents"
                        class="text-decoration-none text-dark ps-4"
                      >
                        <i class="bi bi-people me-2"></i>Daftar Agen
                      </a>
                    </li>
                    <li class="list-group-item border-0 mb-2">
                      <a
                        href="/payment-methods"
                        class="text-decoration-none text-dark ps-4"
                      >
                        <i class="bi bi-credit-card me-2"></i>Metode Pembayaran
                      </a>
                    </li>
                  </ul>
                </li>

                <!-- Laporan -->
                <li class="list-group-item border-0 mb-2">
                  <a href="/reports" class="text-decoration-none text-dark">
                    <i class="bi bi-pie-chart me-2"></i>Laporan
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

