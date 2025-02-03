<x-app-layout title="Emargement pour {{$attendanceForm->event_name}}">
    <h2>Feuille d'émargement : {{$attendanceForm->event_name}}</h2>
    <h3>De {{$attendanceForm->event_start_hour}} à {{$attendanceForm->event_end_hour}} le {{$attendanceForm->event_date}}</h3>

    <!-- Affichage du nombre d'absents -->
    <p><strong>Nombre d'absents : </strong>{{ $absentCount }}</p>
    <p>Nombre de présents : {{ $presentCount }}</p>
    <livewire:attendance-event :eventId="$eventId"/>
    <livewire:attendance-access :eventId="$eventId"/>

    <!-- Formulaire de signature pour le professeur -->

    @if($attendanceForm->signature_teacher)
        <div class="mt-6 p-4 border-2 border-gray-300 bg-gray-50">
            <h2 class="text-xl font-bold">Signature du professeur</h2>
            <p><strong>Nom :</strong> {{ Auth::user()->name }}</p>
            <p><strong>Date :</strong> {{ $attendanceForm->updated_at->format('d/m/Y H:i') }}</p>
            <div class="mt-2">
                <img src="{{ asset($attendanceForm->signature_teacher) }}" alt="Signature du professeur" class="h-32">
            </div>
        </div>
    @else
        <!-- Formulaire de signature pour le professeur -->
        <h2 class="text-xl font-bold mt-6">Signer en tant que professeur</h2>
        <form method="POST" action="{{ route('teacherSignature.store', ['eventId' => $eventId]) }}">
            @csrf
            <div class="mb-4">
                <label for="signature">Votre signature :</label>
                <div class="border rounded">
                    <canvas id="signature-pad-teacher" class="border" width="400" height="200"></canvas>
                </div>
                <input type="hidden" id="signature" name="signature">
                <div class="mt-2">
                    <button type="button" id="clear-signature-teacher" class="bg-red-500 text-white py-1 px-2 rounded">Effacer</button>
                </div>
                @error('signature')
                <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Soumettre</button>
        </form>
    @endif

    <form action="{{ route('export', ['eventId' => $eventId]) }}" method="GET">
        <button type="submit" class="bg-blue-600">
            Exporter la présence
        </button>
    </form>
    <form action="{{ route('export.attendance', ['eventId' => $eventId]) }}" method="GET">
        <button type="submit" class="bg-blue-600">
            Exporter la présence en pdf
        </button>
    </form>




    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signature-pad-teacher'); // Canvas ID mis à jour
            const signaturePad = new SignaturePad(canvas);

            const form = document.querySelector('form');
            form.addEventListener('submit', (event) => {
                if (signaturePad.isEmpty()) {
                    alert("Veuillez signer avant de soumettre !");
                    event.preventDefault();
                } else {
                    const signatureInput = document.getElementById('signature');
                    signatureInput.value = signaturePad.toDataURL('image/png');
                }
            });

            document.getElementById('clear-signature-teacher').addEventListener('click', () => {
                signaturePad.clear();
            });
        });
    </script>
</x-app-layout>
