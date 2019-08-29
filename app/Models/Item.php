<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Item extends Model
{
    use SoftDeletes, Sluggable;

    // protected $table = 'items';

    protected $fillable = [
        'id_store',
        'name',
        'slug',
        'likes',
        'views',
        'desc',
        'tags',
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

    public function item_price(){
        return $this->hasMany('App\Models\ItemPrice', 'id_item')->latest();
    }

    public function store(){
        return $this->belongsTo('App\Models\Store', 'id_store')->withTrashed();
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by')->withTrashed();
    }

    public function getPricePerPcsAttribute(){
        return $this->item_price()->where('unit', 'pcs')->latest()->first()->price;
    }

    // public function getLatestPriceAttribute(){
    //     return $this->item_price->first();
    // }
}
