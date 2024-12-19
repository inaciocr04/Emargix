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
                            Formation / Groupes :
                            <ul>
                                @foreach ($event['trainees'] as $trainee)
                                    <li>{{ $trainee }}</li>
                                @endforeach
                            </ul>
                            Classes :
                            <ul>
                                @foreach ($event['classrooms'] as $classroom)
                                    <li>{{ $classroom }}</li>
                                @endforeach
                            </ul>
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

<!--<x-app-layout>
    <div>
        <h1>Emploi du temps</h1>

        <div class="flex space-x-4">
            @foreach($groupedEvents as $day => $events)
                <div class="day-column w-1/7 border p-4">
                    <h2 class="text-lg font-bold text-center">{{ $day }}</h2>
                    @foreach($events as $event)
                        @php
                            // Modifier ici pour gérer le format de la date 'd/m/Y'
                            $eventDate = \Carbon\Carbon::createFromFormat('d/m/Y', $event['date']);
                            $startHour = \Carbon\Carbon::parse($event['startHour'])->format('H:i');
                            $endHour = \Carbon\Carbon::parse($event['endHour'])->format('H:i');
                            $now = \Carbon\Carbon::now()->format('H:i'); // Récupère l'heure actuelle sans la date
                        @endphp

                        <div class="event border-b pb-2 mb-2">
                            <p class="font-medium">{{ $event['id'] }}</p>
                            <p class="font-medium">{{ $event['name'] }}</p>
                            <p class="text-sm">Heure : {{ $event['startHour'] }} - {{ $event['endHour'] }}</p>
                            <p class="text-xs">Date : {{ $event['date'] }}</p>
                                                        Formation / Groupes :
                            <ul>
                                @foreach ($event['trainees'] as $trainee)
                            <li>{{ $trainee }}</li>
                                @endforeach
                        </ul>
                        Classes :
                        <ul>
@foreach ($event['classrooms'] as $classroom)
                            <li>{{ $classroom }}</li>
                                @endforeach
                        </ul>

@if ($eventDate->isToday() && $now >= $startHour && $now <= $endHour)
                                <form action="{{ route('attendance.generateQrCode', ['eventId' => $event['id']]) }}" method="GET">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Générer le QR Code</button>
                                </form>
                            @else
                                <p class="text-gray-500">Événement hors de portée ou non aujourd'hui.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>-->

