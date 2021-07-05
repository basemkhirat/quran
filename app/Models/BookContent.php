<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookContent extends Model
{
    protected $table = 'books_contents';
    protected $primaryKey = 'books_contents_id';
    protected $guarded = ['books_contents_id'];

    public function ayah(){
        return $this->belongsTo(Ayah::class,'ayah_from_id','ayah_id');
    }
}
