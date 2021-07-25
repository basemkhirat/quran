<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecitersResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'caption' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
                'ur' => $this->name_ar
            ],
            'img' => null,
            'src_base_url' => $this->getBaseSrc($this->tracks->first()->url),
            'src_base_url_without_sura' => $this->getBaseSrcWithOutSura($this->tracks->first()->url),
        ];
    }

    public function getBaseSrc($str)
    {
        $url_arr = explode('/', $str);
        if(count($url_arr) === 7){
            $url_arr = array_splice($url_arr, 0, -2);
            $url = implode('/', $url_arr);
            return url($url . '/');
        }else{
            return null;
        }

    }

    public function getBaseSrcWithOutSura($str)
    {
        $url_arr = explode('/', $str);
        if (count($url_arr) === 6){
            $url_arr = array_splice($url_arr, 0, -1);
            $url = implode('/', $url_arr);
            return url($url . '/');
        }else{
          return null;
        }


    }
}
