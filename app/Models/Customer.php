<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'nif', 'address', 'country_id', 'status', 'company_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
