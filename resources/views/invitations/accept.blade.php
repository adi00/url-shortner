<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation – Sunback URL Shortener</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; background: #e8e8e8; }
        .wrapper { max-width: 380px; margin: 60px auto; padding: 0 12px; }
        .brand { text-align: center; margin-bottom: 16px; }
        .logo-box { display:inline-block; background:#e07b00; color:#fff; font-weight:bold; font-size:10px; padding:3px 7px; border-radius:3px; letter-spacing:1px; margin-bottom:6px; }
        .brand h2 { font-size:14px; color:#444; }
        .panel { background:#fff; border:1px solid #d0d0d0; border-radius:4px; }
        .panel-header { background:#f5f5f5; border-bottom:1px solid #d0d0d0; padding:8px 14px; border-radius:4px 4px 0 0; font-weight:bold; font-size:13px; }
        .panel-body { padding:16px; }
        .invite-info { background:#d9edf7; border:1px solid #bce8f1; color:#31708f; padding:8px 10px; border-radius:3px; font-size:12px; margin-bottom:14px; }
        .form-group { margin-bottom:11px; }
        .form-group label { display:block; font-size:12px; color:#555; font-weight:bold; margin-bottom:3px; }
        .form-group input { width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:3px; font-size:12px; }
        .form-group input:focus { outline:none; border-color:#4a90d9; }
        .form-group input[disabled] { background:#f5f5f5; color:#888; }
        .field-error { color:#a94442; font-size:11px; margin-top:3px; }
        .btn-submit { width:100%; background:#e07b00; border:1px solid #c06a00; color:#fff; padding:7px; border-radius:3px; font-size:13px; font-weight:bold; cursor:pointer; }
        .btn-submit:hover { background:#c96f00; }
        .alert-error { background:#f2dede; color:#a94442; border:1px solid #ebccd1; padding:7px 10px; border-radius:3px; margin-bottom:10px; font-size:12px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="brand">
        <div class="logo-box">URL</div>
        <h2> Shortener</h2>
    </div>
    <div class="panel">
        <div class="panel-header">Create Your Account</div>
        <div class="panel-body">
            <div class="invite-info">
                You've been invited to join <strong>{{ $invitation->company->name }}</strong>
                as <strong>{{ ucfirst($invitation->role) }}</strong>.
            </div>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $e){{ $e }}<br>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" value="{{ $invitation->email }}" disabled>
                </div>
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Full name">
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Min. 8 characters">
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password">
                </div>
                <button type="submit" class="btn-submit">Create Account & Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
