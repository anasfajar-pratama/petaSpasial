import * as faceapi from '@vladmandic/face-api';

const MODEL_URL = '/js/models';

export async function initFaceCapture(options = {}) {
    const {
        videoId = 'face-video',
        canvasId = 'face-overlay',
        btnId = 'btn-capture',
        previewId = 'face-preview',
        inputId = 'foto_wajah_input',
        onCapture = null,
    } = options;

    const video = document.getElementById(videoId);
    const overlay = document.getElementById(canvasId);
    const btn = document.getElementById(btnId);
    const preview = document.getElementById(previewId);
    const hiddenInput = document.getElementById(inputId);

    if (!video || !overlay || !btn) return null;

    const ctx = overlay.getContext('2d');
    let stream = null;
    let animFrame = null;
    let faceDetected = false;

    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);

    function getOvalRect() {
        const w = overlay.width;
        const h = overlay.height;
        const ovalW = w * 0.5;
        const ovalH = h * 0.7;
        return {
            x: (w - ovalW) / 2,
            y: (h - ovalH) / 2,
            width: ovalW,
            height: ovalH,
        };
    }

    function drawOval() {
        const r = getOvalRect();
        ctx.clearRect(0, 0, overlay.width, overlay.height);

        ctx.fillStyle = 'rgba(0,0,0,0.4)';
        ctx.fillRect(0, 0, overlay.width, overlay.height);

        ctx.beginPath();
        ctx.ellipse(
            r.x + r.width / 2,
            r.y + r.height / 2,
            r.width / 2,
            r.height / 2,
            0, 0, Math.PI * 2
        );
        ctx.closePath();

        ctx.save();
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fill();
        ctx.restore();

        ctx.strokeStyle = faceDetected ? '#22c55e' : '#ef4444';
        ctx.lineWidth = 3;
        ctx.stroke();

        ctx.fillStyle = faceDetected ? '#22c55e' : '#ffffff';
        ctx.font = '14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(
            faceDetected ? 'Wajah terdeteksi ✓' : 'Posisikan wajah di dalam bingkai',
            overlay.width / 2,
            r.y + r.height + 30
        );
    }

    async function detectLoop() {
        const detections = await faceapi.detectAllFaces(
            video,
            new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 })
        );

        const r = getOvalRect();
        faceDetected = false;

        if (detections.length > 0) {
            const face = detections[0];
            const fw = face.box.width;
            const fh = face.box.height;
            const ovalArea = r.width * r.height;
            const faceArea = fw * fh;

            if (faceArea / ovalArea >= 0.2) {
                const faceCenterX = face.box.x + fw / 2;
                const faceCenterY = face.box.y + fh / 2;
                const distX = Math.abs(faceCenterX - (r.x + r.width / 2)) / (r.width / 2);
                const distY = Math.abs(faceCenterY - (r.y + r.height / 2)) / (r.height / 2);

                const insideOval = (distX * distX + distY * distY) <= 1;
                if (insideOval) {
                    faceDetected = true;
                }
            }
        }

        drawOval();
        btn.disabled = !faceDetected;
        btn.classList.toggle('opacity-50', !faceDetected);
        btn.classList.toggle('cursor-not-allowed', !faceDetected);

        animFrame = requestAnimationFrame(detectLoop);
    }

    btn.addEventListener('click', function () {
        const captureCanvas = document.createElement('canvas');
        captureCanvas.width = video.videoWidth;
        captureCanvas.height = video.videoHeight;
        const capCtx = captureCanvas.getContext('2d');

        const r = getOvalRect();
        capCtx.drawImage(video, 0, 0);

        const imageData = capCtx.getImageData(
            Math.round(r.x), Math.round(r.y),
            Math.round(r.width), Math.round(r.height)
        );

        const cropCanvas = document.createElement('canvas');
        cropCanvas.width = Math.round(r.width);
        cropCanvas.height = Math.round(r.height);
        const cropCtx = cropCanvas.getContext('2d');
        cropCtx.putImageData(imageData, 0, 0);

        const webpData = cropCanvas.toDataURL('image/webp', 0.9);

        if (hiddenInput) {
            hiddenInput.value = webpData;
        }

        if (preview) {
            preview.src = webpData;
            preview.classList.remove('hidden');
        }

        btn.textContent = '✓ Foto Diambil';
        btn.disabled = true;

        if (typeof onCapture === 'function') {
            onCapture(webpData);
        }
    });

    async function start() {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 }
        });
        video.srcObject = stream;
        await video.play();

        overlay.width = video.videoWidth;
        overlay.height = video.videoHeight;

        detectLoop();
    }

    function stop() {
        if (animFrame) cancelAnimationFrame(animFrame);
        if (stream) stream.getTracks().forEach(t => t.stop());
    }

    return { start, stop };
}
