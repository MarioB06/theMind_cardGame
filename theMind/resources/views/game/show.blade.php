<x-app-layout>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Game Room</title>
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            Pusher.logToConsole = true;
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const channel = pusher.subscribe('theMIndCardGame-{{ $game->id }}');

            channel.bind('game.updated', function (data) {
                location.reload();
            });

            channel.bind('game.started', function (data) {
                location.reload();
            });

            
        </script>



    </head>

    <body>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    
                @if($game->status === 'pending')

                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p>Access Code: {{ $game->access_code }}</p>
                        <!-- Hier wird der Test-Text nach dem Start des Spiels angezeigt -->
                        <div id="game-info">
                            @if($game->status === 'pending' && $game->participants->count() >= 4)
                            <form action="{{ route('game.start', $game->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Spiel starten
                                </button>
                            </form>
                            @else
                            <p>Warten auf das Starten des Spiels...</p>
                            @endif
                            <p>{{ __('Anzahl der Spieler: ') }} {{ $game->participants->count() }}</p>
                        </div>
                    </div>
                
                @else 

                    <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Das Spiel hat gestartet!</p>
    
                        @if($userCardNumber)
                            <p>Deine Kartennummer: {{ $userCardNumber }}</p>
                        @else
                            <p>Du hast keine Karte zugewiesen bekommen.</p>
                        @endif
                    </div>

                @endif
                
                </div>
            </div>
        </div>
    </body>

</x-app-layout>
