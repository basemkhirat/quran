<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'books_id';
    protected $guarded = ['books_id'];
    public $timestamps = false;
}
