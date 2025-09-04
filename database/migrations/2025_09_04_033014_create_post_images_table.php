<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // liên kết bài viết
            $table->string('image_path'); // đường dẫn ảnh
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_images');
    }
};
