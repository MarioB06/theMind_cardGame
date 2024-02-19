<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Http\Request;
use App\Models\Game;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsBroadcaster;
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
    
        $channelManager = app(ChannelManager::class);
    
        $channelManager->broadcastToChannel('game.' . $game->id, [
            'event' => 'game.updated',
            'game_id' => $game->id,
        ]);
    
        return redirect()->route('game.show', $game->id)->with('success', 'Erfolgreich dem Spiel beigetreten!');
    }
    private function checkAndUpdateGameStatus(Game $game)
    {
        if ($game->participants->count() >= 4) {
            $game->update(['status' => 'starting']);
        }
    }
    
    

    public function show($id)
    {
        $game = Game::findOrFail($id);

        return view('game.show', compact('game'));
    }

    public function start(Game $game)
    {

        $game->update(['status' => 'started']);
        

        return redirect()->route('game.show', $game->id)->with('success', 'Das Spiel wurde erfolgreich gestartet!');
    }
    

}