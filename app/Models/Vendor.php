<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_name',
        'store_address',
        'image',
        'driver_license_id',
        'id_number',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class);
    }
    public function products()
    {
        return $this->hasMany(Products::class);
    }
}
