<x-app-layout>
    <div class="border-2 border-black flex flex-col justify-center items-center space-y-8">
        <h1>Formulaire d'Émargement</h1>
        <p>Scannez le QR code ci-dessous pour accéder au formulaire d'émargement.</p>

        <!-- Afficher le QR code généré -->
        <div class="qr-code">
            {!! $qrCodeImage !!}
        </div>

        <p><strong>Scannez ce QR code pour marquer votre présence à cet événement !</strong></p>
    </div>
</x-app-layout>
