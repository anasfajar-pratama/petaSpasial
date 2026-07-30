import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('mini-map');
    if (!mapEl) return;

    const districtFilter = mapEl.dataset.district || '';

    const isSatellite = !!districtFilter;

    const map = L.map(mapEl, {
        center: [-6.9217, 106.9273],
        zoom: isSatellite ? 14 : 11,
        zoomControl: false,
        attributionControl: false,
        dragging: !isSatellite,
        scrollWheelZoom: !isSatellite,
        doubleClickZoom: !isSatellite,
        touchZoom: !isSatellite,
        keyboard: !isSatellite,
    });

    if (isSatellite) {
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: '&copy; Esri',
        }).addTo(map);
    } else {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
    }

    if (isSatellite && districtFilter) {
        L.circleMarker(map.getCenter(), {
            radius: 8,
            color: '#ef4444',
            fillColor: '#ef4444',
            fillOpacity: 0.3,
            weight: 2,
        }).addTo(map).bindTooltip('Kecamatan terpilih', { direction: 'top' });
    }

    const warnaMap = {};
    let allFeatures = [];

    fetch('/api/map/layers')
        .then(r => r.json())
        .then(layers => {
            layers.forEach(l => { warnaMap[l.id] = l.warna; });
            const activeIds = layers.map(l => l.id).join(',');
            let url = `/api/map/data?sw_lat=-7.2&sw_lng=106.6&ne_lat=-6.6&ne_lng=107.3&layer_id=${activeIds}&limit=1000`;
            if (districtFilter) {
                url += `&district_id=${districtFilter}`;
            }
            return fetch(url);
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
                    L.geoJSON({ type: 'FeatureCollection', features: feats.slice(0, 500) }, {
                        style: {
                            color: warna, weight: 2, opacity: 0.8, fillColor: warna, fillOpacity: 0.15,
                        },
                    }).addTo(map);
                }
            });

            if (geojson.features.length > 0) {
                const bounds = L.geoJSON({ type: 'FeatureCollection', features: geojson.features.slice(0, 1000) }).getBounds();
                if (bounds.isValid()) {
                    if (isSatellite) {
                        map.fitBounds(bounds, { padding: [30, 30], maxZoom: 16 });
                    } else {
                        map.fitBounds(bounds, { padding: [20, 20] });
                    }
                }
            }
        })
        .catch(() => {});

    mapEl.addEventListener('click', () => {
        window.location.href = '/map';
    });
});
