<x-app-layout>
    <h1>Signer votre présence</h1>

    <p>Événement ID : {{ $attendanceForm->event_id }}</p>
    <p>Nom de l'évènement : {{ $attendanceForm->event_name }}</p>
    <p>Date : {{ $attendanceForm->event_date }}</p>

    <form action="{{ route('studentSignature.store', ['studentId' => $studentId, 'attendanceFormId' => $attendanceFormId])}}" method="POST">
        @csrf

        <div>
            <label for="student_name">Nom de l'étudiant :</label>
            <input type="text" id="student_name" name="student_name" value="{{ Auth::user()->name }}" readonly>
        </div>

        <!-- Zone de signature -->
        <div class="mb-4">
            <label for="signature">Votre signature :</label>
            <div class="border rounded">
                <canvas id="signature-pad" class="border" width="400" height="200"></canvas>
            </div>
            <input type="hidden" id="signature" name="signature">
            <div class="mt-2">
                <button type="button" id="clear-signature" class="bg-red-500 text-white py-1 px-2 rounded">Effacer</button>
            </div>
            @error('signature')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Bouton pour soumettre -->
        <button
            type="submit"
            class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
            Enregistrer la présence
        </button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // Initialisation de Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        // Sauvegarde la signature en base64 lors de l'envoi du formulaire
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

        // Effacer la signature
        document.getElementById('clear-signature').addEventListener('click', () => {
            signaturePad.clear();
        });
    </script>
</x-app-layout>
