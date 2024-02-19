<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $accessCode = $this->generateUniqueAccessCode();

        $game = Game::create([
            'access_code' => $accessCode,
            'user_id' => auth()->id(),
        ]);

        // Füge den Benutzer, der das Spiel erstellt hat, als Teilnehmer hinzu
        $game->participants()->attach(auth()->id());

        return view('game.show', compact('game'))->with('success', 'Spiel erfolgreich erstellt!');
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
        // Den room_code aus dem Request erhalten
        $roomCode = $request->input('room_code');

        // Suche nach dem Spiel anhand des room_code
        $game = Game::where('access_code', $roomCode)->first();

        // Überprüfe, ob ein Spiel mit diesem room_code gefunden wurde
        if (!$game) {
            return redirect()->back()->with('error', 'Spiel nicht gefunden.');
        }

        // Überprüfe, ob das Spiel bereits die maximale Anzahl von Spielern erreicht hat
        if ($game->participants->count() >= 4) {
            return redirect()->back()->with('error', 'Das Spiel hat bereits die maximale Anzahl von Spielern erreicht.');
        }

        // Füge den aktuellen Benutzer als Teilnehmer des Spiels hinzu
        $game->participants()->attach(auth()->id());

        // Erfolgreiche Weiterleitung zur Spielansicht
        return redirect()->route('game.show', $game->id)->with('success', 'Erfolgreich dem Spiel beigetreten!');
    }


    public function show($id)
    {
        $game = Game::findOrFail($id);

        return view('game.show', compact('game'));
    }

}