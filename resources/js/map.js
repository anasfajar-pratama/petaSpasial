import 'leaflet/dist/leaflet.css';
import 'leaflet.fullscreen/dist/Control.FullScreen.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet-draw/dist/leaflet.draw.css';

import L from 'leaflet';
import 'leaflet.fullscreen';
import 'leaflet.markercluster';
import 'leaflet-draw';

L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
    iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
    shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
});

const MapManager = {
    map: null,
    layerGroups: {},
    layerBoundsCache: {},
    activeLayers: [],
    layerLoading: {},
    drawnItems: null,
    currentLayerId: null,
    refreshDebounce: null,
    canEdit: false,
    searchLayer: null,
    radiusCircle: null,
    radiusCenter: null,
    radiusLatLng: null,
    bufferActive: false,
    searchTimer: null,

    init(containerId = 'map') {
        this.canEdit = document.getElementById('draw-layer-select') !== null;

        this.map = L.map(containerId, {
            center: [-6.9217, 106.9273],
            zoom: 12,
            zoomControl: false,
            fullscreenControl: true,
        });

        L.control.zoom({ position: 'topright' }).addTo(this.map);
        L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(this.map);

        this.searchLayer = L.featureGroup().addTo(this.map);

        this.initBasemaps();
        this.initCoordinateDisplay();
        this.loadLayers();
        this.initDraw();
        this.initDigitasiModal();
        this.initSearch();
        this.initRadius();

        this.map.on('mousemove', (e) => {
            const coordEl = document.getElementById('mouse-coord');
            if (coordEl) coordEl.textContent = `Lat: ${e.latlng.lat.toFixed(6)}, Lng: ${e.latlng.lng.toFixed(6)}`;
            const zoomEl = document.getElementById('zoom-level');
            if (zoomEl) zoomEl.textContent = `Zoom: ${this.map.getZoom()}`;
        });

        this.refreshDebounce = this._debounce(() => this.refreshVisibleLayers(), 400);
        this.map.on('moveend', () => this.refreshDebounce());
    },

    _debounce(fn, ms) {
        let t;
        return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
    },

    _getBbox() {
        const b = this.map.getBounds();
        const sw = b.getSouthWest();
        const ne = b.getNorthEast();
        const lngPad = (ne.lng - sw.lng) * 0.3;
        const latPad = (ne.lat - sw.lat) * 0.3;
        return {
            sw_lat: sw.lat - latPad, sw_lng: sw.lng - lngPad,
            ne_lat: ne.lat + latPad, ne_lng: ne.lng + lngPad,
        };
    },

    initBasemaps() {
        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap',
        }).addTo(this.map);

        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19, attribution: '&copy; Esri',
        });

        const dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19, attribution: '&copy; CARTO',
        });

        const basemaps = { 'Street': osm, 'Satellite': satellite, 'Dark': dark };

        document.querySelectorAll('[data-basemap]').forEach((el) => {
            el.addEventListener('click', () => {
                this.map.eachLayer((layer) => { if (layer._isBasemap) this.map.removeLayer(layer); });
                const key = el.dataset.basemap;
                if (basemaps[key]) { basemaps[key]._isBasemap = true; this.map.addLayer(basemaps[key]); }
            });
        });
    },

    initCoordinateDisplay() {
        const bottomBar = document.getElementById('bottom-bar');
        if (!bottomBar) return;
        const coordEl = document.createElement('span');
        coordEl.id = 'mouse-coord'; coordEl.className = 'text-xs text-gray-300';
        coordEl.textContent = 'Lat: -, Lng: -';
        bottomBar.appendChild(coordEl);
        const sep = document.createElement('span');
        sep.className = 'text-xs text-gray-500 mx-2'; sep.textContent = '|';
        bottomBar.appendChild(sep);
        const zoomEl = document.createElement('span');
        zoomEl.id = 'zoom-level'; zoomEl.className = 'text-xs text-gray-300';
        zoomEl.textContent = 'Zoom: 12';
        bottomBar.appendChild(zoomEl);
    },

    async loadLayers() {
        try {
            const res = await fetch('/api/map/layers');
            const layers = await res.json();
            this.activeLayers = layers;
            this.renderLayerList(layers);
            this.populateDrawSelect(layers);
            this.populateLayerFilter(layers);
        } catch (e) { console.error('Gagal load layers:', e); }
    },

    populateLayerFilter(layers) {
    },

    initSearch() {
        const input = document.getElementById('search-input');
        const kategori = document.getElementById('filter-kategori');
        const district = document.getElementById('filter-district');
        const village = document.getElementById('filter-village');
        const clearBtn = document.getElementById('btn-clear');
        const clearResults = document.getElementById('btn-clear-results');

        if (window._REFERENSI) {
            if (kategori && window._REFERENSI.kategori) {
                window._REFERENSI.kategori.forEach(k => {
                    kategori.innerHTML += `<option value="${k.id}">${k.nama}</option>`;
                });
            }
            if (district && window._REFERENSI.districts) {
                window._REFERENSI.districts.forEach(d => {
                    district.innerHTML += `<option value="${d.id}">${d.nama}</option>`;
                });
            }
        }

        if (district && village) {
            district.addEventListener('change', () => this.loadVillages(district.value, village));
        }

        const triggerSearch = () => this.queueSearch();
        if (input) input.addEventListener('input', triggerSearch);
        if (kategori) kategori.addEventListener('change', triggerSearch);
        if (district) district.addEventListener('change', triggerSearch);
        if (village) village.addEventListener('change', triggerSearch);

        const statusEl = document.getElementById('filter-status');
        if (statusEl) statusEl.addEventListener('change', triggerSearch);

        if (clearBtn) clearBtn.addEventListener('click', () => this.resetAll());
        if (clearResults) clearResults.addEventListener('click', () => this.clearSearch());
    },

    async loadVillages(districtId, selectEl) {
        selectEl.innerHTML = '<option value="">Desa</option>';
        selectEl.disabled = !districtId;
        if (!districtId) return;
        try {
            const res = await fetch(`/api/villages?district_id=${districtId}`);
            const data = await res.json();
            data.forEach(v => { selectEl.innerHTML += `<option value="${v.id}">${v.nama}</option>`; });
        } catch (e) {}
    },

    queueSearch() {
        clearTimeout(this.searchTimer);
        this.searchTimer = setTimeout(() => this.doSearch(), 300);
    },

    async doSearch() {
        const input = document.getElementById('search-input');
        const kategori = document.getElementById('filter-kategori');
        const statusEl = document.getElementById('filter-status');
        const district = document.getElementById('filter-district');
        const village = document.getElementById('filter-village');
        const spinner = document.getElementById('search-spinner');

        const q = input?.value?.trim() || '';
        if (!q && !kategori?.value && !statusEl?.value && !district?.value && !village?.value) {
            this.clearSearch();
            return;
        }

        if (spinner) spinner.classList.remove('hidden');

        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (kategori?.value) params.set('kategori_id', kategori.value);
        if (statusEl?.value) params.set('status', statusEl.value);
        if (district?.value) params.set('district_id', district.value);
        if (village?.value) params.set('village_id', village.value);

        try {
            const res = await fetch(`/api/map/search?${params}`);
            const data = await res.json();
            this.showSearchResults(data);
        } catch (e) {
            console.error('Search failed:', e);
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
    },

    showSearchResults(data) {
        this.clearBuffer();
        this.searchLayer.clearLayers();

        const info = document.getElementById('search-info');
        const count = document.getElementById('search-count');
        if (!data.features || data.features.length === 0) {
            if (info) info.classList.add('hidden');
            if (count) count.textContent = 'Tidak ditemukan';
            return;
        }

        const bounds = [];
        data.features.forEach(f => {
            const props = f.properties || {};
            const warna = props.layer_warna || '#fbbf24';
            const coords = f.geometry.coordinates;
            let layer;

            if (f.geometry.type === 'Point' || f.geometry.type === 'MultiPoint') {
                const c = f.geometry.type === 'Point' ? coords : coords[0];
                layer = L.circleMarker([c[1], c[0]], {
                    radius: 10, fillColor: '#fbbf24', color: '#000', weight: 2, opacity: 1, fillOpacity: 0.9,
                });
                bounds.push([c[1], c[0]]);
            } else {
                layer = L.geoJSON(f, {
                    style: { color: '#fbbf24', weight: 4, opacity: 1, fillColor: '#fbbf24', fillOpacity: 0.2 },
                });
                const b = layer.getBounds();
                if (b.isValid()) bounds.push(b.getCenter());
            }

            if (layer) {
                const popupHtml = `<div class="min-w-[180px]">
                    <p class="font-semibold text-sm mb-1">${props.nama || '-'}</p>
                    <p class="text-xs text-gray-500">Layer: ${props.layer_nama || '-'}</p>
                    ${props.tahun ? `<p class="text-xs text-gray-500">Tahun: ${props.tahun}</p>` : ''}
                    </div>`;
                layer.bindPopup(popupHtml);
                layer._dataId = props.id;
                this.searchLayer.addLayer(layer);
            }
        });

        if (bounds.length > 0) {
            this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
        }

        if (info) info.classList.remove('hidden');
        if (count) count.textContent = `${data.total} hasil ditemukan`;
    },

    clearSearch() {
        this.searchLayer.clearLayers();
        const info = document.getElementById('search-info');
        if (info) info.classList.add('hidden');
    },

    initRadius() {
        const btn = document.getElementById('btn-radius');
        const modal = document.getElementById('radius-modal');
        if (!btn || !modal) return;

        btn.addEventListener('click', () => {
            this.bufferActive = !this.bufferActive;
            btn.classList.toggle('bg-blue-600', this.bufferActive);
            btn.classList.toggle('border-blue-500', this.bufferActive);
            btn.textContent = this.bufferActive ? '📍 Klik peta' : '🔘 Radius';

            if (this.bufferActive) {
                this.map.once('click', (e) => {
                    if (!this.bufferActive) return;
                    this.radiusLatLng = e.latlng;
                    if (this.radiusCenter) this.map.removeLayer(this.radiusCenter);
                    this.radiusCenter = L.circleMarker([e.latlng.lat, e.latlng.lng], {
                        radius: 6, color: '#dc2626', fillColor: '#dc2626', fillOpacity: 1, weight: 2,
                    }).addTo(this.map).bindPopup('Pusat radius').openPopup();

                    document.getElementById('radius-modal').classList.remove('hidden');
                    document.getElementById('radius-modal').classList.add('flex');
                });
            } else {
                this.clearBuffer();
            }
        });

        document.getElementById('radius-batal')?.addEventListener('click', () => {
            document.getElementById('radius-modal').classList.add('hidden');
            document.getElementById('radius-modal').classList.remove('flex');
            this.clearBuffer();
        });

        document.getElementById('radius-cari')?.addEventListener('click', () => {
            const radius = parseInt(document.getElementById('radius-value')?.value) || 1000;
            if (!this.radiusLatLng) return;
            document.getElementById('radius-modal').classList.add('hidden');
            document.getElementById('radius-modal').classList.remove('flex');
            this.doBufferSearch(this.radiusLatLng.lat, this.radiusLatLng.lng, radius);
        });
    },

    async doBufferSearch(lat, lng, radius) {
        try {
            const res = await fetch('/api/map/buffer', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: JSON.stringify({ lat, lng, radius }),
            });
            const data = await res.json();
            this.showBufferResults(data);
        } catch (e) {
            alert('Gagal mencari radius.');
        }
    },

    showBufferResults(data) {
        this.clearSearch();
        this.searchLayer.clearLayers();
        this.clearBuffer();

        if (this.radiusCenter) {
            this.radiusCenter = L.circleMarker([data.center.lat, data.center.lng], {
                radius: 6, color: '#dc2626', fillColor: '#dc2626', fillOpacity: 1, weight: 2,
            }).addTo(this.map).bindPopup('Pusat radius').openPopup();
        }

        this.radiusCircle = L.circle([data.center.lat, data.center.lng], {
            radius: data.radius, color: '#dc2626', fillColor: '#dc2626', fillOpacity: 0.08, weight: 2, dashArray: '5,5',
        }).addTo(this.map);
        this.map.fitBounds(this.radiusCircle.getBounds(), { padding: [30, 30] });

        const info = document.getElementById('search-info');
        const count = document.getElementById('search-count');
        if (info) info.classList.remove('hidden');

        if (!data.features || data.features.length === 0) {
            if (count) count.textContent = `Tidak ada data dalam radius ${data.radius}m`;
            return;
        }

        if (count) count.textContent = `${data.total} hasil dalam radius ${data.radius}m`;

        data.features.forEach(f => {
            const props = f.properties || {};
            const coords = f.geometry.coordinates;
            let layer;

            if (f.geometry.type === 'Point' || f.geometry.type === 'MultiPoint') {
                const c = f.geometry.type === 'Point' ? coords : coords[0];
                layer = L.circleMarker([c[1], c[0]], {
                    radius: 8, fillColor: '#f97316', color: '#fff', weight: 2, fillOpacity: 0.9,
                });
                layer.bindPopup(`<div class="min-w-[150px]">
                    <p class="font-semibold text-sm">${props.nama || '-'}</p>
                    <p class="text-xs text-gray-500">Jarak: ${props.distance ? Math.round(props.distance) + 'm' : '-'}</p>
                </div>`);
                this.searchLayer.addLayer(layer);
            } else {
                layer = L.geoJSON(f, {
                    style: { color: '#f97316', weight: 3, fillColor: '#f97316', fillOpacity: 0.15 },
                });
                this.searchLayer.addLayer(layer);
            }
        });
    },

    clearBuffer() {
        this.bufferActive = false;
        const btn = document.getElementById('btn-radius');
        if (btn) { btn.classList.remove('bg-blue-600', 'border-blue-500'); btn.textContent = '🔘 Radius'; }
        if (this.radiusCircle) { this.map.removeLayer(this.radiusCircle); this.radiusCircle = null; }
        if (this.radiusCenter) { this.map.removeLayer(this.radiusCenter); this.radiusCenter = null; }
        this.radiusLatLng = null;
    },

    resetAll() {
        this.clearSearch();
        this.clearBuffer();
        const input = document.getElementById('search-input');
        if (input) input.value = '';
        ['filter-kategori', 'filter-status', 'filter-district', 'filter-village'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const village = document.getElementById('filter-village');
        if (village) { village.innerHTML = '<option value="">Desa</option>'; village.disabled = true; }
    },

    renderLayerList(layers) {
        const container = document.getElementById('layer-list');
        if (!container) return;
        container.innerHTML = '';

        const header = document.createElement('div');
        header.className = 'flex items-center justify-between pb-2 border-b border-gray-700 mb-2';
        header.innerHTML = `
            <span class="text-xs font-semibold text-gray-400 uppercase">Daftar Layer</span>
            <button data-zoom-all class="text-xs text-blue-400 hover:text-blue-300 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Zoom All
            </button>
        `;
        container.appendChild(header);
        header.querySelector('[data-zoom-all]').addEventListener('click', () => this.zoomToAll());

        layers.forEach((layer) => {
            const item = document.createElement('div');
            item.dataset.layerId = layer.id;
            item.className = 'flex items-center px-3 py-2 hover:bg-gray-700 rounded group';
            item.innerHTML = `
                <input type="checkbox" class="rounded border-gray-500 mr-2">
                <span class="layer-spinner hidden w-3 h-3 mr-2">
                    <svg class="animate-spin text-blue-400 w-3 h-3" viewBox="0 0 16 16" fill="none">
                        <circle class="opacity-25" cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/>
                        <path class="opacity-75" fill="currentColor" d="M8 0a8 8 0 018 8h-2a6 6 0 00-6-6V0z"/>
                    </svg>
                </span>
                <span class="geom-preview mr-2 inline-flex items-center justify-center w-5 h-5 rounded" style="background:${layer.warna}20; border:1px solid ${layer.warna}">
                    ${layer.geom_type === 'Point'
                        ? `<svg class="w-2.5 h-2.5" fill="${layer.warna}" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3.5"/></svg>`
                        : layer.geom_type === 'LineString'
                        ? `<svg class="w-3.5 h-2" viewBox="0 0 14 4"><line x1="0" y1="2" x2="14" y2="2" stroke="${layer.warna}" stroke-width="2"/></svg>`
                        : `<svg class="w-3 h-3" viewBox="0 0 6 6"><polygon points="3,0 6,6 0,6" fill="${layer.warna}" opacity="0.5" stroke="${layer.warna}" stroke-width="0.5"/></svg>`
                    }
                </span>
                <span class="text-sm text-gray-200 flex-1 truncate">${layer.nama}</span>
                <button data-zoom-layer="${layer.id}" class="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-white transition mr-1" title="Zoom ke layer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            `;
            container.appendChild(item);

            const checkbox = item.querySelector('input');
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    this.addLayerToMap(layer);
                } else {
                    this.removeLayerFromMap(layer.id);
                }
            });

            item.querySelector('[data-zoom-layer]').addEventListener('click', (e) => {
                e.stopPropagation();
                this.zoomToLayer(layer.id);
            });
        });
    },

    setLayerLoading(layerId, busy) {
        const item = document.querySelector(`[data-layer-id="${layerId}"]`);
        if (!item) return;
        const spinner = item.querySelector('.layer-spinner');
        if (spinner) spinner.classList.toggle('hidden', !busy);
    },

    async addLayerToMap(layer) {
        if (this.layerLoading[layer.id]) return;
        this.layerLoading[layer.id] = true;
        this.setLayerLoading(layer.id, true);

        try {
            const bbox = this._getBbox();
            const params = new URLSearchParams({ ...bbox, layer_id: layer.id });
            const res = await fetch(`/api/map/data?${params}`);
            const geojson = await res.json();
            this.renderGeoJSON(layer, geojson);
        } catch (e) {
            console.error('Gagal load layer:', layer.nama, e);
        } finally {
            this.layerLoading[layer.id] = false;
            this.setLayerLoading(layer.id, false);
        }
    },

    renderGeoJSON(layer, geojson) {
        this.removeLayerFromMap(layer.id);

        if (!geojson.features || geojson.features.length === 0) return;

        if (geojson.features.length > 0) {
            this.layerBoundsCache[layer.id] = L.geoJSON(geojson).getBounds();
        }

        let geoLayer;
        if (layer.geom_type === 'Point') {
            geoLayer = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 50 });
            geojson.features.forEach((f) => {
                const c = f.geometry.coordinates;
                const m = L.circleMarker([c[1], c[0]], {
                    radius: 8, fillColor: layer.warna, color: '#fff', weight: 2, opacity: 1, fillOpacity: layer.opacity,
                });
                m._dataId = f.properties?.id;
                m._layerId = layer.id;
                m.bindPopup(this.buildPopup(f.properties, layer.id));
                if (this.canEdit) m.on('popupopen', () => this.onPopupOpen(m));
                geoLayer.addLayer(m);
            });
        } else {
            geoLayer = L.geoJSON(geojson, {
                style: {
                    color: layer.warna, weight: layer.geom_type === 'LineString' ? 3 : 2,
                    opacity: layer.opacity, fillColor: layer.warna, fillOpacity: layer.geom_type === 'Polygon' ? layer.opacity * 0.3 : 0,
                },
                onEachFeature: (f, fl) => {
                    if (f.properties) {
                        fl._dataId = f.properties.id;
                        fl._layerId = layer.id;
                        fl.bindPopup(this.buildPopup(f.properties, layer.id));
                        if (this.canEdit) fl.on('popupopen', () => this.onPopupOpen(fl));
                    }
                },
            });
        }
        geoLayer._layerId = layer.id;
        geoLayer.addTo(this.map);
        this.layerGroups[layer.id] = geoLayer;
    },

    onPopupOpen(layer) {
        layer.off('popupopen');
        const popup = layer.getPopup();
        if (!popup) return;
        const container = popup.getElement();
        if (!container) return;
        container.querySelector('[data-edit]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.editFeature(layer._dataId, layer._layerId, layer.getLatLng ? layer.getLatLng() : null);
        });
        container.querySelector('[data-delete]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.deleteFeature(layer._dataId, layer._layerId);
        });
    },

    editFeature(dataId, layerId) {
        if (!dataId) return;
        fetch(`/api/map/data-single?id=${dataId}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('dig-title').textContent = 'Edit Data Spasial';
                document.getElementById('dig-method').value = 'PUT';
                document.getElementById('dig-layer-id').value = layerId;
                document.getElementById('dig-data-id').value = dataId;
                document.getElementById('dig-geometry').value = JSON.stringify(data.geometry);
                document.getElementById('dig-nama').value = data.nama || '';
                document.getElementById('dig-deskripsi').value = data.deskripsi || '';
                document.getElementById('dig-foto').value = '';
                document.getElementById('dig-submit').textContent = 'Update';

                document.getElementById('digitasi-modal').classList.remove('hidden');
                document.getElementById('digitasi-modal').classList.add('flex');
            })
            .catch(() => alert('Gagal memuat data.'));
    },

    deleteFeature(dataId, layerId) {
        if (!dataId || !confirm('Hapus data ini?')) return;
        fetch(`/api/map/digitasi/${dataId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
        })
            .then(r => r.json())
            .then(result => {
                if (result.message) {
                    this.removeLayerFromMap(layerId);
                    const layer = this.activeLayers.find(l => l.id === layerId);
                    if (layer) this.addLayerToMap(layer);
                }
            })
            .catch(() => alert('Gagal menghapus data.'));
    },

    async refreshVisibleLayers() {
        if (this.bufferActive) return;
        const items = document.querySelectorAll('#layer-list [data-layer-id]');
        for (const item of items) {
            const cb = item.querySelector('input[type="checkbox"]');
            if (cb && cb.checked) {
                const id = parseInt(item.dataset.layerId);
                const layer = this.activeLayers.find(l => l.id === id);
                if (layer) await this.addLayerToMap(layer);
            }
        }
    },

    removeLayerFromMap(layerId) {
        if (this.layerGroups[layerId]) {
            this.map.removeLayer(this.layerGroups[layerId]);
            delete this.layerGroups[layerId];
        }
    },

    zoomToLayer(layerId) {
        if (this.layerBoundsCache[layerId]) {
            this.map.fitBounds(this.layerBoundsCache[layerId], { padding: [50, 50], maxZoom: 16 });
        } else if (this.layerGroups[layerId]) {
            const b = this.layerGroups[layerId].getBounds();
            if (b.isValid()) this.map.fitBounds(b, { padding: [50, 50], maxZoom: 16 });
        }
    },

    zoomToAll() {
        const b = Object.values(this.layerGroups).reduce((acc, l) => {
            const lb = l.getBounds();
            if (lb.isValid()) acc.extend(lb);
            return acc;
        }, L.latLngBounds());
        if (b.isValid()) this.map.fitBounds(b, { padding: [50, 50] });
    },

    buildPopup(props, layerId) {
        const rows = Object.entries(props).filter(([k]) => k !== 'id')
            .map(([k, v]) => `<tr><td class="font-medium capitalize text-xs text-gray-500 pr-3">${k}</td><td class="text-sm">${v ?? '-'}</td></tr>`).join('');
        let html = `<div class="min-w-[200px]"><table class="w-full">${rows}</table>`;
        if (this.canEdit) {
            html += `<div class="flex gap-2 mt-2 pt-2 border-t border-gray-200">
                <button data-edit class="flex-1 px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</button>
                <button data-delete class="flex-1 px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            </div>`;
        }
        html += '</div>';
        return html;
    },

    populateDrawSelect(layers) {
        const select = document.getElementById('draw-layer-select');
        if (!select) return;
        select.innerHTML = '<option value="">Pilih layer tujuan...</option>';
        layers.forEach((l) => {
            const opt = document.createElement('option');
            opt.value = l.id; opt.textContent = `${l.nama} (${l.geom_type})`;
            opt.dataset.geomType = l.geom_type;
            select.appendChild(opt);
        });
    },

    initDraw() {
        this.drawnItems = new L.FeatureGroup();
        this.map.addLayer(this.drawnItems);

        ['btn-draw-point', 'btn-draw-line', 'btn-draw-polygon'].forEach((id) => {
            const btn = document.getElementById(id);
            if (btn) btn.addEventListener('click', () => this.startDraw(id.replace('btn-draw-', '')));
        });
        const cancelBtn = document.getElementById('btn-draw-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', () => this.cancelDraw());

        const layerSelect = document.getElementById('draw-layer-select');
        if (layerSelect) layerSelect.addEventListener('change', () => this.onDrawLayerChange());
    },

    onDrawLayerChange() {
        const select = document.getElementById('draw-layer-select');
        const layerId = select?.value;
        ['btn-draw-point', 'btn-draw-line', 'btn-draw-polygon'].forEach((id) => {
            const btn = document.getElementById(id);
            if (btn) btn.disabled = !layerId;
        });
    },

    startDraw(type) {
        if (!this.currentLayerId) {
            const select = document.getElementById('draw-layer-select');
            this.currentLayerId = select?.value;
        }
        if (!this.currentLayerId) return;
        this.cancelDraw();

        const opts = {
            point: { shapeOptions: { color: '#3388ff', weight: 2 } },
            line: { shapeOptions: { color: '#3388ff', weight: 3 } },
            polygon: { shapeOptions: { color: '#3388ff', fillOpacity: 0.3 } },
        };

        const handlers = { point: L.Draw.Marker, line: L.Draw.Polyline, polygon: L.Draw.Polygon };
        const Handler = handlers[type];
        if (!Handler) return;
        const h = new Handler(this.map, opts[type]);
        h.enable();
        this.showDrawUI(true);

        this.map.once(L.Draw.Event.CREATED, (e) => {
            this.drawnItems.addLayer(e.layer);
            this.showDrawUI(false);
            this.openDigitasiModal(e.layer.toGeoJSON().geometry);
        });
    },

    cancelDraw() {
        this.drawnItems.clearLayers();
        this.showDrawUI(false);
        this.currentLayerId = null;
    },

    showDrawUI(active) {
        ['btn-draw-point', 'btn-draw-line', 'btn-draw-polygon'].forEach((id) => {
            const btn = document.getElementById(id);
            if (btn) btn.classList.toggle('hidden', active);
        });
        const cancelBtn = document.getElementById('btn-draw-cancel');
        if (cancelBtn) cancelBtn.classList.toggle('hidden', !active);
    },

    initDigitasiModal() {
        const modal = document.getElementById('digitasi-modal');
        if (!modal) return;

        document.getElementById('dig-batal')?.addEventListener('click', () => this.closeDigitasiModal());
        modal.addEventListener('click', (e) => { if (e.target === modal) this.closeDigitasiModal(); });
        document.getElementById('digitasi-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitDigitasi();
        });
    },

    openDigitasiModal(geometry) {
        document.getElementById('dig-title').textContent = 'Simpan Data Spasial';
        document.getElementById('dig-method').value = 'POST';
        document.getElementById('dig-data-id').value = '';
        document.getElementById('dig-geometry').value = JSON.stringify(geometry);
        document.getElementById('dig-nama').value = '';
        document.getElementById('dig-deskripsi').value = '';
        document.getElementById('dig-foto').value = '';
        document.getElementById('dig-submit').textContent = 'Simpan';

        const select = document.getElementById('draw-layer-select');
        if (select) document.getElementById('dig-layer-id').value = select.value;

        document.getElementById('digitasi-modal').classList.remove('hidden');
        document.getElementById('digitasi-modal').classList.add('flex');
    },

    closeDigitasiModal() {
        document.getElementById('digitasi-modal').classList.add('hidden');
        document.getElementById('digitasi-modal').classList.remove('flex');
        this.drawnItems.clearLayers();
    },

    async submitDigitasi() {
        const form = document.getElementById('digitasi-form');
        const fd = new FormData(form);
        const dataId = document.getElementById('dig-data-id').value;

        if (dataId) {
            fd.set('_method', 'PUT');
        }

        const url = dataId ? `/api/map/digitasi/${dataId}` : '/api/map/digitasi';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: fd,
            });
            const result = await res.json();
            if (res.ok) {
                alert(dataId ? 'Data berhasil diperbarui!' : 'Data berhasil disimpan!');
                this.closeDigitasiModal();
                const layerId = document.getElementById('dig-layer-id').value;
                this.removeLayerFromMap(layerId);
                const layer = this.activeLayers.find(l => l.id == layerId);
                if (layer) this.addLayerToMap(layer);
            } else {
                alert('Error: ' + (result.message || 'Gagal menyimpan data'));
            }
        } catch (e) {
            alert('Gagal terhubung ke server.');
        }
    },
};

document.addEventListener('DOMContentLoaded', () => MapManager.init());
