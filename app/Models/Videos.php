<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'video',
        'title',
        'description',
        'category',
        'status',
        'uploader_id',
        'uploaded_date',
        'views',
        'likes',
    ];
}
