<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['clave', 'valor', 'descripcion', 'activo'];
    
    protected $casts = [
        'activo' => 'boolean',
        'valor' => 'integer',
    ];
}