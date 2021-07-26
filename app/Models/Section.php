<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Section
 * @package Dot\Sections\Models
 */
class Section extends Model
{

    /**
     * @var string
     */
    protected $table = 'sections';

    /**
     * sections relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function sections()
    {
        return $this->hasMany(Section::class, 'parent');
    }

    /**
     * sections relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function translations()
    {
        return $this->hasMany(SectionTranslation::class, 'section_id');
    }

    /**
     * @param $query
     * @param int $parent
     */
    function scopeParent($query, $parent = 0)
    {
        $query->where("sections.parent", $parent);
    }
}
