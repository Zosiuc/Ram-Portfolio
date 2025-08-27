<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['portfolio_item_id', 'title', 'description'];

    public function portfolioItem()
    {
        return $this->belongsTo(PortfolioItem::class);
    }

    public function media()
    {
        return $this->hasMany(SubjectMedia::class);
    }
}

