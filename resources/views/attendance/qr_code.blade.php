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

        // Fonction pour actualiser le QR code toutes les 20 secondes et mettre à jour le compte à rebours
        setInterval(function() {
            var eventId = {{ $eventId }}; // L'ID de l'événement, passé depuis Laravel

            // Effectuer la requête AJAX pour récupérer le QR code mis à jour
            fetch(`/teacher/planning/generate-qr-code/${eventId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Assurez-vous d'envoyer une requête AJAX
                }
            })
                .then(response => response.json()) // On parse la réponse en JSON
                .then(data => {
                    // Injecter le QR code mis à jour dans le conteneur
                    document.getElementById('qrCodeContainer').innerHTML = data.qrCode;  // Utilisation de innerHTML pour insérer du HTML

                    // Mettre à jour le token si nécessaire, bien que ça ne soit pas obligatoire ici
                    // Si tu veux utiliser le token pour d'autres actions côté client, tu peux le stocker
                    // var token = data.token;
                })
                .catch(error => {
                    console.error('Erreur lors de la génération du QR code:', error);
                });

            // Réinitialiser le compte à rebours
            countdownTime = 20;
        }, 20000); // Rafraîchir toutes les 20 secondes (20000 millisecondes)

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
