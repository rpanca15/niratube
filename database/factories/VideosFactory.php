<?php

namespace Database\Factories;

use App\Models\Videos;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideosFactory extends Factory
{
    protected $model = Videos::class;

    public function definition()
    {
        return [
            'video' => 'dummy_video.mp4', // Sesuaikan dengan file video dummy
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'category' => 'Education',
            'status' => 'public',
            'uploader_id' => \App\Models\User::factory(),
            'views' => 0,
            'likes' => 0,
        ];
    }
}
