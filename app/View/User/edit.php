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
              <h4 class="card-title fw-bold text-primary"><i class="fas fa-user-plus me-2"></i>Edit User</h4>
              <a href="/admin/master/users" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
              </a>
            </div>

            <!-- Form Tambah User -->
            <form action="/admin/master/users/ubah" method="post">
              <div class="mb-3">
                <input type="hidden" name="user_id" value="<?= $model['user_id'] ?>">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" id="nama" value="<?= $model['nama'] ?>" >
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" value="<?= $model['username'] ?>" >
              </div>
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" id="role" required>
                  <?php $roles = ["Admin", "Ustadz", "Santri"]; ?>
                  <option value="">Pilih role</option>
                  <?php foreach($roles as $role) : ?>
                    <option value="<?=$role?>" <?= $role == $model['role'] ? 'selected' : '' ?>><?=$role?></option>
                  <?php endforeach ?>
                </select>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Edit
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>