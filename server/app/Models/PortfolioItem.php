<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
