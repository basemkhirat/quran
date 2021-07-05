<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caption extends Model
{
    protected $table = 'captions';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation(){
        return $this->hasMany(Transes::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tafasir(){
        return $this->hasMany(Tafasir::class);
    }
}
