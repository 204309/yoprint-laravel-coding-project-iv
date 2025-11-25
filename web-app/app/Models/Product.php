<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unique_key',
        'product_title',
        'product_description',
        'style#',
        'sanmar_mainframe_color',
        'size',
        'color_name',
        'piece_price',
    ];

    protected static array $uniqueKeys = [
        'unique_key'
    ];

    public static function uniqueBy(): array
    {
        return self::$uniqueKeys;
    }
}
