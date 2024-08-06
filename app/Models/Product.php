<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'quantity', 'type', 'price', 'iva', 'status', 'category_id', 'company_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}