<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Message;
use App\Models\VerifyUser;
use App\Models\Ranking;
use App\Models\GroupUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'title',
        'email_verified_at',
        'google_id',
        'facebook_id',
        'line_id',
        'apple_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function ranking()
    {
        return $this->hasOne(Ranking::class);
    }

    public function group()
    {
        return $this->hasMany(GroupUser::class, 'user_id', 'id');
    }

    public function userLeagues()
    {
        return $this->hasMany('App\Models\UserLeague');
    }

    public function schedule()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function league()
    {
        return $this->hasMany('App\Models\League','owner_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'group_owner', 'id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class,'created_by_user_id', 'id');
    }

    public function partnerInLeague()
    {
        return $this->hasMany(Partner::class,'created_by_user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited($productId)
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }
}
