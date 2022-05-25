<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $visible = [
        'name','id','profile_image','url','public_id'
    ];

    protected $appends = [
        'url'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

    public function getUrlAttribute()
    {
        $user = Auth::user();
        if ($user->profile_image == NULL) {
            $default = 'Gorilla.jpeg';
            $user->profile_image = $default;
        };

        $userAttr = Storage::url('profiles/'. $this->attributes['profile_image']);
        return $userAttr;
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }
}
