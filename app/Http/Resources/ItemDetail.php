<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
// use App\Http\Resources\ItemPrice as ItemPriceResource;

// use App\Http\Resources\Store as StoreResource;

class ItemDetail extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'likes' => $this->likes,
            'views' => $this->views,
            'desc' => $this->desc,
            'tags' => $this->tags,

            'pic1' => $this->pic1,
            'pic2' => $this->pic2,
            'pic3' => $this->pic3,

            // 'store' => $this->whenLoaded('store'),
            'id_store' => $this->id_store,
            'store_name' => $this->whenLoaded('store')->name,
            'store_slug' => $this->whenLoaded('store')->slug,

            'prices' => $this->whenLoaded('item_price'),

            'created_by' => $this->created_by,
            'created_by_name' => $this->whenLoaded('creator')->name,
            'created_by_username' => $this->whenLoaded('creator')->username,

            // 'deleted_by' => $this->deleted_by,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            // 'deleted_at' => (string) $this->deleted_at,
        ];
    }
}
