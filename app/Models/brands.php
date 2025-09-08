<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class brands extends Model
{
    public $timestamps = true;

    protected $fillable = ['name', 'slug', 'image', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
