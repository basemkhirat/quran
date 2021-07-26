<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model{

    protected $table = "pages";
    
    protected $hidden = [
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

    protected $appends = ["title", "excerpt", "content"];

    public function getTitleAttribute()
    {
        return $this->{"title_" . app()->getLocale()};
    }

    public function getExcerptAttribute()
    {
        return $this->{"excerpt_" . app()->getLocale()};
    }

    public function getContentAttribute()
    {
        return $this->{"content_" . app()->getLocale()};
    }
}
