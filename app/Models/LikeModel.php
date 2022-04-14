<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LikeModel extends Model
{

    protected $table = 'like';

    protected $fillable = [
        'counter',
        'article_id',
    ];

    protected $hidden = [];
}
