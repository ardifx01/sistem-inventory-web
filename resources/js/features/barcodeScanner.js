export function initBarcodeScanner() {
    const barcodeModal = document.getElementById('barcodeModal');
    const openScannerBtn = document.getElementById('openScannerBtn');
    const closeScannerBtn = document.getElementById('closeScannerBtn');
    const searchInput = document.getElementById('search');

    let html5QrCode = null;

    function startScanner() {
        if (html5QrCode) return;
        try { html5QrCode = new Html5Qrcode("reader"); }
        catch (err) { console.error('Html5Qrcode init failed', err); return; }

        Html5Qrcode.getCameras()
            .then(devices => {
                const config = { fps: 10, qrbox: 250 };
                const camera = devices.length ? { facingMode: "environment" } : { facingMode: "user" };
                html5QrCode.start(camera, config, onScanSuccess, onScanError)
                    .catch(err => console.error('QR start failed:', err));
            })
            .catch(err => console.error('Camera error:', err));
    }

    function onScanSuccess(codeText) {
        if (searchInput) searchInput.value = codeText;
        safeStopScanner(true);
        document.getElementById('searchForm')?.submit();
    }

    function onScanError(_) {}

    function safeStopScanner(closeModal = false) {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
                if (closeModal) barcodeModal?.classList.add('hidden');
            }).catch(() => {
                html5QrCode = null;
                if (closeModal) barcodeModal?.classList.add('hidden');
            });
        } else if (closeModal) {
            barcodeModal?.classList.add('hidden');
        }
    }

    // Event Listeners
    openScannerBtn?.addEventListener('click', () => {
        barcodeModal?.classList.remove('hidden');
        startScanner();
    });

    closeScannerBtn?.addEventListener('click', () => safeStopScanner(true));

    barcodeModal?.addEventListener('click', (e) => {
        if (e.target === barcodeModal) safeStopScanner(true);
    });
}
