<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LastPlayedCard extends Model
{
    protected $table = 'last_played_card';

    protected $fillable = ['game_id', 'card'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
