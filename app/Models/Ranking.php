<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'posicion',
        'puntos',
        'total_ganado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
