<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouletteBet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'selected_color',
        'result_color',
        'amount',
        'won',
        'balance_before',
        'balance_after',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'won' => 'boolean',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSelectedColorLabelAttribute(): string
    {
        return match ($this->selected_color) {
            'red' => 'Rojo',
            'black' => 'Negro',
            default => ucfirst((string) $this->selected_color),
        };
    }

    public function getResultColorLabelAttribute(): string
    {
        return match ($this->result_color) {
            'red' => 'Rojo',
            'black' => 'Negro',
            'green' => 'Verde',
            default => ucfirst((string) $this->result_color),
        };
    }
}
