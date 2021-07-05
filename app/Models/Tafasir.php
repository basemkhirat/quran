<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tafasir extends Model
{

    protected $table = "tafasir";
    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
 /*   protected $hidden = [
        'id','key','name','created_at','updated_at'
    ];*/

    /**
     * @var array
     */
    /*protected $appends = ['key'];*/

    /**
     * @return string
     */
  /*  public function getKeyAttribute() {
        return (string) @$this->name;
    }*/
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * get transes caption
     */
    public function caption()
    {
        return $this->belongsTo(Caption::class, 'key','key');
    }
}
