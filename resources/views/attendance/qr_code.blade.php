<x-app-layout>
    <div class="border-2 border-black flex flex-col justify-center items-center space-y-8">
        <h1>Formulaire d'Émargement</h1>
        <p>Scannez le QR code ci-dessous pour accéder au formulaire d'émargement.</p>

        <!-- Afficher le QR code généré -->
        <div class="qr-code">
            {!! $qrCodeImage !!}
        </div>

        <p><strong>Scannez ce QR code pour marquer votre présence à cet événement !</strong></p>

        <a href="{{ route('attendance.list', ['eventId' => $eventId]) }}" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">
            Voir la liste des présences
        </a>
    </div>
</x-app-layout>
