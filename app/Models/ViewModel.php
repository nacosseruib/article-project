<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ViewModel extends Model
{

    protected $table = 'views';

    protected $fillable = [
        'count',
        'article_id',
    ];

    protected $hidden = [];
}
