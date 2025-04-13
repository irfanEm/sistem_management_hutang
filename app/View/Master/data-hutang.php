      <!-- Konten Utama -->
      <div class="col-md-9 order-md-last">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <!-- Header dengan Tombol Tambah -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="card-title fw-bold text-primary">
                <i class="bi bi-journal-text me-2"></i>Data Hutang
              </h4>
              <a href="/debts/create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Hutang
              </a>
            </div>
            
            <!-- Filter dan Pencarian -->
            <div class="row mb-4">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Cari berdasarkan nama agen...">
                  <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Tabel Data Hutang -->
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 5%">No</th>
                    <th>
                      <a href="#" class="text-decoration-none text-dark">
                        Nama Agen <i class="bi bi-arrow-down-up"></i>
                      </a>
                    </th>
                    <th>
                      <a href="#" class="text-decoration-none text-dark">
                        Tgl Hutang <i class="bi bi-arrow-down-up"></i>
                      </a>
                    </th>
                    <th>
                      <a href="#" class="text-decoration-none text-dark">
                        Tgl Bayar <i class="bi bi-arrow-down-up"></i>
                      </a>
                    </th>
                    <th>Pembayaran Via</th>
                    <th>Sisa Hutang</th>
                    <th style="width: 15%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Contoh Data -->
                  <tr>
                    <td>1</td>
                    <td>Agen Baju Mandiri</td>
                    <td>05 Jan 2024</td>
                    <td>05 Feb 2024</td>
                    <td><span class="badge bg-primary">Transfer Bank</span></td>
                    <td class="fw-bold text-danger">Rp2.000.000</td>
                    <td>
                      <a href="/debts/edit/1" class="btn btn-sm btn-warning me-2">
                        <i class="bi bi-pencil-square"></i>
                      </a>
                      <a href="/debts/delete/1" class="btn btn-sm btn-danger" 
                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Toko Elektronik Sejahtera</td>
                    <td>15 Des 2023</td>
                    <td>15 Jan 2024</td>
                    <td><span class="badge bg-success">Cash</span></td>
                    <td class="fw-bold text-danger">Rp5.000.000</td>
                    <td>
                      <a href="/debts/edit/2" class="btn btn-sm btn-warning me-2">
                        <i class="bi bi-pencil-square"></i>
                      </a>
                      <a href="/debts/delete/2" class="btn btn-sm btn-danger" 
                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#">Next</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>