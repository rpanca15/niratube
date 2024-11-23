<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Videos extends Model
{
    use HasFactory;

    /**
     * Fillable attributes for mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'video',
        'title',
        'description',
        'privacy',
        'category_id',
        'uploader_id',
        'views',
    ];

    /**
     * Relasi ke tabel User (Uploader).
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Relasi ke tabel Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke tabel User untuk likes.
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'user_video_likes', 'video_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Menghitung jumlah likes dengan status 'active'.
     *
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->where('status', 'active')->count();
    }

    public function like(User $user)
    {
        $existingLike = $this->likes()
            ->wherePivot('user_id', $user->id)
            ->first();

        // Log::info('Existing like status: ' . ($existingLike ? $existingLike->pivot->status : 'none'));

        if ($existingLike) {
            $newStatus = $existingLike->pivot->status === 'active' ? 'removed' : 'active';
            $this->likes()->updateExistingPivot($user->id, ['status' => $newStatus]);
            // Log::info('Updated status to ' . $newStatus . ' for user_id ' . $user->id);
        } else {
            $this->likes()->attach($user->id, ['status' => 'active']);
            // Log::info('Attached new like with status active for user_id ' . $user->id);
        }
    }
}
