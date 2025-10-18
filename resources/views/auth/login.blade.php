<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #000;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #000;
            letter-spacing: 1.2px;
        }
        .error-box {
            background: #ffe6e6;
            color: #d32f2f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #d32f2f;
            text-align: left;
        }
        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .input-box {
            margin-bottom: 30px;
            position: relative;
            text-align: center;
        }
        .input-box input {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            padding: 14px 12px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 16px;
            outline: none;
            background: #fafafa;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-box input:focus {
            border-color: #000;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }
        .input-box label {
            position: absolute;
            top: 50%;
            left: 40px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #666;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        .input-box input:focus + label,
        .input-box input:not(:placeholder-shown) + label {
            top: -10px;
            left: 40px;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 0 5px;
            transform: translateY(0);
        }
        .forgot-link {
            position: absolute;
            bottom: -18px;
            right: 25px;
            font-size: 13px;
            color: #000;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .forgot-link:hover {
            text-decoration: underline;
            color: #333;
        }
        .btn {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            background: #000;
            border: none;
            padding: 14px;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            display: block;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background: #1a1a1a;
            transform: scale(1.02);
        }
        .signup-box {
            margin-top: 20px;
            font-size: 15px;
            color: #333;
        }
        .signup-box a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .signup-box a:hover {
            color: #333;
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ehub</h1>

        @if (session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="input-box">
                <input type="text" name="login" id="login" placeholder=" " value="{{ old('login') }}" required>
                <label for="login">Email hoặc MSSV</label>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Mật khẩu</label>
                <a href="{{ route('password.form') }}" class="forgot-link">Quên mật khẩu?</a>
            </div>
            <button type="submit" class="btn">Đăng nhập</button>
        </form>

        <div class="signup-box">
            Chưa có tài khoản? <a href="{{ route('auth.register') }}">Đăng ký</a>
        </div>
    </div>
</body>
</html>
