<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // Người report
            $table->morphs('reportable'); // reportable_id + reportable_type (User/Post/Comment)
            $table->text('reason')->nullable(); // lý do báo cáo
            $table->string('status')->default('pending'); // pending, reviewed, resolved, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
