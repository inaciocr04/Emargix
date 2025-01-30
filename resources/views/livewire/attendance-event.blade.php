<div>
    <!-- Table des étudiants -->
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
        <tr>
            <th class="px-4 py-2 border">Nom de l'étudiant</th>
            <th class="px-4 py-2 border">Status de l'étudiant</th>
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
                <td class="px-4 py-2 border">{{ $student->student_statu }}</td>
                <td class="px-4 py-2 border">{{ $student->training->name }}</td>
                <td class="px-4 py-2 border">{{ $student->course->name ?? 'Rien' }}</td>
                <td class="px-4 py-2 border">{{ $student->td_group->name ?? 'Rien' }}</td>
                <td class="px-4 py-2 border">{{ $student->tp_group->name ?? 'Rien' }}</td>
                <td class="px-4 py-2 border flex items-center">
                    @if($student->signatures_list->isNotEmpty())
                        <span class="text-green-500">Signé</span>
                        <!-- Affiche toutes les signatures si nécessaire -->
                        @foreach($student->signatures_list as $signature)
                            <img class="w-36" src="/{{ $signature->signature }}" alt="Signature de {{ $student->firstname }} {{ $student->lastname }}">
                        @endforeach
                    @else
                        <span class="text-red-500">Absent</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
