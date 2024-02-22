<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Card;
use App\Models\LastPlayedCard;
use Pusher\Pusher;
use Illuminate\Support\Facades\DB;


class GameController extends Controller
{
    public function create(Request $request)
    {
        $accessCode = $this->generateUniqueAccessCode();

        $game = Game::create([
            'access_code' => $accessCode,
            'user_id' => auth()->id(),
        ]);

        LastPlayedCard::create([
            'game_id' => $game->id,
            'card' => 0,
        ]);

        $this->createCardsForGame($game);

        $this->addParticipantAndAssignCard($game);

        if ($game->participants->count() >= 4) {
            $game->update(['status' => 'started']);
            $this->sendPusherEvent('game.created', ['game_id' => $game->id], $game);
        }

        return redirect()->route('game.show', $game->id)->with('success', 'Spiel erfolgreich erstellt und beigetreten!');
    }

    private function createCardsForGame(Game $game)
    {
        $cards = [];
        for ($i = 1; $i <= 100; $i++) {
            $cards['card_' . $i] = true;
        }
        $game->cards()->create($cards);
    }

    private function addParticipantAndAssignCard(Game $game)
    {
        $game->participants()->attach(Auth::id());

        $cardNumber = $this->getAvailableCardNumber($game);
        $game->participants()->updateExistingPivot(Auth::id(), ['card_number' => $cardNumber]);

        $game->cards()->update(['card_' . $cardNumber => false]);
    }

    private function getAvailableCardNumber(Game $game)
    {
        $randomCardNumber = rand(1, 100);

        $assignedCard = DB::table('game_participants')
            ->where('game_id', $game->id)
            ->where('card_number', $randomCardNumber)
            ->exists();

        if ($assignedCard) {
            return $this->getAvailableCardNumber($game);
        }

        return $randomCardNumber;
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

        $this->addParticipantAndAssignCard($game);

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
        $userCardNumber = $game->participants()->where('user_id', auth()->id())->value('card_number');
        $lastPlayedCard = LastPlayedCard::where('game_id', $game->id)->latest()->first();
        return view('game.show', compact('game', 'userCardNumber', 'lastPlayedCard'));
    }


    public function start(Game $game)
    {
        $game->update(['status' => 'started']);

        $this->sendPusherEvent('game.started', ['game_id' => $game->id], $game);

        return redirect()->route('game.show', $game->id);
    }


    public function playCard(Request $request, Game $game)
    {
        $playedCardNumber = $game->participants()->where('user_id', auth()->id())->value('card_number');

        $lastPlayedCard = LastPlayedCard::where('game_id', $game->id)->latest()->first();

        if ($lastPlayedCard) {

            $lastPlayedCard_value = $lastPlayedCard->card;

            if($lastPlayedCard_value > $playedCardNumber)
            {
                $game->update(['status' => 'lost']);

                $this->sendPusherEvent('game.updated', ['game_id' => $game->id], $game);

                return redirect()->route('game.show', $game->id);
            }
            $lastPlayedCard->update(['card' => $playedCardNumber]);
        } else {
            LastPlayedCard::create([
                'game_id' => $game->id,
                'card' => $playedCardNumber,
            ]);
        }

        $game->increment('players_played');

        if($game->players_played < 4)
        {
            $game->update(['status' => 'won']);

        }

        $user = auth()->user();
        $game->participants()->where('user_id', $user->id)->update(['card_played' => true]);

        $this->sendPusherEvent('game.updated', ['game_id' => $game->id], $game);

        return redirect()->route('game.show', $game->id);
    }






}
