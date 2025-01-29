<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-semibold mb-6">Mes formulaires d'émargement</h1>

        @if($attendanceForms->isEmpty())
            <p class="text-lg text-gray-700">Aucun formulaire d'émargement créé.</p>
        @else
            <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
                <table class="min-w-full table-auto">
                    <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-6 py-3 text-left">Nom de l'événement</th>
                        <th class="px-6 py-3 text-left">Date de l'événement</th>
                        <th class="px-6 py-3 text-left">Heure du début</th>
                        <th class="px-6 py-3 text-left">Heure de fin</th>
                        <th class="px-6 py-3 text-left">Signature professeur</th>
                        <th class="px-6 py-3 text-left">Date de création</th>
                        <th class="px-6 py-3 text-left">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attendanceForms as $form)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-6 py-3">{{ $form->event_name }}</td>
                            <td class="px-6 py-3">{{ $form->event_date }}</td>
                            <td class="px-6 py-3">{{ $form->event_start_hour }}</td>
                            <td class="px-6 py-3">{{ $form->event_end_hour }}</td>
                            <td class="px-6 py-3">
                                @if($form->signature_teacher)
                                    <img class="w-20 h-auto" src="/{{ $form->signature_teacher }}" alt="Signature de {{ Auth::user()->name }}">
                                @else
                                    <span class="text-gray-500">Aucune signature pour le moment</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $form->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('attendance.list', ['eventId' => $form->event_id]) }}" class="text-blue-500 hover:underline">Liste des présents</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
