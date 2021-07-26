<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $table = 'posts';

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $hidden = ["published_at", "user_id", "image", "media", "tags", "sections", "status"];

    protected $appends = ["post_tags", "post_sections"];

    public function getPostTagsAttribute()
    {
        return $this->tags->map(function ($tag) {
            return (object) [
                "id" => $tag->id,
                "name" => $tag->name
            ];
        });
    }

    public function getPostSectionsAttribute()
    {
        return $this->sections->map(function ($tag) {
            return (object) [
                "id" => $tag->id,
                "name" => $tag->translations()
                    ->join("languages", "languages.languages_id", "=", "sections_translations.language_id")
                    ->where("languages.short_name", app()->getLocale())->first()->name
            ];
        });
    }

        /**
     * Status scope
     * @param $query
     * @param $status
     */
    public function scopeStatus($query, $status)
    {
        switch ($status) {
            case "published":
                $query->where("status", 1);
                break;

            case "unpublished":
                $query->where("status", 0);
                break;
        }
    }

     /**
     * Categories relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, "posts_sections", "post_id", "section_id");
    }

    /**
     * Galleries relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function galleries()
    {
        return $this->belongsToMany(Gallery::class, "posts_galleries", "post_id", "gallery_id");
    }

    /**
     * Sync tags
     * @param $tags
     */
    public function syncTags($tags)
    {
        $tag_ids = array();

        if ($tags = @explode(",", $tags)) {
            $tags = array_filter($tags);
            $tag_ids = Tag::saveNames($tags);
        }

        $this->tags()->sync($tag_ids);
    }

    /**
     * Tags relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "posts_tags", "post_id", "tag_id");
    }
}
