<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}" dir="{{ $isRtl ?? false ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.login.title') }} - {{ __('admin.app_name') }}</title>
    @if($isRtl ?? false)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
            border-radius: 1rem 1rem 0 0;
            position: relative;
        }
        .login-header img {
            max-width: 180px;
            height: auto;
            margin-bottom: 0.5rem;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9 0%, #1f6dad 100%);
        }
        .lang-dropdown {
            position: absolute;
            top: 1rem;
            {{ $isRtl ?? false ? 'left' : 'right' }}: 1rem;
        }
        .lang-dropdown .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 0.25rem 0.75rem;
            font-size: 0.85rem;
        }
        .lang-dropdown .btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .lang-dropdown .dropdown-menu {
            min-width: 120px;
        }
        .lang-dropdown .dropdown-item.active {
            background-color: #3498db;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <!-- Language Dropdown -->
            <div class="dropdown lang-dropdown">
                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-globe"></i>
                    @switch($currentLocale ?? 'en')
                        @case('en')
                            EN
                            @break
                        @case('fr')
                            FR
                            @break
                        @case('ar')
                            AR
                            @break
                    @endswitch
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">
                            English
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'fr' ? 'active' : '' }}" href="{{ route('locale.switch', 'fr') }}">
                            Français
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'ar' ? 'active' : '' }}" href="{{ route('locale.switch', 'ar') }}">
                            العربية
                        </a>
                    </li>
                </ul>
            </div>

            <img src="{{ asset('images/logo.png') }}" alt="{{ __('admin.app_name') }}" class="d-block mx-auto">
            <small>{{ __('admin.login.subtitle') }}</small>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('admin.login.email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('admin.login.password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">{{ __('admin.login.remember') }}</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-{{ $isRtl ?? false ? 'left' : 'right' }}"></i> {{ __('admin.login.submit') }}
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
