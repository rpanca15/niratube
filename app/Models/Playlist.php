<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi many-to-many ke video
    public function videos()
    {
        return $this->belongsToMany(Videos::class, 'playlist_video', 'playlist_id', 'video_id');
    }
}
