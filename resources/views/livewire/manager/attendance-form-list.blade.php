<div>
    <!-- Boutons pour changer l'état de filtrage -->
    <div class="mb-4">
        <button wire:click="setStatus('en_cours')" class="bg-yellow-500 text-white px-4 py-2 rounded">
            En cours
        </button>
        <button wire:click="setStatus('termine')" class="bg-green-500 text-white px-4 py-2 rounded ml-4">
            Terminé
        </button>
    </div>

    <!-- Table des formulaires d'émargement -->
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
        <tr>
            <th class="px-4 py-2 border">Nom du cours</th>
            <th class="px-4 py-2 border">Horaire</th>
            <th class="px-4 py-2 border">Date de l'événement</th>
            <th class="px-4 py-2 border">Formation</th>
            <th class="px-4 py-2 border">Parcours</th>
            <th class="px-4 py-2 border">Groupes</th>
            <th class="px-4 py-2 border">Signature du professeur</th>
        </tr>
        </thead>
        <tbody>
        @foreach($attendanceForms as $attendanceForm)
            <tr>
                <td class="px-4 py-2 border">{{ $attendanceForm->event_name }}</td>
                <td class="px-4 py-2 border">{{ $attendanceForm->event_start_hour }} / {{ $attendanceForm->event_end_hour }}</td>
                <td class="px-4 py-2 border">{{ $attendanceForm->event_date }}</td>
                <td class="px-4 py-2 border">{{ $attendanceForm->training->name }}</td>
                <td class="px-4 py-2 border">{{ $attendanceForm->course ? $attendanceForm->course->name : 'Parcours non renseigner' }}</td>
                <td class="px-4 py-2 border">{{ $attendanceForm->group ? $attendanceForm->group->name : 'Groupe non renseigner' }}</td>
                <td class="px-4 py-2 border">
                    @if($attendanceForm->signature_teacher)
                        <span class="text-green-500">Signé</span>
                    @else
                        <span class="text-red-500">Non signé</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
