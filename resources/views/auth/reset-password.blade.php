<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đặt lại mật khẩu</title>
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
        h2 {
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
        .input-box input:read-only {
            background: #e8e8e8;
            cursor: not-allowed;
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đặt lại mật khẩu</h2>

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

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="input-box">
                <input type="email" name="email" id="email" placeholder=" " value="{{ $email ?? old('email') }}" required readonly>
                <label for="email">Email</label>
            </div>

            <div class="input-box">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Mật khẩu mới</label>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" " required>
                <label for="password_confirmation">Nhập lại mật khẩu</label>
            </div>

            <button type="submit" class="btn">Đặt lại mật khẩu</button>
        </form>
    </div>
</body>
</html>