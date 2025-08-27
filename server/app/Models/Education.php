<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = ['user_meta_id', 'title', 'description','start_date','end_date'];

    public function user_meta()
    {
        return $this->belongsTo(UserMeta::class);
    }
}
