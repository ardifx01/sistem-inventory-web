import './bootstrap';

import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;

document.addEventListener("DOMContentLoaded", function () {
    const scanButton = document.getElementById("scanButton");
    const qrReader = document.getElementById("qr-reader");
    const searchInput = document.getElementById("search");

    if (scanButton && qrReader) {
        scanButton.addEventListener("click", function () {
            qrReader.style.display = "block";

            const html5QrCode = new Html5Qrcode("qr-reader");
            const qrConfig = { fps: 10, qrbox: 250 };

            html5QrCode.start(
                { facingMode: "environment" },
                qrConfig,
                (decodedText) => {
                    searchInput.value = decodedText;
                    html5QrCode.stop();
                    qrReader.style.display = "none";
                },
                (errorMessage) => {
                    console.log("Scanning...", errorMessage);
                }
            ).catch((err) => {
                console.error("Unable to start scanning.", err);
            });
        });
    }
});

Alpine.start();
