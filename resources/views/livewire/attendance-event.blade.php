<div>
    <!-- Table des étudiants -->
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
        <tr>
            <th class="px-4 py-2 border">Nom de l'étudiant</th>
            <th class="px-4 py-2 border">Formation</th>
            <th class="px-4 py-2 border">Parcours</th>
            <th class="px-4 py-2 border">Groupe de TD</th>
            <th class="px-4 py-2 border">Groupe de TP</th>
            <th class="px-4 py-2 border">Statut de la signature</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
            <tr>
                <td class="px-4 py-2 border">{{ $student->firstname }} {{ $student->lastname }}</td>
                <td class="px-4 py-2 border">{{ $student->training->name }}</td>
                <td class="px-4 py-2 border">{{ $student->course->name ?? 'rien' }}</td>
                <td class="px-4 py-2 border">{{ $student->td_group->name ?? 'rien' }}</td>
                <td class="px-4 py-2 border">{{ $student->tp_group->name ?? 'rien' }}</td>
                <td class="px-4 py-2 border">
                    @if($student->signature_status === 'Signé')
                        <span class="text-green-500">Signé</span>
                    @else
                        <span class="text-red-500">Absent</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
