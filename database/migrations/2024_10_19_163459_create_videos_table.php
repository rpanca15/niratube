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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('video');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('status');
            $table->bigInteger('uploader_id')->nullable();
            $table->timestamps();
            $table->bigInteger('views')->default(0);
            $table->bigInteger('likes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
