<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'first_name','last_name','phone','country_code','country_name',
        'name',
        'email',
        'password',
        'role',
        'is_active','is_approved','plan_id','created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function plan(){
        return $this->belongsTo(Plans::class,'plan_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', '1');
        });
    }

    public function employees(){
        return $this->hasMany(User::class,'created_by');
    }

    public function posts(){
        return $this->hasMany(Post::class,'user_id');
    }

    public function softDeleteWithPosts()
    {
        $this->posts()->delete();
        $this->forceDelete();
    }

    public function dealerAdmin(){
        return $this->belongsTo(User::class,'created_by');
    }





}
