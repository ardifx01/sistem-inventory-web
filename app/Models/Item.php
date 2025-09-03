<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'dscription',
        'itemCode',
        'codeBars',
        // 'description',
        'rack_location', // tambahkan kolom location
        'rack_location_unique', // kolom helper untuk unique constraint yang mengecualikan ZIP
        'category_id', // tambahkan supaya bisa mass assign
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
