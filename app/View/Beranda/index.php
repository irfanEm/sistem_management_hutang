      <!-- Konten Utama -->
      <div class="col-md-9 order-md-last">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h4 class="card-title fw-bold text-primary mb-4">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard Hutang
              </h4>
              <hr class="mb-4" />

              <!-- Statistik Cepat -->
              <div class="row g-4">
                <div class="col-md-4">
                  <div
                    class="card text-white shadow-sm border-0"
                    style="
                      background: linear-gradient(135deg, #6a11cb, #2575fc);
                    "
                  >
                    <div class="card-body">
                      <h5 class="card-title fw-bold">
                        <i class="bi bi-currency-exchange me-2"></i>Total Hutang
                      </h5>
                      <p class="card-text display-6 fw-bold">Rp15.000.000</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div
                    class="card text-white shadow-sm border-0"
                    style="
                      background: linear-gradient(135deg, #00b09b, #96c93d);
                    "
                  >
                    <div class="card-body">
                      <h5 class="card-title fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Jatuh Tempo
                      </h5>
                      <p class="card-text display-6 fw-bold">5</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div
                    class="card text-white shadow-sm border-0"
                    style="
                      background: linear-gradient(135deg, #ff9a9e, #fad0c4);
                    "
                  >
                    <div class="card-body">
                      <h5 class="card-title fw-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Overdue
                      </h5>
                      <p class="card-text display-6 fw-bold">3</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabel Data Hutang Terbaru -->
              <div class="mt-5">
                <h5 class="fw-bold text-primary mb-3">
                  <i class="bi bi-table me-2"></i>Transaksi Terakhir
                </h5>
                <div class="table-responsive">
                  <table class="table table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Agen</th>
                        <th>Tanggal Hutang</th>
                        <th>Jatuh Tempo</th>
                        <th>Pembayaran Via</th>
                        <th>Sisa Hutang</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Agen Baju Mandiri</td>
                        <td>2024-01-05</td>
                        <td>2024-02-05</td>
                        <td>Transfer Bank</td>
                        <td>Rp2.000.000</td>
                        <td>
                          <span class="badge bg-warning">Akan Jatuh Tempo</span>
                        </td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>Toko Elektronik Sejahtera</td>
                        <td>2023-12-15</td>
                        <td>2024-01-15</td>
                        <td>Cash</td>
                        <td>Rp5.000.000</td>
                        <td><span class="badge bg-danger">Overdue</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
