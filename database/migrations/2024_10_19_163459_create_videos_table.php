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
        // Tabel Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tambahkan tanda titik koma
            $table->timestamps();
        });

        // Tabel Videos
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('video');
            $table->string('title');
            $table->text('description')->nullable(); // Tambahkan nullable jika tidak wajib
            $table->string('privacy')->default('public'); // Tambahkan default
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('views')->default(0);
            $table->timestamps();
        });

        // Tabel Pivot User Video Likes
        Schema::create('user_video_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->enum('status', ['active', 'removed'])->default('active');
            $table->timestamps();
            $table->unique(['user_id', 'video_id'], 'unique_user_video');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_video_likes');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('categories');
    }
};
