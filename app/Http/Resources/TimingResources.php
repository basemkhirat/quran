<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimingResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'end' => $this->time_of_end,
            'aya' => (int)$this->ayah_id
        ];
    }
}
