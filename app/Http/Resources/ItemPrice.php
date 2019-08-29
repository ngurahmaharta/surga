<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ItemPrice extends Resource
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
            'id_item' => $this->id_item,
            'price' => $this->price,
            'unit' => $this->unit,
            'created_by' => $this->created_by,
            // 'updated_by' => $this->updated_by,*
            // 'deleted_by' => $this->deleted_by,
            'created_at' => (string) $this->created_at,
            // 'updated_at' => (string) $this->updated_at,
            // 'deleted_at' => (string) $this->deleted_at,
        ];
    }
}
