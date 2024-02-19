<x-app-layout>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Room</title>
</head>
<body>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Access Code: {{ $game->access_code }}</p>
                    @if($game->status === 'pending' && $game->participants->count() >= 4)
                        <form action="{{ route('game.start', $game->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Spiel starten
                            </button>
                        </form>
                    @else
                        <p>Warten auf das Starten des Spiels...</p>
                    @endif
                    <p>{{ __('Anzahl der Spieler: ') }} {{ $game->participants->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</body>

</x-app-layout>
