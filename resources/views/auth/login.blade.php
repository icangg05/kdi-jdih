<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - JDIH Kota Kendari</title>

  <!-- Font Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      font-size: 0.92rem;
      background: url('{{ asset("assets/img/bg-login.jpg") }}') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
      max-width: 400px;
      width: 100%;
      border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .login-logo img {
      max-width: 160px;
      margin-bottom: 1.2rem;
    }

    h4 {
      font-size: 1.1rem;
      font-weight: 600;
      color: #fff;
    }

    label {
      font-weight: 500;
      color: #fff;
    }

    .form-control {
      font-size: 0.9rem;
      background-color: rgba(255, 255, 255, 0.8);
      border: none;
    }

    .form-control:focus {
      border-color: #FF891E;
      box-shadow: 0 0 0 0.2rem rgba(255, 137, 30, 0.25);
    }

    .btn-primary {
      background-color: #FF891E;
      border-color: #FF891E;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .btn-primary:hover {
      background-color: #e67b1a;
      border-color: #e67b1a;
    }

    .text-danger {
      font-size: 0.85rem;
      color: #ffd1c0;
    }

    .form-check-label {
      color: #f1f1f1;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <div class="login-logo text-center">
      <a href="{{ route('frontend.beranda') }}">
        <img src="{{ asset('assets/img/jdih-logo.png') }}" alt="Logo JDIH">
      </a>
    </div>
    <h4 class="text-center mb-3">Silakan Login</h4>
    <form action="{{ route('authenticate') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" required autofocus value="{{ old('username') }}">
        @error('username')
        <small class="text-white mt-2">{{ $message }}</small>
        @enderror
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
        @error('password')
        <small class="text-white mt-2">{{ $message }}</small>
        @enderror
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe" value="1">
        <label class="form-check-label" for="rememberMe">Remember Me</label>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Sign In</button>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
