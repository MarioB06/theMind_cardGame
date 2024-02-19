<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-4">
                <!-- Spiel erstellen -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Spiel erstellen') }}</h3>
                        <form action="{{ route('games.create') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Spiel erstellen') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Spiel beitreten -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Spiel beitreten') }}</h3>
                        <form action="{{ route('games.join') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="room_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zugangscode:</label>
                                <input type="text" name="room_code" id="room_code" class="form-input mt-1 block w-full" placeholder="Zugangscode eingeben">
                            </div>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Spiel beitreten') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
