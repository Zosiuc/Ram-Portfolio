<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;

    protected $fillable = ['id','job_title','first_name', 'last_name', 'bio','education_bio','number','email','profile_photo','cover_photo','birthday'];

    public function education()
    {
        return $this->hasMany(Education::class,'user_meta_id');
    }

    public function experience()
    {
        return $this->hasMany(Experience::class,'user_meta_id');
    }

    public function skills()
    {
        return $this->hasMany(Skill::class,'user_meta_id');
    }

    public function socialMedia()
    {
        return $this->hasMany(SocialMedia::class,'user_meta_id');
    }

    public function reels()
    {
        return $this->hasMany(Reel::class,'user_meta_id');
    }

}
