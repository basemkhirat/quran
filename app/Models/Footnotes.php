<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footnotes extends Model
{
    protected $table = 'books_footnotes';
    protected $guarded = ['books_footnotes_id'];
}
