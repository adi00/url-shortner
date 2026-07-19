<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    const ROLE_SUPERADMIN="superadmin";
    const ROLE_ADMIN= "admin";
    const ROLE_MEMBER="member";
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    function company(){
        return $this->belongsTo(Company::class);
    }

    function shortUrls(){
        return $this->hasmany(ShortUrl::class);
    }


    ///// Role function

        function isSuperAdmin(): bool{
            return $this->role ===self::ROLE_SUPERADMIN;
        }

         function isAdmin(): bool{
            return $this->role ===self::ROLE_ADMIN;
        }

          function isMember(): bool{
            return $this->role ===self::ROLE_MEMBER;
        }
}
