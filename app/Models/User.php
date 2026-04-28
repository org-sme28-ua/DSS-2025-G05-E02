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
    protected $fillable = [
        'name',
        'email',
        'password',
        'puntos_fidelidad',
        'nivel_vip',
        'role',
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


    public function billetera()
    {
        return $this->hasOne(Billetera::class);
    }

    public function apuestas()
    {
        return $this->hasMany(Apuesta::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'emisor_id');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'receptor_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'emisor_id');
    }

    public function amigos()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'friend_id')->withTimestamps();
    }

    public static function registrar(array $datos)
    {
        $usuario = new User();
        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->password = $datos['password']; // Laravel hasher applied automatically
        $usuario->puntos_fidelidad = 0;
        $usuario->nivel_vip = 0;
        $usuario->save();

        // Inicializar billetera vacía
        $usuario->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']);

        return $usuario;
    }

    public function apostar(int $juegoId, float $monto, float $cuota)
    {
        // Opcional: Validar saldo en billetera aquí antes de apostar
        // Esta función crea la apuesta y reduce saldo billetera

        if (!$this->billetera) {
            $this->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']);
            $this->load('billetera');
        }

        if ($this->billetera->saldoDisponible < $monto) {
            throw new \Exception('Saldo insuficiente.');
        }

        $this->billetera->saldoDisponible -= $monto;
        $this->billetera->save();

        return $this->apuestas()->create([
            'juego_id' => $juegoId,
            'monto' => $monto,
            'cuota' => $cuota,
            'estado' => 'pendiente',
            'fecha' => now(),
        ]);
    }

    public function enviarMensaje(User $receptor, string $contenido, int $chatId = null)
    {
        // Si no hay chatId, buscar o crear un chat entre los dos usuarios
        if (!$chatId) {
            $chat = Chat::primerChatEntre($this->id, $receptor->id);
            if (!$chat) {
                $chat = Chat::crearChatEntre($this->id, $receptor->id);
            }
            $chatId = $chat->id;
        }

        $mensaje = Mensaje::create([
            'chat_id' => $chatId,
            'emisor_id' => $this->id,
            'receptor_id' => $receptor->id,
            'contenido' => $contenido,
            'fechaHora' => now(),
            'editado' => false,
        ]);
        // Crear notificación para el receptor
        \App\Models\Notificacion::crearNotificacion(
            $receptor->id,
            'Nuevo mensaje recibido',
            "Has recibido un nuevo mensaje de {$this->name}.",
            'mensaje'
        );
        return $mensaje;
    }

    // --- FUNCIONES EXTRA ---

    // Operaciones de billetera
    public function depositar(float $monto)
    {
        return $this->billetera->depositar($monto);
    }

    public function retirar(float $monto)
    {
        return $this->billetera->retirar($monto);
    }

    public function consultarSaldo()
    {
        return $this->billetera->consultarSaldo();
    }

    // Gestión de apuestas
    public function historialApuestas()
    {
        return $this->apuestas()->orderByDesc('fecha')->get(); // Podemos modificar para usar scopePorUsuario en controlador si es necesario
    }

    public function apuestasActivas()
    {
        return $this->apuestas()->activas()->get();
    }

    public function apuestasGanadas()
    {
        return $this->apuestas()->ganadas()->get();
    }

    public function apuestasPerdidas()
    {
        return $this->apuestas()->perdidas()->get();
    }

    // Gestión de amigos
    public function solicitudesDeAmistadPendientes()
    {
        // Si gestionaras solicitudes, necesitarías una tabla pivote especial
        return []; // Placeholder
    }

    public function bloquearUsuario(User $otro)
    {
        // Puedes tener otra tabla pivote 'user_blocks'
        // Aquí solo es demostrativo
        // $this->bloqueados()->attach($otro->id);
        return true;
    }

    public function desbloquearUsuario(User $otro)
    {
        // $this->bloqueados()->detach($otro->id);
        return true;
    }

    // Notificaciones
    public function notificacionesNoLeidas()
    {
        return $this->hasMany(Notificacion::class)->where('leida', false)->get();
    }

    public function marcarNotificacionesComoLeidas()
    {
        return $this->hasMany(Notificacion::class)->where('leida', false)->update(['leida' => true]);
    }

    // Configuración y perfil
    public function actualizarPerfil(array $datos)
    {
        $this->update($datos);
        return $this;
    }

    public function cambiarContrasena(string $nuevaContrasena)
    {
        $this->password = bcrypt($nuevaContrasena);
        $this->save();
    }

    // Estadísticas del usuario
    public function estadisticas()
    {
        return [
            'total_apuestas' => $this->apuestas()->count(),
            'apuestas_ganadas' => $this->apuestasGanadas()->count(),
            'apuestas_perdidas' => $this->apuestasPerdidas()->count(),
            'saldo' => $this->consultarSaldo()
        ];
    }

    public function sumarPuntosFidelidad($puntos)
    {
        $this->puntos_fidelidad += $puntos;
        $this->save();
    }
}
