<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người đăng
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->string('hashtag')->nullable();  // hashtag lưu chuỗi (vd: #laravel,#php)
            
            // Thống kê
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->unsignedBigInteger('views')->default(0);
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
