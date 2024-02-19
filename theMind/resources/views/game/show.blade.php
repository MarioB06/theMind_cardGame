<x-app-layout>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Game Room</title>
        <!-- Lade das Pusher JavaScript SDK -->
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
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
            </div>
        </div>
    </body>

</x-app-layout>

<!-- Lade und binde das Pusher JavaScript SDK -->
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    // Erstelle eine neue Pusher-Instanz mit deinen Pusher-Anmeldeinformationen
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });

    // Abonniere den Pusher-Kanal für das aktuelle Spiel
    const channel = pusher.subscribe('{{ $game->id }}');

    // Reagiere auf Ereignisse auf dem Pusher-Kanal
    channel.bind('game.updated', function (data) {
        // Aktualisiere die Benutzeroberfläche basierend auf den erhaltenen Daten
        // Zum Beispiel kannst du den Spielstatus überprüfen und die Seite aktualisieren
        location.reload(); // Aktualisiere die Seite, wenn ein Spiel aktualisiert wird
    });
</script>
