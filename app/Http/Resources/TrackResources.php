<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackResources extends JsonResource
{

    public function toArray($request)
    {
        return [
            'url' => $this->url,
            'sura' => $this->ZSURANUMBER,
        ];
    }
}
