<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //多対対の反対※不要
    //public function favorited()
    //{
    //	return $this->belongsToMany(User::class, 'user_favorite', 'favorite_id', 'user_id')->withTimestamps();
    //}
}