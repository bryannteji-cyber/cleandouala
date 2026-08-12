// CleanDouala - Main JavaScript (improved map)

document.addEventListener('DOMContentLoaded', function() {
    const mapDiv = document.getElementById('map');
    
    if (mapDiv) {
        // Check if Leaflet loaded
        if (typeof L === 'undefined') {
            mapDiv.innerHTML = '<div style="padding:40px;text-align:center;background:#fee2e2;color:#991b1b;border-radius:12px;">' +
                '<strong>Map library failed to load.</strong><br>' +
                'Please check your internet connection and refresh the page.<br>' +
                '(Leaflet + OpenStreetMap need internet)</div>';
            return;
        }

        try {
            // Center of Douala
            const doualaCenter = [4.0511, 9.7679];
            
            const map = L.map('map', {
                scrollWheelZoom: true,
                zoomControl: true
            }).setView(doualaCenter, 13);
            
            // OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Force map to recalculate size (common fix)
            setTimeout(function() {
                map.invalidateSize();
            }, 200);
            
            // Custom colored markers
            function createIcon(color) {
                return L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background:' + color + ';width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                    popupAnchor: [0, -12]
                });
            }

            const icons = {
                dump: createIcon('#dc2626'),
                overflowing_bin: createIcon('#f59e0b'),
                clogged_drain: createIcon('#2563eb'),
                flood_risk: createIcon('#7c3aed'),
                other: createIcon('#64748b')
            };
            
            // Load reports
            if (typeof reportsData !== 'undefined' && Array.isArray(reportsData) && reportsData.length > 0) {
                const bounds = [];
                
                reportsData.forEach(function(report) {
                    const lat = parseFloat(report.latitude);
                    const lng = parseFloat(report.longitude);
                    
                    if (isNaN(lat) || isNaN(lng)) return; // skip invalid coords
                    
                    const marker = L.marker([lat, lng], {
                        icon: icons[report.type] || icons.other
                    }).addTo(map);
                    
                    const typeLabels = {
                        dump: 'Illegal Dump / Dépôt sauvage',
                        overflowing_bin: 'Overflowing Bin / Poubelle débordante',
                        clogged_drain: 'Clogged Drain / Drain bouché',
                        flood_risk: 'Flood Risk / Risque d\'inondation',
                        other: 'Other / Autre'
                    };
                    
                    let photoHtml = '';
                    if (report.photo) {
                        photoHtml = '<br><img src="' + report.photo + '" alt="Photo" style="max-width:180px;border-radius:6px;margin-top:8px;display:block;">';
                    }
                    
                    const desc = report.description ? report.description : '';
                    
                    marker.bindPopup(
                        '<div style="min-width:160px;">' +
                        '<strong style="font-size:1rem;">' + (typeLabels[report.type] || report.type) + '</strong><br>' +
                        '<span style="color:#444;">' + desc + '</span><br>' +
                        '<small style="color:#666;">Status: ' + report.status + ' • ' + report.created_at + '</small>' +
                        photoHtml +
                        '</div>'
                    );
                    
                    bounds.push([lat, lng]);
                });
                
                // Fit map to show all markers if there are several
                if (bounds.length > 1) {
                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                } else if (bounds.length === 1) {
                    map.setView(bounds[0], 15);
                }
            }
            
            window.cleanDoualaMap = map;
            
        } catch (err) {
            console.error('Map error:', err);
            mapDiv.innerHTML = '<div style="padding:30px;text-align:center;background:#fef3c7;color:#92400e;border-radius:12px;">' +
                'Error loading the map. Please refresh the page.<br><small>' + err.message + '</small></div>';
        }
    }
    
    // Geolocation button
    const locBtn = document.getElementById('getLocationBtn');
    if (locBtn) {
        locBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            locBtn.textContent = 'Getting location...';
            locBtn.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    
                    locBtn.textContent = 'Location captured ✓';
                    locBtn.style.background = '#059669';
                    
                    // Show on map if exists
                    if (window.cleanDoualaMap && typeof L !== 'undefined') {
                        window.cleanDoualaMap.setView([lat, lng], 16);
                        L.marker([lat, lng]).addTo(window.cleanDoualaMap)
                            .bindPopup('Your current location').openPopup();
                    }
                },
                function(error) {
                    let msg = 'Unable to get your location. ';
                    if (error.code === 1) msg += 'Please allow location access in your browser.';
                    else if (error.code === 2) msg += 'Position unavailable.';
                    else msg += 'Please enter coordinates manually.';
                    
                    alert(msg);
                    locBtn.textContent = '📍 Use My Location';
                    locBtn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    }
});
// ========== DARK / LIGHT MODE ==========
(function() {
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

    // Load saved theme
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        if (toggleBtn) toggleBtn.textContent = '☀️';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                toggleBtn.textContent = '☀️';
            } else {
                localStorage.setItem('theme', 'light');
                toggleBtn.textContent = '🌙';
            }
        });
    }
})();