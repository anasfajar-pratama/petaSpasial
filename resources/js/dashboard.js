import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('mini-map');
    if (!mapEl) return;

    const map = L.map(mapEl, {
        center: [-6.9217, 106.9273],
        zoom: 11,
        zoomControl: false,
        attributionControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        keyboard: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    const warnaMap = {};
    let allFeatures = [];

    fetch('/api/map/layers')
        .then(r => r.json())
        .then(layers => {
            layers.forEach(l => { warnaMap[l.id] = l.warna; });
            const activeIds = layers.map(l => l.id).join(',');
            return fetch(`/api/map/data?sw_lat=-7.2&sw_lng=106.6&ne_lat=-6.6&ne_lng=107.3&layer_id=${activeIds}`);
        })
        .then(r => r.json())
        .then(geojson => {
            if (!geojson.features) return;
            allFeatures = geojson.features;

            const grouped = {};
            geojson.features.forEach(f => {
                const lid = f.properties?.layer_id;
                if (!lid) return;
                if (!grouped[lid]) grouped[lid] = [];
                grouped[lid].push(f);
            });

            Object.entries(grouped).forEach(([lid, feats]) => {
                const warna = warnaMap[lid] || '#3388ff';
                const fc = { type: 'FeatureCollection', features: feats.slice(0, 500) };
                const first = feats[0];
                const geomType = first?.geometry?.type || 'Point';
                const isPoint = geomType === 'Point' || geomType === 'MultiPoint';

                if (isPoint) {
                    feats.slice(0, 500).forEach(f => {
                        const c = f.geometry.coordinates;
                        L.circleMarker([c[1], c[0]], {
                            radius: 4, fillColor: warna, color: '#fff', weight: 1, opacity: 1, fillOpacity: 0.8,
                        }).addTo(map);
                    });
                } else {
                    L.geoJSON(fc, {
                        style: {
                            color: warna, weight: 2, opacity: 0.8, fillColor: warna, fillOpacity: 0.15,
                        },
                    }).addTo(map);
                }
            });

            if (geojson.features.length > 0) {
                const bounds = L.geoJSON({ type: 'FeatureCollection', features: geojson.features.slice(0, 1000) }).getBounds();
                if (bounds.isValid()) map.fitBounds(bounds, { padding: [20, 20] });
            }
        })
        .catch(() => {});

    mapEl.addEventListener('click', () => {
        window.location.href = '/map';
    });
});
