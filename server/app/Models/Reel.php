<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;

    protected $fillable = ['user_meta_id', 'title', 'description','url'];

    public function user_meta()
    {
        return $this->belongsTo(UserMeta::class);
    }
}
