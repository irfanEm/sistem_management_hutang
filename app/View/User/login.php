<!-- baru -->
<style>
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

     /* Tambahkan CSS ini */
     .alert-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        max-width: 400px;
        z-index: 1000;
      }
  
      .alert {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
      }
  
      /* Media query untuk mobile */
      @media (max-width: 576px) {
        .alert-container {
          left: 1rem;
          right: 1rem;
          max-width: 100%;
        }
      }

  </style>
<!-- pesan error jika ada -->
<?php if(isset($model['error'])) { ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
          <strong><?= $model['error'] ?></strong>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<!-- Card Login -->
<div class="card login-card">

    <div class="card-header">
      <h3 class="fw-bold">Login Admin</h3>
      <p class="text-muted">Silakan masuk untuk mengakses dashboard</p>
    </div>
    <div class="card-body">
      <form action="/users/login" method="post">
        <!-- Input Email -->
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="username" name="username" class="form-control" id="username" placeholder="Masukkan username" required>
        </div>

        <!-- Input Password -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
        </div>

        <!-- Tombol Login -->
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>

        <!-- Link Register -->
        <div class="text-muted mt-3">
          Belum punya akun? <a href="/users/register">Daftar di sini</a>
        </div>
      </form>
    </div>
  </div>