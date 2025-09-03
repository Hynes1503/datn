<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // Họ tên
            $table->date('dob');                   // Ngày sinh
            $table->string('class');               // Lớp
            $table->string('major');               // Ngành
            $table->string('course');              // Khóa học
            $table->string('student_id')->unique();// MSSV
            $table->string('avatar')->nullable();  // Ảnh đại diện
            $table->string('email')->unique();     // Email trường (vd: MSSV@epu.edu.vn)
            $table->string('password');            // Mật khẩu (hash)
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
