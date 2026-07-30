import { initFaceCapture } from './face-capture';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('kritik-saran-form');
    const submitBtn = document.getElementById('btn-submit');
    const formFields = document.getElementById('form-fields');
    const cameraSection = document.getElementById('face-capture-section');
    const verifikasiMsg = document.getElementById('verifikasi-message');
    let capture = null;

    if (!form || !submitBtn || !formFields || !cameraSection) return;

    submitBtn.addEventListener('click', async function () {
        const nama = document.querySelector('[name="nama"]');
        const email = document.querySelector('[name="email"]');
        const pesan = document.querySelector('[name="pesan"]');

        const errors = [];
        if (!nama.value.trim()) errors.push('Nama lengkap wajib diisi.');
        if (!email.value.trim()) errors.push('Email wajib diisi.');
        if (!pesan.value.trim() || pesan.value.trim().length < 10) errors.push('Pesan / Saran minimal 10 karakter.');

        const errorContainer = document.getElementById('client-errors');
        if (errorContainer) errorContainer.remove();

        if (errors.length > 0) {
            const div = document.createElement('div');
            div.id = 'client-errors';
            div.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6';
            div.innerHTML = '<ul class="list-disc pl-5">' + errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
            form.insertBefore(div, form.firstChild);
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
            return;
        }

        formFields.classList.add('hidden');
        cameraSection.classList.remove('hidden');
        if (verifikasiMsg) verifikasiMsg.classList.remove('hidden');
        window.scrollTo({ top: cameraSection.offsetTop - 20, behavior: 'smooth' });

        capture = await initFaceCapture({
            onCapture: function () {
                form.submit();
            }
        });

        if (capture) {
            capture.start();
        }
    });
});
