<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Game;
use Pusher\Pusher;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $accessCode = $this->generateUniqueAccessCode();

        $game = Game::create([
            'access_code' => $accessCode,
            'user_id' => auth()->id(),
        ]);

        $game->participants()->attach(auth()->id());

        if ($game->participants->count() >= 4) {
            $game->update(['status' => 'started']);
            $this->sendPusherEvent('game.created', ['game_id' => $game->id]);
        }

        return redirect()->route('game.show', $game->id)->with('success', 'Spiel erfolgreich erstellt und beigetreten!');
    }

    private function generateUniqueAccessCode()
    {
        do {
            $accessCode = mt_rand(10000, 99999);
        } while (Game::where('access_code', $accessCode)->exists());

        return $accessCode;
    }

    public function join(Request $request)
    {
        $roomCode = $request->input('room_code');

        $game = Game::where('access_code', $roomCode)->first();

        if (!$game) {
            return redirect()->back()->with('error', 'Spiel nicht gefunden.');
        }

        if ($game->participants->count() >= 4) {
            return redirect()->back()->with('error', 'Das Spiel hat bereits die maximale Anzahl von Spielern erreicht.');
        }

        $game->participants()->attach(Auth::id());

        $this->sendPusherEvent('game.updated', ['game_id' => $game->id]);

        return redirect()->route('game.show', $game->id)->with('success', 'Erfolgreich dem Spiel beigetreten!');
    }

    private function sendPusherEvent($event, $data)
    {
        $options = [
            'cluster' => 'eu',
            'useTLS' => true
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('theMIndCardGame', $event, $data);
    }

    public function show($id)
    {
        $game = Game::findOrFail($id);

        return view('game.show', compact('game'));
    }
}
