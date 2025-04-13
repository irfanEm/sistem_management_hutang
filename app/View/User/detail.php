      <!-- Konten Utama -->
      <div class="col-md-9 order-md-last">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <!-- Judul dan Tombol Kembali -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="card-title fw-bold text-primary"><i class="fas fa-user me-2"></i>Detail User</h4>
              <a href="/admin/master/users" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
              </a>
            </div>

            <!-- Informasi Detail User -->
            <div class="row">
              <div class="col-md-4">
                <!-- Foto Profil -->
                <div class="text-center mb-4">
                  <img src="<?= asset('assets/images/muslim-avatar.png') ?>" class="img-fluid rounded-circle" alt="Foto Profil">
                </div>
              </div>
              <div class="col-md-8">
                <!-- Data User -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Nama:</label>
                  <p><?=$model['nama']?></p>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Username / Email:</label>
                  <p><?=$model['username']?></p>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Role:</label>
                  <p><?=$model['role']?></p>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Tanggal Bergabung:</label>
                  <p><?=$model['created_at']?></p>
                </div>
              </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-end mt-4">
              <a href="/admin/master/user/ubah/<?= $model['user_id'] ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
              </a>
              <a href="/admin/master/user/hapus/<?= $model['user_id'] ?>" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Hapus
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>