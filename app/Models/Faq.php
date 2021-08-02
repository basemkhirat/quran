<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = "faq";

    protected $hidden = [
        "lang",
        "title_ar",
        "excerpt_ar",
        "content_ar",
        "title_en",
        "excerpt_en",
        "content_en",
        "title_ur",
        "excerpt_ur",
        "content_ur"
    ];

    protected $appends = ["title", "content"];

    public function getTitleAttribute()
    {
        return $this->{"title_" . app()->getLocale()};
    }

    public function getContentAttribute()
    {
        return $this->{"content_" . app()->getLocale()};
    }
}
