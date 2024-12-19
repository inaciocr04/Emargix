<div class="border-2 border-gray-300 p-4">
    <h1 class="text-2xl font-bold mb-4">Liste des élèves présents</h1>

    @if($signatures->isEmpty())
        <p>Aucun élève n'a encore signé la feuille de présence pour cet événement.</p>
    @else
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
            <tr>
                <th class="px-4 py-2 border">Nom</th>
                <th class="px-4 py-2 border">Prénom</th>
                <th class="px-4 py-2 border">Heure de signature</th>
                <th class="px-4 py-2 border">Signature</th>
            </tr>
            </thead>
            <tbody>
            @foreach($signatures as $signature)
                <tr>
                    <td class="px-4 py-2 border">{{ $signature->student->lastname }}</td>
                    <td class="px-4 py-2 border">{{ $signature->student->firstname }}</td>
                    <td class="px-4 py-2 border">{{ $signature->created_at->format('H:i:s') }}</td>
                    <td class="px-4 py-2 border">
                        @if($signature->signature)
                            <img src="{{ asset($signature->signature) }}" alt="Signature" class="h-16">
                        @else
                            <span>Aucune signature</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
