<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'item_code',
        'barcode',
        'description',
        'rack_location', // tambahkan kolom location
        'category_id', // tambahkan supaya bisa mass assign
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
