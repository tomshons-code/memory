<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Client extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $guard = 'mobile';
    protected $fillable = [
        'first_name', 'last_name', 'nickname', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }

    public function course_access()
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    public function course_question()
    {
        return $this->belongsToMany(Question::class);
    }

	public function getNameAttribute()
    {
        return Str::ucfirst($this->first_name). ' ' . Str::ucfirst($this->last_name);
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url('reset?token=' . $token);
        $this->notify(new ResetPasswordNotification($url));
    }
}
