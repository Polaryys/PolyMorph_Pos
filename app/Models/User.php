<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'rol',
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'password',
        'imagen_id',
        'estado',
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
            'estado' => 'boolean',
        ];
    }

    public function imagen()
    {
        return $this->belongsTo(Imagen::class, 'imagen_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'vendedor_id');
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'usuario_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class, 'usuario_id');
    }

    public function solicitudesIdempotencia(): HasMany
    {
        return $this->hasMany(SolicitudIdempotencia::class, 'usuario_id');
    }
}
