<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;

    protected $fillable = ['user_meta_id','job_title','first_name', 'last_name', 'bio','education_bio','number','email','profile_photo','cover_photo','birthday'];

    public function education()
    {
        return $this->hasMany(Education::class);
    }

    public function experience()
    {
        return $this->hasMany(Experience::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function socialMedia()
    {
        return $this->hasMany(SocialMedia::class);
    }

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

}
