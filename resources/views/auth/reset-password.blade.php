<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h2>Đặt lại mật khẩu</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
            </div>

            <div>
                <label>Mật khẩu mới</label>
                <input type="password" name="password" required>
            </div>

            <div>
                <label>Nhập lại mật khẩu</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <button type="submit">Đặt lại mật khẩu</button>
        </form>
    </div>
</body>

</html>
