<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'leido',
        'fecha',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function crearNotificacion($userId, $titulo, $mensaje, $tipo = 'info')
    {
        return self::create([
            'user_id' => $userId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leido' => false,
            'fecha' => now(),
        ]);
    }
}
