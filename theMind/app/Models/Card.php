<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['game_id', 'card_number'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
