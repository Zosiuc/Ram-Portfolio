<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectMedia extends Model
{
    use HasFactory;

    protected $table = 'subject_media'; // jouw migratie naam
    protected $fillable = ['subject_id', 'media_type', 'media_url'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
