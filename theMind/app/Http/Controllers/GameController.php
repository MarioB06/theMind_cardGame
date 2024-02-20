<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Card;
use Pusher\Pusher;
use App\Http\Controllers\DB;


class GameController extends Controller
{
    public function create(Request $request)
    {
        $accessCode = $this->generateUniqueAccessCode();
    
        // Spiel erstellen und in games-Tabelle eintragen
        $game = Game::create([
            'access_code' => $accessCode,
            'user_id' => auth()->id(),
        ]);
    
        // Alle Karten für das Spiel in der cards-Tabelle erstellen
        $this->createCardsForGame($game);
    
        // Spieler zum Spiel hinzufügen und Karte zuweisen
        $this->addParticipantAndAssignCard($game);
    
        // Pusher-Event auslösen
        if ($game->participants->count() >= 4) {
            $game->update(['status' => 'started']);
            $this->sendPusherEvent('game.created', ['game_id' => $game->id], $game);
        }
    
        return redirect()->route('game.show', $game->id)->with('success', 'Spiel erfolgreich erstellt und beigetreten!');
    }
    
    private function createCardsForGame(Game $game)
    {
        // Alle Karten für das Spiel erstellen und in cards-Tabelle eintragen
        $cards = [];
        for ($i = 1; $i <= 100; $i++) {
            $cards['card_' . $i] = true; // Alle Karten sind zunächst verfügbar
        }
        $game->cards()->create($cards);
    }
    
    private function addParticipantAndAssignCard(Game $game)
    {
        // Den aktuellen Benutzer als Teilnehmer hinzufügen
        $game->participants()->attach(Auth::id());
    
        // Eine Karte für den Teilnehmer auswählen und zuweisen
        $cardNumber = $this->getAvailableCardNumber($game);
        $game->participants()->updateExistingPivot(Auth::id(), ['card_number' => $cardNumber]);
    
        // Markiere die zugewiesene Karte als vergeben (false)
        $game->cards()->update(['card_' . $cardNumber => false]);
    }
    
    private function getAvailableCardNumber(Game $game)
    {

        return 1;
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

        $this->sendPusherEvent('game.updated', ['game_id' => $game->id], $game);

        return redirect()->route('game.show', $game->id)->with('success', 'Erfolgreich dem Spiel beigetreten!');
    }

    private function sendPusherEvent($event, $data, Game $game)
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

        $channelName = 'theMIndCardGame-' . $game->id;
        $pusher->trigger($channelName, $event, $data);

    }

    public function show($id)
    {
        $game = Game::findOrFail($id);

        return view('game.show', compact('game'));
    }

    public function start(Game $game)
    {
        $gameId = $game->id;

        $game->update(['status' => 'started']);

        $this->sendPusherEvent('game.started', ['game_id' => $game->id], $game);
    }

    private function get_card()
    {

    }
}
