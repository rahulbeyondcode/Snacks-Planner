<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['shop_name', 'address', 'phone_number', 'location'];

    protected $primaryKey = 'shop_id';

    public function snackItems()
    {
        return $this->hasMany(SnackItem::class);
    }
}
