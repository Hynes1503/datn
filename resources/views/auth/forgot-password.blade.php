<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { font-size: 28px; margin-bottom: 20px; }
        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: #000;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover { background: #222; }
        .back-link { margin-top: 15px; display: block; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quên mật khẩu</h1>

        @if (session('status'))
            <p style="color: green;">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <ul style="color: red; text-align:left;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('password.send') }}">
            @csrf
            <input type="text" name="login" placeholder="Nhập email hoặc MSSV" required value="{{ old('login') }}">
            <button type="submit">Gửi link đặt lại mật khẩu</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Quay lại đăng nhập</a>
    </div>
</body>
</html>
