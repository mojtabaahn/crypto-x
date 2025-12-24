<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getLockedBalanceAttribute(){
        return $this->orders()
            ->where('side', 'buy')
            ->where('status',OrderStatus::Open)
            ->sum(DB::raw('price * amount'));
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
