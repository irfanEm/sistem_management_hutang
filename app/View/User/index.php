<?php
  $flashMessage = $_SESSION['flash_message'] ?? null;
  unset($_SESSION['flash_message']);
?>
      <!-- Konten Utama -->
      <div class="col-md-9 order-md-last">
            <!-- pesan error jika ada -->
            <?php if(isset($model['error'])) { ?>
              <div class="alert alert-<?=$model['status']?> alert-dismissible" role="alert">
                    <strong><?= $model['error'] ?></strong>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
            <?php } ?>
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <!-- Judul dan Tombol Tambah User -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="card-title fw-bold text-primary"><i class="fas fa-user me-2"></i>Data User</h4>
              <a href="/admin/master/users/tambah" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah User
              </a>
            </div>

            <!-- Search Bar -->
            <div class="mb-4">
              <form>
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Cari user..." aria-label="Cari user">
                  <button class="btn btn-outline-primary" type="button">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
            </div>

            <!-- Tabel Data User -->
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $a = 1; foreach($model['users'] as $user) : ?>
                  <tr>
                    <td><?=$a ?></td>
                    <td><?=$user['name'] ?></td>
                    <td><?=$user['username'] ?></td>
                    <td><?=$user['role'] ?></td>
                    <td>
                      <a href="/admin/master/user/detail/<?= $user['user_id'] ?>" class="btn btn-sm btn-primary me-2 rounded-5 mb-1 mb-md-0">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="/admin/master/user/ubah/<?= $user['user_id'] ?>" class="btn btn-sm btn-warning me-2 rounded-5 mb-1 mb-md-0">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="/admin/master/user/hapus/<?= $user['user_id'] ?>" class="btn btn-sm btn-danger rounded-5">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php $a++; endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
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