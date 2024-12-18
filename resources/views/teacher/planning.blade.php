<x-app-layout>
    <div>
        <h1>Emploi du temps</h1>

        <!-- Conteneur flex pour les colonnes -->
        <div class="flex space-x-4">
            @foreach($groupedEvents as $day => $events)
                <div class="day-column w-1/7 border p-4">
                    <h2 class="text-lg font-bold text-center">{{ $day }}</h2>
                    @foreach($events as $event)
                        <div class="event border-b pb-2 mb-2">
                            <p class="font-medium">{{ $event['id'] }}</p>
                            <p class="font-medium">{{ $event['name'] }}</p>
                            <p class="text-sm">Heure : {{ $event['startHour'] }} - {{ $event['endHour'] }}</p>
                            <p class="text-xs">Date : {{ $event['date'] }}</p>

                            <form action="{{ route('attendance.generateQrCode', ['eventId' => $event['id']]) }}" method="GET">
                                @csrf
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Générer le QR Code</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
