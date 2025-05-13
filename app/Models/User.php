<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password' => 'hashed',
    ];

    public function generateTwoFactorCode()
    {
        $this->two_factor_code = rand(100000, 999999); // Generate a 6-digit OTP
        $this->two_factor_expires_at = Carbon::now()->addMinutes(10); // Expire in 10 minutes
        $this->save();
    }

    public function sendTwoFactorCode()
    {
        // Send email
        //  Mail::to($this->email)->send(new TwoFactorCodeMail($this->two_factor_code));

        Mail::to($this->email)->send(new \App\Mail\TwoFactorCodeMail($this->two_factor_code));

        // OR send SMS using Twilio, Nexmo, etc.
    }
}
