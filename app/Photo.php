<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    // ぷらいまりきー
    protected $keyType = 'string';

    protected $visible = [
        'id','owner','url','comments',
        'likes_count','liked_by_user','public_id'
    ];

    protected $appends = [
        'url','likes_count','liked_by_user',
    ];

    // 1ページ当たりの描写される写真の数
    protected $perPage = 9;

    //public $incrementing = false;

    // IDの桁数
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes,"id")) {
            $this->setId();
        }
    }
      /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     * @return string
     */
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    public function owner()
    {
        return $this->belongsTo('App\User','user_id','id','users');
    }

    public function getUrlAttribute()
    {
        $photoAttr = Storage::url('photos/'. $this->attributes['filename']);

        return $photoAttr;
    }



    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function($user) {
            return $user->id === Auth::user()->id;
        });
    }

    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User','likes')->withTimestamps();
    }

}
