<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPrice extends Model
{
    use SoftDeletes;

    protected $table = 'item_price';

    protected $fillable = [
        'id_item',
        'price',
        'unit',
        'created_by',
        // 'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
