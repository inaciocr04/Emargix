<div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
    <table id="table_emargement" class="w-full text-sm text-left border-collapse rounded-lg mt-5">
        <thead class="bg-gray-800 text-white">
        <tr>
            <th class="px-4 py-3">Nom</th>
            <th class="px-4 py-3">Statut</th>
            <th class="px-4 py-3">Formation</th>
            <th class="px-4 py-3">Parcours</th>
            <th class="px-4 py-3">Groupe TD</th>
            <th class="px-4 py-3">Groupe TP</th>
            <th class="px-4 py-3">Signature</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        @foreach($students as $student)
            <tr class="hover:bg-gray-100">
                <td class="px-4 py-3">{{ $student->firstname }} {{ $student->lastname }}</td>
                <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded
                            {{ $student->student_statu == 'Présent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->student_statu }}
                        </span>
                </td>
                <td class="px-4 py-3">{{ $student->training->name }}</td>
                <td class="px-4 py-3">{{ $student->course->name ?? 'Rien' }}</td>
                <td class="px-4 py-3">{{ $student->td_group->name ?? 'Rien' }}</td>
                <td class="px-4 py-3">{{ $student->tp_group->name ?? 'Rien' }}</td>
                <td class="px-4 py-3 flex items-center space-x-2">
                    @if($student->signatures_list->isNotEmpty())
                        <span class="text-green-500 font-semibold">✔ Signé</span>
                        @foreach($student->signatures_list as $signature)
                            <img class="w-12 h-12 rounded shadow-md" src="/{{ $signature->signature }}" alt="Signature">
                        @endforeach
                    @else
                        <span class="text-red-500 font-semibold">✘ Absent</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#table_emargement').DataTable({
            paging: true,
            searching: true,
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
            autoWidth: false,
            ordering: true,
            pageLength: 10, // Nombre de lignes par défaut
            lengthMenu: [5, 8, 10, 15], // Options de choix de lignes
            language: {
                "sEmptyTable": "Aucune donnée disponible",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ étudiants",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 étudiants",
                "sInfoFiltered": "(filtré de _MAX_ étudiants au total)",
                "sLengthMenu": "Afficher _MENU_ étudiants",
                "sLoadingRecords": "Chargement...",
                "sProcessing": "Traitement...",
                "sSearch": "Rechercher:",
                "sZeroRecords": "Aucun résultat trouvé",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sLast": "Dernier",
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    });


</script>
