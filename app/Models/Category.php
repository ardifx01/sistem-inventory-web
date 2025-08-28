<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_default',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
    
    protected static function booted()
    {
        static::creating(function ($category) {
            if ($category->is_default) {
                // Cegah ada lebih dari 1 default
                if (self::where('is_default', true)->exists()) {
                    throw new \Exception("Kategori default sudah ada.");
                }
            }
        });
    }
}
