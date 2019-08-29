<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Store extends Model
{
    use SoftDeletes, Sluggable;

    // protected $table = 'stores';

    protected $fillable = [
        'name',
        'slug',
        'likes',
        'views',
        'desc',
        'tags',
        'address',
        'latitude',
        'longitude',
        'pic1',
        'pic2',
        'pic3',
        'created_by',
        'updated_by',
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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function item()
    {
        return $this->hasMany('App\Models\Item', 'id_store');
    }
}
