<?php
if (isset($_POST['login'])) {
    header('Location: /CCRO-Request/AdminDashboard/dashboard/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background: url('images/background.png') no-repeat center center fixed; background-size: cover; margin: 0; padding: 0; }
    .login-container { min-height: 100vh; display: flex; justify-content: center; align-items: center; background-color: rgba(0, 0, 0, 0.5); }
    .login-box { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); width: 100%; max-width: 400px; text-align: center; }
    .login-box img { height: 80px; margin: 0 10px; }
    .login-box h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; }
    .form-control { border-radius: 5px; height: 45px; }
    .btn-primary { background-color: #007bff; border: none; border-radius: 5px; height: 45px; font-size: 1rem; font-weight: 600; }
    .btn-primary:hover { background-color: #0056b3; }
    .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .forgot-password { font-size: 0.9rem; color: #007bff; text-decoration: none; }
    .forgot-password:hover { text-decoration: underline; }
    .alert { border-radius: 5px; margin-bottom: 20px; }
    .loading { display: none; }
    .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
    .input-group { position: relative; }
    .input-group .form-control { padding-left: 45px; }
    .input-group .input-group-text { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #6c757d; z-index: 10; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="d-flex justify-content-center mb-4">
        <img src="images/Logo 1.png" alt="Logo 1">
        <img src="images/Logo 2.png" alt="Logo 2">
      </div>
      <h2>Admin Login</h2>
      <p>Sign in to start your session</p>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <i class='bx bx-error-circle'></i>
          <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
          <i class='bx bx-check-circle'></i>
          <?= htmlspecialchars($success_message) ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="POST" action="">
        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text">
              <i class='bx bx-user'></i>
            </span>
            <input type="text" name="username" class="form-control" placeholder="Username" required 
                   autocomplete="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text">
              <i class='bx bx-lock-alt'></i>
            </span>
            <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
            <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; z-index: 10;">
              <i class='bx bx-show'></i>
            </button>
          </div>
        </div>
        <div class="mb-3 form-check d-flex align-items-center justify-content-start">
          <input type="checkbox" name="remember_me" class="form-check-input me-2" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Remember Me</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100" id="loginBtn">
          <!-- <h5 class="normal">Sign In</h5> -->
          <span class="loading">
            <i class='bx bx-loader-alt bx-spin'></i>
            Signing In...
          </span>
        </button>
      </form>
      <div class="mt-3 text-muted">
        <small>
          <a href="../index.php" class="text-decoration-none">
            <i class='bx bx-arrow-back'></i>
            Back to Main Site
          </a>
        </small>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const loginBtn = document.getElementById('loginBtn');
      const loading = loginBtn.querySelector('.loading');
      const normal = loginBtn.querySelector('.normal');
      loading.style.display = 'inline';
      normal.style.display = 'none';
      loginBtn.disabled = true;
      return true;
    });
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.querySelector('input[name="password"]');
      const icon = this.querySelector('i');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
      }
    });
    setTimeout(() => {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
        }, 500);
      });
    }, 5000);
  </script>
</body>
</html>
