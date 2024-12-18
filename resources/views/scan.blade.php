<x-app-layout>
    <h1>Scanner un QR Code</h1>
    <div id="qr-reader"></div>
    <div id="qr-reader-results"></div>

    <script>
        const qrReader = new Html5Qrcode("qr-reader");

        qrReader.start(
            { facingMode: "environment" }, // Utilise la caméra arrière
            {
                fps: 10,                    // Fréquence de détection (10 images/sec)
                qrbox: { width: 250, height: 250 }, // Zone de scan
            },
            (decodedText, decodedResult) => {
                // Une fois le QR Code scanné, redirige ou affiche les résultats
                console.log(`Code scanné : ${decodedText}`);
                window.location.href = decodedText; // Redirige vers l'URL scannée
            },
            (errorMessage) => {
                // Erreur dans le scan
                console.warn(`Erreur : ${errorMessage}`);
            }
        ).catch((err) => {
            console.error(`Erreur au démarrage du scanner : ${err}`);
        });
    </script>
</x-app-layout>
