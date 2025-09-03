<?php

namespace App\Http\Controllers;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class OCRController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function uploadImage(Request $request)
    {
        // Validate the uploaded image
        $request->validate([
            'student_card' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'student_card.required' => 'Vui lòng tải lên thẻ sinh viên.',
            'student_card.image' => 'Tệp tải lên phải là hình ảnh.',
            'student_card.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg hoặc png.',
            'student_card.max' => 'Hình ảnh không được vượt quá 2MB.',
        ]);

        // Store the image
        $path = $request->file('student_card')->store('uploads', 'public');

        try {
            // Configure TesseractOCR
            $tesseractPath = env('TESSERACT_PATH', 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            $text = (new TesseractOCR(storage_path("app/public/{$path}")))
                ->executable($tesseractPath)
                ->lang('vie')
                ->run();
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Không thể đọc ảnh. Vui lòng đảm bảo ảnh rõ nét và là thẻ sinh viên.')->withInput();
        }

        // Parse data from OCR
        $data = [
            'name'   => $this->extractField($text, '/Họ tên\s*[:]*\s*(.+?)(?=\s{2,}|$)/i') ?? '',
            'dob'    => $this->extractField($text, '/Ngày sinh\s*[:]*\s*(\d{1,2}[-\/.]\d{1,2}[-\/.]\d{4})/i') ?? '',
            'class'  => $this->extractField($text, '/Lớp\s*[:]*\s*([A-Z0-9\-]+)/i') ?? '',
            'major'  => $this->extractField($text, '/Ngành\s*[:]*\s*(.+?)(?=\s{2,}|$)/i') ?? '',
            'course' => $this->extractField($text, '/Khóa\s*[:]*\s*(\d{2,4}[-\/]?\d{2,4}|\w+)/i') ?? '',
            'mssv'   => $this->extractField($text, '/MSSV\s*[:]*\s*(\d{4,10})/i') ?? '',
        ];

        // Check if all fields are empty
        if (empty($data['name']) && empty($data['dob']) && empty($data['class']) && empty($data['major']) && empty($data['course']) && empty($data['mssv'])) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Ảnh không hợp lệ hoặc không chứa thông tin thẻ sinh viên cần thiết.')->withInput();
        }

        return view('auth.register', ['data' => $data, 'image' => $path]);
    }

    public function submit(Request $request)
    {
        // Validate form input with custom error messages
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'dob'                   => 'required|string|regex:/^\d{1,2}[-\/.]\d{1,2}[-\/.]\d{4}$/',
            'class'                 => 'required|string|max:50',
            'major'                 => 'required|string|max:255',
            'course'                => 'required|string|max:20',
            'mssv'                  => 'required|string|max:20|unique:users,student_id',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'name.required'                => 'Vui lòng nhập họ tên.',
            'name.max'                    => 'Họ tên không được vượt quá 255 ký tự.',
            'dob.required'                => 'Vui lòng nhập ngày sinh.',
            'dob.regex'                   => 'Ngày sinh phải có định dạng dd/mm/yyyy hoặc dd-mm-yyyy.',
            'class.required'              => 'Vui lòng nhập lớp.',
            'class.max'                   => 'Lớp không được vượt quá 50 ký tự.',
            'major.required'              => 'Vui lòng nhập ngành học.',
            'major.max'                   => 'Ngành học không được vượt quá 255 ký tự.',
            'course.required'             => 'Vui lòng nhập khóa học.',
            'course.max'                  => 'Khóa học không được vượt quá 20 ký tự.',
            'mssv.required'               => 'Vui lòng nhập MSSV.',
            'mssv.max'                    => 'MSSV không được vượt quá 20 ký tự.',
            'mssv.unique'                 => 'MSSV đã được sử dụng.',
            'email.required'              => 'Vui lòng nhập email.',
            'email.email'                 => 'Email không hợp lệ.',
            'email.max'                   => 'Email không được vượt quá 255 ký tự.',
            'email.unique'                => 'Email đã được sử dụng.',
            'password.required'           => 'Vui lòng nhập mật khẩu.',
            'password.min'                => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed'          => 'Mật khẩu xác nhận không khớp.',
            'password_confirmation.required' => 'Vui lòng nhập xác nhận mật khẩu.',
        ]);

        // Normalize and validate date of birth
        try {
            $dobInput = trim(str_replace(['-', '.'], '/', $validated['dob']));
            $dob = Carbon::createFromFormat('d/m/Y', $dobInput)->format('Y-m-d');
        } catch (\Exception $e) {
            return back()->withErrors(['dob' => 'Ngày sinh không hợp lệ, vui lòng nhập dạng dd/mm/yyyy.'])
                ->withInput();
        }

        // Create new user
        try {
            $user = User::create([
                'name'       => $validated['name'],
                'dob'        => $dob,
                'class'      => $validated['class'],
                'major'      => $validated['major'],
                'course'     => $validated['course'],
                'student_id' => $validated['mssv'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
            ]);

            // Delete the uploaded image after successful registration
            if ($request->has('image') && Storage::disk('public')->exists($request->image)) {
                Storage::disk('public')->delete($request->image);
            }

            // Redirect to login page with success message
            return redirect()->route('login')->with('success', 'Đăng ký thành công cho MSSV: ' . $user->student_id . '. Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            // Handle general errors and return to previous page with input
            return redirect()->route('auth.register')->withErrors(['general' => 'Lỗi khi tạo tài khoản. Vui lòng thử lại.'])->withInput();
        }
    }

    private function extractField($text, $pattern)
    {
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
