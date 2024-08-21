<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Resources\AuthUserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }

    public function scopeApplyUsername($query, $username)
    {

        if (preg_match('/^\+?88\d{11}/', $username)) {
            $username = preg_replace('/^\+?88/', '', $username);
        }

        return $query->where(function ($query) use ($username) {
            $query->where('email', $username)
                ->orWhere('phone', $username);
        });
    }

    public function scopeApplyPassword($query, $password)
    {
        return $query->where(function ($query) use ($password) {
            $query->whereRaw('1 = 1');
        })
            ->get()->filter(function ($item) use ($password) {
                return Hash::check($password, $item->password);
            });
    }

    public function setAuthPasswordAttribute($password)
    {
        $this->password =  bcrypt($password);
    }

    public function setAndGetLoginResponse($token = null, $additional = [])
    {
        if ($token === null) {
            $token = $this->loginAndGetToken();
        }

        return [
            'user'  =>  (new AuthUserResource($this)),
            'token' => $token,
            'tokenHash' => base64_encode($token),
            'message' => 'You are successfully logged  in',
        ] + $additional;
    }

    public function loginAndGetToken()
    {
        return $this->createToken(request()->ip())->plainTextToken;
    }
}
