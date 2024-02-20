<x-app-layout>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Game Room</title>
        <!-- Lade das Pusher JavaScript SDK -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            Pusher.logToConsole = true;
            // Erstelle eine neue Pusher-Instanz mit deinen Pusher-Anmeldeinformationen
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            // Abonniere den Pusher-Kanal für das aktuelle Spiel
            const channel = pusher.subscribe('theMIndCardGame-{{ $game->id }}');

            // Reagiere auf Ereignisse auf dem Pusher-Kanal
            channel.bind('game.updated', function (data) {
                // Überprüfe, ob der Status auf "started" geändert wurde
                if (data.status === 'started') {
                    // Setze den Inhalt des Divs auf den Test-Text
                    document.getElementById('game-info').innerHTML = '<p>Test Test</p>';
                } else {
                    // Aktualisiere die Seite, wenn der Status nicht "started" ist
                    location.reload();
                }
            });

            // Ein weiterer bind für das Ereignis 'game.started'
            channel.bind('game.started', function (data) {
                // Reagiere auf das Ereignis 'game.started'
                // Sende eine AJAX-Anfrage, um die Route game.start aufzurufen
                fetch('{{ route('game.start', $game->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data) // Hier kannst du Daten senden, wenn nötig
                })
                .then(response => {
                    // Handle die Antwort, wenn benötigt
                    if (response.ok) {
                        // Wenn die Antwort erfolgreich ist, leite den Benutzer zur neuen Seite weiter
                        window.location.href = response.url;
                    } else {
                        // Wenn die Antwort nicht erfolgreich ist, handle den Fehler entsprechend
                        console.error('Fehler beim Aufrufen der Route game.start:', response.statusText);
                    }
                })
                .catch(error => {
                    // Handle den Fehler, wenn ein Fehler beim Senden der Anfrage auftritt
                    console.error('Fehler beim Senden der Anfrage:', error);
                });
            });
        </script>



    </head>

    <body>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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
                </div>
            </div>
        </div>
    </body>

</x-app-layout>
