document.addEventListener('DOMContentLoaded', function () {
    const inputGaleri = document.getElementById('input-galeri');
    const inputKamera = document.getElementById('input-kamera');
    const fotoPreview = document.getElementById('foto-preview');
    let fotoDipilih = false;

    // Tombol galeri -> buka dialog file (tanpa capture)
    document.getElementById('btn-galeri')?.addEventListener('click', () => inputGaleri?.click());

    // Tombol kamera -> buka kamera langsung (capture environment)
    document.getElementById('btn-kamera')?.addEventListener('click', () => inputKamera?.click());

    function handleFotoPilih(input) {
        if (!input || !input.files || !input.files[0]) return;
        const file = input.files[0];
        fotoDipilih = true;

        // Tandai input aktif: hanya input yang dipilih yang punya name="foto"
        inputGaleri.name = input === inputGaleri ? 'foto' : '';
        inputKamera.name = input === inputKamera ? 'foto' : '';

        // Kosongkan input satunya agar hanya satu foto terkirim
        const other = input === inputGaleri ? inputKamera : inputGaleri;
        if (other) other.value = '';

        // Preview
        if (fotoPreview) {
            fotoPreview.src = URL.createObjectURL(file);
            fotoPreview.classList.remove('hidden');
        }

        // Agar browser mengirim file dari input yang benar
        if (input === inputGaleri) inputGaleri.name = 'foto';
        else inputGaleri.name = '';
    }

    inputGaleri?.addEventListener('change', () => handleFotoPilih(inputGaleri));
    inputKamera?.addEventListener('change', () => handleFotoPilih(inputKamera));

    // --- Lokasi saat ini ---
    const btnLokasi = document.getElementById('btn-lokasi');
    const lokasiTeks = document.getElementById('lokasi-teks');
    const inputLat = document.getElementById('latitude');
    const inputLng = document.getElementById('longitude');
    const inputMaps = document.getElementById('maps_link');

    btnLokasi?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            if (lokasiTeks) lokasiTeks.textContent = 'Browser tidak mendukung geolokasi.';
            return;
        }
        if (lokasiTeks) lokasiTeks.textContent = 'Mendeteksi lokasi...';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude.toFixed(7);
                const lng = pos.coords.longitude.toFixed(7);
                inputLat.value = lat;
                inputLng.value = lng;
                if (inputMaps) inputMaps.value = `https://www.google.com/maps?q=${lat},${lng}`;
                if (lokasiTeks) lokasiTeks.textContent = `Lokasi terdeteksi: ${lat}, ${lng}`;
            },
            () => {
                if (lokasiTeks) lokasiTeks.textContent = 'Gagal mendapatkan lokasi. Periksa izin lokasi browser.';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // --- Parse link Google Maps ---
    function parseMapsLink(url) {
        const match = url.match(/[-+]?\d{1,3}\.\d+,\s*[-+]?\d{1,3}\.\d+/);
        return match ? { lat: match[0].split(',')[0].trim(), lng: match[0].split(',')[1].trim() } : null;
    }

    inputMaps?.addEventListener('blur', () => {
        if (!inputMaps.value.trim()) return;
        const coord = parseMapsLink(inputMaps.value);
        if (coord) {
            inputLat.value = coord.lat;
            inputLng.value = coord.lng;
            if (lokasiTeks) lokasiTeks.textContent = `Koordinat terdeteksi: ${coord.lat}, ${coord.lng}`;
        }
    });
});
