<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserUnLikes extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');

    }
}
