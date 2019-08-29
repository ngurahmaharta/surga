<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
// use App\Http\Resources\ItemPrice as ItemPriceResource;

// use App\Http\Resources\Store as StoreResource;

class StoreDetail extends Resource
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
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'likes' => $this->likes,
            'views' => $this->views,
            'desc' => $this->desc,
            'tags' => $this->tags,

            'pic1' => $this->pic1,
            // 'pic2' => $this->pic2,
            // 'pic3' => $this->pic3,

            // 'created_by' => $this->created_by,
            // 'surveyor_name' => $this->whenLoaded('surveyor')->name,
            // 'surveyor_username' => $this->whenLoaded('surveyor')->username,

            // 'updated_by' => $this->updated_by,
            // 'deleted_by' => $this->deleted_by,
            'created_at' => (string) $this->created_at,
            // 'updated_at' => (string) $this->updated_at,
            // 'deleted_at' => (string) $this->deleted_at,
        ];
    }
}
