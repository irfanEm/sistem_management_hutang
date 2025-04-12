      <!-- Konten Utama -->
      <div class="col-md-9 order-md-last">
            <!-- pesan error jika ada -->
            <?php if(isset($model['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                    <strong><?= $model['error'] ?></strong>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
            <?php } ?>

        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <!-- Judul dan Tombol Kembali -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="card-title fw-bold text-primary"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h4>
              <a href="/admin/master/users" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
              </a>
            </div>

            <!-- Form Tambah User -->
            <form action="/admin/master/users/tambah" method="post">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" id="nama" placeholder="Masukkan nama" required>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Masukkan username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
              </div>
              <div class="mb-3">
                <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_konfirmation" class="form-control" id="konfirmasi_password" placeholder="Konfirmasi password" required>
              </div>
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" id="role" required>
                  <option value="">Pilih role</option>
                  <option value="Admin">Admin</option>
                  <option value="Ustadz">Ustadz</option>
                  <option value="Santri">Santri</option>
                </select>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>