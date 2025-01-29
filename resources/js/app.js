import './bootstrap';
import { Html5Qrcode } from "html5-qrcode";

function onScanSuccess(decodedText, decodedResult) {
    // Log pour vérifier le texte décodé
    console.log(`Code QR scanné: ${decodedText}`);

    // Rediriger l'utilisateur vers l'URL contenue dans le QR code
    window.location.href = decodedText; // Cette ligne effectue la redirection
}

function onScanFailure(error) {
    // Appelée en cas d'échec de la lecture du code QR
    console.warn(`Erreur de scan: ${error}`);
}

const html5QrCode = new Html5Qrcode("reader");

html5QrCode.start(
    { facingMode: "environment" }, // Option pour utiliser la caméra arrière
    {
        fps: 10,    // Vitesse des frames
        qrbox: 250  // Taille de la zone de scan
    },
    onScanSuccess,
    onScanFailure
);
