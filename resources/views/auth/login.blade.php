<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – URL Shortener</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; background: #e8e8e8; }
        .login-wrapper { max-width: 320px; margin: 70px auto; padding: 0 12px; }
        .login-brand { text-align: center; margin-bottom: 18px; }
        .logo-box {
            display: inline-block; background: #e07b00; color: #fff;
            font-weight: bold; font-size: 10px; padding: 3px 7px;
            border-radius: 3px; letter-spacing: 1px; margin-bottom: 6px;
        }
        .login-brand h2 { font-size: 14px; color: #444; font-weight: bold; }
        .panel { background: #fff; border: 1px solid #d0d0d0; border-radius: 4px; }
        .panel-header {
            background: #f5f5f5; border-bottom: 1px solid #d0d0d0;
            padding: 8px 14px; border-radius: 4px 4px 0 0; font-weight: bold; font-size: 13px;
        }
        .panel-body { padding: 16px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; color: #555; margin-bottom: 3px; }
        .form-group input {
            width: 100%; padding: 6px 8px; border: 1px solid #ccc;
            border-radius: 3px; font-size: 12px; color: #333;
        }
        .form-group input:focus { outline: none; border-color: #4a90d9; }
        .form-group .placeholder-hint { font-size: 11px; color: #aaa; margin-top: 2px; }
        .btn-login {
            width: 100%; background: #4a90d9; border: 1px solid #357abd;
            color: #fff; padding: 7px; border-radius: 3px; font-size: 13px;
            font-weight: bold; cursor: pointer;
        }
        .btn-login:hover { background: #357abd; }
        .field-error { color: #a94442; font-size: 11px; margin-top: 3px; }
        .alert-error {
            background: #f2dede; color: #a94442; border: 1px solid #ebccd1;
            padding: 7px 10px; border-radius: 3px; margin-bottom: 10px; font-size: 12px;
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-brand">
        <div class="logo-box">URL</div>
        <h2>URL Shortener</h2>
    </div>

    <div class="panel">
        <div class="panel-header">Login Screen</div>
        <div class="panel-body">
            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error){{ $error }}<br>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('post.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="e.g. user@example.com"
                           required autofocus>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
