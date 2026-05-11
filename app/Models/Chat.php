<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'activo',
        'user_id',
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    public function ultimoMensaje()
    {
        return $this->hasOne(Mensaje::class)->latestOfMany();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->orWhere('user_two_id', $userId)
                ->orWhere('user_id', $userId);
        });
    }

    public function scopeBetweenUsers(Builder $query, int $firstUserId, int $secondUserId): Builder
    {
        return $query->where(function (Builder $query) use ($firstUserId, $secondUserId) {
            $query->where('user_one_id', $firstUserId)
                ->where('user_two_id', $secondUserId);
        })->orWhere(function (Builder $query) use ($firstUserId, $secondUserId) {
            $query->where('user_one_id', $secondUserId)
                ->where('user_two_id', $firstUserId);
        });
    }

    public static function primerChatEntre(int $firstUserId, int $secondUserId): ?self
    {
        return self::betweenUsers($firstUserId, $secondUserId)->first();
    }

    public static function crearChatEntre(int $firstUserId, int $secondUserId): self
    {
        $firstUser = User::find($firstUserId);
        $secondUser = User::find($secondUserId);

        return self::create([
            'nombre' => 'Chat con ' . ($secondUser?->name ?? 'Usuario'),
            'activo' => true,
            'user_id' => $firstUserId,
            'user_one_id' => $firstUserId,
            'user_two_id' => $secondUserId,
            'last_message_at' => null,
        ]);
    }

    public function hasParticipant(int $userId): bool
    {
        return (int) $this->user_one_id === $userId
            || (int) $this->user_two_id === $userId
            || (int) $this->user_id === $userId;
    }

    public function otherParticipant(User|int $user): ?User
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ((int) $this->user_one_id === (int) $userId) {
            return $this->userTwo;
        }

        if ((int) $this->user_two_id === (int) $userId) {
            return $this->userOne;
        }

        if ((int) $this->user_id === (int) $userId) {
            return $this->userTwo ?: $this->userOne;
        }

        return null;
    }
}
