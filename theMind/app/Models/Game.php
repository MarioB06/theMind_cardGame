<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'access_code',
        'user_id',
        'status',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'game_participants');
    }
}
