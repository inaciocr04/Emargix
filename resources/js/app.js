import './bootstrap';
import { Html5Qrcode } from "html5-qrcode";

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code QR scanné: ${decodedText}`);

    window.location.href = decodedText;
}

function onScanFailure(error) {
    console.warn(`Erreur de scan: ${error}`);
}

document.addEventListener("DOMContentLoaded", function() {
    const html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250,
        },
        onScanSuccess,
        onScanFailure
    ).catch((err) => {
        console.error("Erreur lors du démarrage du scanner : ", err);
    });
});
