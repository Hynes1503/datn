<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
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
            margin-bottom: 30px;
            color: #000;
            letter-spacing: 1.2px;
        }

        .input-box,
        .upload-box {
            margin-bottom: 25px;
            position: relative;
            text-align: center;
        }

        .input-box input,
        .upload-box input[type="file"] {
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

        .input-box input:focus,
        .upload-box input[type="file"]:focus {
            border-color: #000;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .input-box label,
        .upload-box label {
            position: absolute;
            top: 50%;
            left: 40px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #666;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-box input:focus+label,
        .input-box input:not(:placeholder-shown)+label {
            top: -10px;
            left: 40px;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 0 5px;
            transform: translateY(0);
        }

        .upload-box label {
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

        .back {
            display: block;
            margin-top: 15px;
            color: #000;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back:hover {
            color: #333;
            text-decoration: underline;
        }

        .preview {
            margin-top: 15px;
            max-width: 320px;
            max-height: 200px;
            /* Giới hạn chiều cao */
            border-radius: 12px;
            border: 1px solid #ccc;
            display: none;
            object-fit: contain;
            /* Đảm bảo ảnh nằm gọn trong khung */
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .error {
            background: #fff;
            color: #000;
            border: 1px solid #000;
            padding: 8px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: left;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }

        .error ul {
            margin: 0;
            padding-left: 20px;
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
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>EPUHub</h1>

        {{-- Thông báo lỗi --}}
        @if (session('error'))
            <div class="error">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bước 1: chỉ hiện upload --}}
        @if (!isset($data))
            <form action="{{ route('register.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="upload-box">
                    <input type="file" name="student_card" id="student_card" accept="image/*" required>
                    <label for="student_card">Ảnh thẻ sinh viên</label>
                    <img id="preview" class="preview" />
                </div>
                <button type="submit" class="btn">Tiếp tục</button>
            </form>
        @endif

        {{-- Bước 2: hiện form điền thông tin --}}
        @if (isset($data))
            <form action="{{ route('register.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="image" value="{{ isset($image) ? $image : '' }}">
                <div class="input-box">
                    <input type="text" name="name" id="name" placeholder=" "
                        value="{{ old('name', isset($data['name']) ? $data['name'] : '') }}" required>
                    <label for="name">Họ tên</label>
                </div>
                <div class="input-box">
                    <input type="text" name="dob" id="dob" placeholder=" "
                        value="{{ old('dob', isset($data['dob']) ? $data['dob'] : '') }}" required>
                    <label for="dob">Ngày sinh (dd/mm/yyyy)</label>
                </div>
                <div class="input-box">
                    <input type="text" name="class" id="class" placeholder=" "
                        value="{{ old('class', isset($data['class']) ? $data['class'] : '') }}" required>
                    <label for="class">Lớp</label>
                </div>
                <div class="input-box">
                    <input type="text" name="major" id="major" placeholder=" "
                        value="{{ old('major', isset($data['major']) ? $data['major'] : '') }}" required>
                    <label for="major">Ngành học</label>
                </div>
                <div class="input-box">
                    <input type="text" name="course" id="course" placeholder=" "
                        value="{{ old('course', isset($data['course']) ? $data['course'] : '') }}" required>
                    <label for="course">Khóa học</label>
                </div>
                <div class="input-box">
                    <input type="text" name="mssv" id="mssv" placeholder=" "
                        value="{{ old('mssv', isset($data['mssv']) ? $data['mssv'] : '') }}" required>
                    <label for="mssv">MSSV</label>
                </div>
                <div class="input-box">
                    <input type="email" name="email" id="email" placeholder=" " value="{{ old('email') }}"
                        required>
                    <label for="email">Email</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" placeholder=" " required>
                    <label for="password">Mật khẩu</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" "
                        required>
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                </div>
                <button type="submit" class="btn">Đăng ký</button>
            </form>

            {{-- Nút quay lại bước 1 --}}
            <a href="{{ route('auth.register') }}" class="back">← Quay lại</a>
        @endif

        <div class="signup-box">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>

    <script>
        const input = document.getElementById("student_card");
        const preview = document.getElementById("preview");

        if (input) {
            input.addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            });
        }
    </script>
</body>

</html>
