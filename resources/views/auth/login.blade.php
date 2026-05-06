<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Attendance Management System</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --card-radius: 24px;
            --input-radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            background-image: url("{{ asset('assets/images/login-bg.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
        }

        /* Overlay for background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(8px);
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 48px 40px 10px;
            text-align: center;
        }

        .logo-box {
            width: 72px;
            height: 72px;
            background: var(--primary-color);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: white;
            font-size: 2rem;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .card-header h3 {
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .card-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .card-body {
            padding: 20px 48px 48px;
        }

        .form-group label {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            z-index: 10;
        }

        .form-control {
            height: 52px;
            border-radius: var(--input-radius);
            border: 1px solid #e5e7eb;
            padding: 10px 16px 10px 48px;
            font-size: 1rem;
            color: var(--text-main);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f9fafb;
        }

        .form-control:focus {
            background-color: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .custom-control-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            cursor: pointer;
            user-select: none;
            padding-top: 2px;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-submit {
            height: 52px;
            width: 100%;
            background: var(--primary-color);
            border: none;
            border-radius: var(--input-radius);
            color: white;
            font-size: 1rem;
            font-weight: 700;
            margin-top: 12px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2), 0 2px 4px -1px rgba(99, 102, 241, 0.1);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-custom {
            border-radius: var(--input-radius);
            padding: 12px 16px;
            font-size: 0.875rem;
            border: none;
            margin-bottom: 24px;
        }

        .alert-danger-custom {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        .alert-success-custom {
            background-color: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .card-body {
                padding: 20px 24px 32px;
            }
            .card-header {
                padding: 40px 24px 10px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="card-header">
            <div class="logo-box">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>Attendance Pro</h3>
            <p>Enter your credentials to access your account</p>
        </div>

        <div class="card-body">
            <!-- Laravel Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-custom alert-danger-custom">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-custom alert-success-custom">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="far fa-envelope"></i></span>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@company.com">
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                        <label class="custom-control-label" for="remember_me">Keep me signed in</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">
                    Sign In <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="text-center mt-4 position-relative" style="z-index: 2;">
        <p class="text-white-50 small">© {{ date('Y') }} Attendance Pro. All rights reserved.</p>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
