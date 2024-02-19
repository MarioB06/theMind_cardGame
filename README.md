# The Mind Card Game - Online Version


Dies ist eine Online-Version des Kartenspiels "The Mind", das ursprünglich als physisches Kartenspiel veröffentlicht wurde. In dieser Version können Spieler online miteinander spielen und ihre Fähigkeiten im synchronen Kartenspiel unter Beweis stellen.

## Überblick

"The Mind" ist ein kooperatives Kartenspiel, bei dem Spieler zusammenarbeiten müssen, um Karten in aufsteigender Reihenfolge abzulegen, ohne miteinander zu kommunizieren. Die Herausforderung besteht darin, die Gedanken der anderen Spieler zu lesen und den richtigen Zeitpunkt für das Ablegen der Karten zu finden.

## Features

- **Online-Multiplayer**: Spiele mit bis zu 4 Spielern gleichzeitig und fordere deine Freunde heraus.
- **Verschiedene Schwierigkeitsstufen**: Beginne mit einfachen Leveln und arbeite dich durch immer komplexere Herausforderungen.
- **Echtzeitkommunikation**: Nutze WebSockets für eine nahtlose Echtzeitkommunikation zwischen den Spielern.
- **Benutzerprofile**: Verfolge deine Statistiken und deinen Fortschritt im Spiel mit benutzerdefinierten Profilen.

## Installation und Nutzung

Um das Spiel zu spielen, führe die folgenden Schritte aus:

1. Klone das Repository auf deinen Computer:

   ```bash
   git clone https://github.com/dein_benutzername/theMind_cardGame.git

2. Navigiere in das Projektverzeichnis:
   ```bash
   cd theMind_cardGame

3. Installiere die erforderlichen Abhängigkeiten:
   ```bash
   composer install
   npm install

4. Konfiguriere deine Umgebung, einschließlich der WebSocket-Verbindung und der Datenbankverbindung, indem du die entsprechenden Parameter in der `.env-Datei` anpasst.
5. Führe die Migrationen aus, um die Datenbanktabellen zu erstellen:
   ```bash
   php artisan migrate
   
## Contributing

Wir freuen uns über Beiträge! Wenn du Verbesserungsvorschläge hast oder Fehler gefunden hast, zögere nicht, einen Pull-Request zu erstellen oder ein Issue zu öffnen.

