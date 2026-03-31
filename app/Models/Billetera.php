<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billetera extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'saldoDisponible',
        'moneda'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function depositar(float $monto)
    {
        $this->saldoDisponible += $monto;
        $this->save();
        return $this->saldoDisponible;
    }

    public function retirar(float $monto)
    {
        if ($this->saldoDisponible < $monto) {
            throw new \Exception('Saldo insuficiente.');
        }
        $this->saldoDisponible -= $monto;
        $this->save();
        return $this->saldoDisponible;
    }

    public function consultarSaldo()
    {
        return $this->saldoDisponible;
    }
}
