<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - InventoryCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3E2723 0%, #5D4037 50%, #8D6E63 100%);
            padding: 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #5D4037, #8D6E63);
            color: #fff;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
        }
        .login-header .icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
        }
        .login-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: .25rem; }
        .login-header p { opacity: .8; font-size: .9rem; margin: 0; }
        .login-body { padding: 2rem; }
        .login-body .form-label { font-weight: 500; color: #444; font-size: .85rem; }
        .login-body .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: .65rem 1rem;
            font-size: .9rem;
        }
        .login-body .form-control:focus {
            border-color: #8D6E63;
            box-shadow: 0 0 0 3px rgba(141,110,99,.18);
        }
        .login-body .btn-login {
            background: linear-gradient(135deg, #5D4037, #8D6E63);
            border: none;
            border-radius: 10px;
            padding: .7rem;
            font-weight: 600;
            color: #fff;
            width: 100%;
            font-size: .95rem;
            transition: transform .15s;
        }
        .login-body .btn-login:hover { transform: translateY(-1px); }
        .login-footer {
            text-align: center;
            padding: 0 2rem 2rem;
            font-size: .8rem;
            color: #999;
        }
        .invalid-feedback { font-size: .8rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="bi bi-cup-hot-fill"></i></div>
            <h1>InventoryCafe</h1>
            <p>Sistem Manajemen Inventaris</p>
        </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="login-body">
                @error('email')
                    <div class="alert alert-danger py-2" style="font-size:.85rem;border-radius:10px">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="admin@inventorycafe.test" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.85rem">Ingat saya</label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </div>
        </form>
        <div class="login-footer">
            InventoryCafe &copy; {{ date('Y') }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
