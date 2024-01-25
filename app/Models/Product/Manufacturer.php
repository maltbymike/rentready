<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_manufacturers';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
