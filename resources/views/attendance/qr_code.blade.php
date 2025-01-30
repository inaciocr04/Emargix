<x-app-layout>
    <div class="border-2 border-black flex flex-col justify-center items-center space-y-8">
        <h1>Formulaire d'Émargement</h1>
        <p>Scannez le QR code ci-dessous pour accéder au formulaire d'émargement.</p>

        <!-- Afficher le QR code généré -->
        <div id="qrCodeContainer">
            {!! $qrCodeImage !!}
        </div>

        <p><strong>Scannez ce QR code pour marquer votre présence à cet événement !</strong></p>

        <div class="mt-4">
            <p><strong>Le QR code se rafraîchira dans <span id="countdown" class="text-2"></span> secondes.</strong></p>
        </div>

        <a href="{{ route('attendance.list', ['eventId' => $eventId]) }}" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">
            Voir la liste des présences
        </a>
    </div>

    <script>
        let countdownTime = 20;

        // Fonction pour actualiser la page toutes les 20 secondes
        setInterval(function() {
            location.reload();  // Recharger la page pour générer un nouveau QR code
        }, 20000); // Rafraîchir toutes les 20 secondes

        // Fonction pour afficher le compte à rebours
        function updateCountdown() {
            if (countdownTime > 0) {
                countdownTime--;
            }

            // Mettre à jour le texte du compte à rebours dans l'élément HTML
            document.getElementById('countdown').innerText = countdownTime;
        }

        // Mettre à jour le compte à rebours toutes les secondes
        setInterval(updateCountdown, 1000); // Chaque seconde (1000 millisecondes)
    </script>

</x-app-layout>
