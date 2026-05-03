<?php session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'usher') {
    header('Location: ../login.php');
    exit;
}
$usher_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : (isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'Usher');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Available Projects</title>
    <link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../wasla-theme.js?v=<?= time() ?>"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body>
    <?php $active_page = 'jobs'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content">
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1 class="section-title">Available Projects</h1>
                <div class="view-toggle" style="display:flex; gap:8px; background:var(--gray-100); padding:4px; border-radius:8px;">
                    <button id="btn-grid-view" onclick="toggleView('grid')" style="padding:8px 16px; border:none; background:var(--white); border-radius:6px; font-weight:600; cursor:pointer; box-shadow:var(--shadow-sm); color:var(--primary); transition:0.2s"><i class="fas fa-th-large"></i> Grid</button>
                    <button id="btn-map-view" onclick="toggleView('map')" style="padding:8px 16px; border:none; background:transparent; border-radius:6px; font-weight:600; cursor:pointer; color:var(--gray-500); transition:0.2s"><i class="fas fa-map-marked-alt"></i> Map</button>
                </div>
            </div>
            <section class="filters-row">
                <div class="filter-search"><i class="fas fa-search"></i><input type="text" placeholder="Search projects..." id="job-search"></div>
                <div class="filter-select"><select id="filter-location">
                    <option value="">Location</option>
                    <option>Cairo</option>
                    <option>Alexandria</option>
                    <option>Jeddah</option>
                    <option>Dubai</option>
                    <option>Sharm Elsheikh</option>
                    <option>Riyadh</option>
                    <option>North Coast</option>
                    <option>El Gouna</option>
                </select></div>
                <div class="filter-select"><select id="filter-pay"><option value="">Pay Range</option><option value="200-400">EGP 200-400</option><option value="400-600">EGP 400-600</option><option value="600+">EGP 600+</option></select></div>
            </section>
            
            <div id="wasla-map" style="display:none; height:600px; width:100%; border-radius:16px; border:1px solid var(--gray-200); box-shadow:var(--shadow-md); z-index:1;"></div>

            <div class="projects-grid" id="jobs-grid">
                <p style="color:var(--gray-400);text-align:center;grid-column:1/-1">Loading projects...</p>
            </div>

            <!-- Project Details Modal -->
            <div id="project-modal" class="modal-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
                <div class="modal-content" style="background:var(--white);width:90%;max-width:600px;border-radius:16px;padding:32px;position:relative;box-shadow:var(--shadow-xl);">
                    <button class="modal-close" onclick="closeProjectModal()" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:1.2rem;color:var(--gray-400);cursor:pointer;"><i class="fas fa-times"></i></button>
                    <div style="margin-bottom:24px;">
                        <div id="modal-badge" style="margin-bottom:12px;"></div>
                        <h2 id="modal-title" style="color:var(--primary);font-size:1.5rem;font-weight:800;margin-bottom:8px;">Project Title</h2>
                        <p id="modal-company" style="color:var(--gray-600);font-weight:500;margin-bottom:16px;"><i class="fas fa-building"></i> Company Name</p>
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;background:var(--gray-50);padding:16px;border-radius:12px;border:1px solid var(--gray-200);">
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Location</span><strong id="modal-location" style="color:var(--gray-800);">Location</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Date</span><strong id="modal-date" style="color:var(--gray-800);">Date</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Pay</span><strong id="modal-pay" style="color:var(--accent);">EGP 0/day</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Availability</span><strong id="modal-slots" style="color:var(--gray-800);">0 slots left</strong></div>
                        </div>

                        <h3 style="color:var(--primary);font-size:1.1rem;margin-bottom:8px;">Project Description</h3>
                        <p id="modal-description" style="color:var(--gray-600);line-height:1.6;font-size:0.95rem;margin-bottom:24px;white-space:pre-wrap;">Description goes here.</p>
                        
                        <div id="modal-action-container" style="display:flex;justify-content:flex-end;gap:12px;">
                            <button onclick="closeProjectModal()" style="padding:12px 24px;background:var(--gray-200);color:var(--gray-800);border:none;border-radius:8px;font-weight:600;cursor:pointer;">Close</button>
                            <button id="modal-apply-btn" style="padding:12px 24px;background:var(--accent);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px var(--accent-glow);">Apply Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
    let allJobs = [];
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`../api/usher_jobs.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                allJobs = data.jobs;
                renderJobs(allJobs);
            });
    });

    function renderJobs(jobs) {
        const grid = document.getElementById('jobs-grid');
        if (jobs.length === 0) {
            grid.innerHTML = '<p style="color:var(--gray-400);text-align:center;grid-column:1/-1">No projects found</p>';
            return;
        }
        grid.innerHTML = jobs.map(j => {
            let badgeClass = j.status === 'active' ? 'premium-badge-active' : 'premium-badge-pending';
            let badgeText = j.status === 'active' ? 'Hiring Now' : 'Coming Soon';
            const badge = `<div class="${badgeClass}"><i class="fas ${j.status === 'active' ? 'fa-fire' : 'fa-clock'}"></i> ${badgeText}</div>`;
            const applied = j.already_applied;
            let statusText = '';
            let btnClass = 'premium-btn';
            
            if (applied === 'accepted' || applied === 'completed') {
                statusText = '✓ Accepted';
                btnClass = 'premium-btn premium-btn-disabled';
            } else if (applied === 'pending') {
                statusText = 'Pending...';
                btnClass = 'premium-btn premium-btn-disabled';
            }
            
            let actionBtn = '';
            if (applied) {
                actionBtn = `<button class="${btnClass}" disabled>${statusText}</button>`;
            } else if (j.status !== 'active') {
                actionBtn = `<button class="premium-btn premium-btn-disabled" disabled>Coming Soon</button>`;
            } else {
                actionBtn = `<button class="premium-btn" onclick="openProjectModal(${j.id})">Review & Apply</button>`;
            }

            return `
                <div class="premium-card" data-location="${j.location}" data-pay="${j.pay}">
                    <div class="premium-card-header">
                        ${badge}
                        <h3 class="premium-card-title">${j.title}</h3>
                        <p class="premium-card-company"><i class="fas fa-building"></i> ${j.company}</p>
                    </div>
                    <div class="premium-card-body">
                        <div class="premium-detail"><i class="fas fa-map-marker-alt"></i> <span>${j.location}</span></div>
                        <div class="premium-detail"><i class="fas fa-calendar-alt"></i> <span>${j.date}</span></div>
                        <div class="premium-detail"><i class="fas fa-clock"></i> <span>${j.hours} hours</span></div>
                        <div class="premium-detail premium-pay"><i class="fas fa-money-bill-wave"></i> <span>EGP ${j.pay}/day</span></div>
                    </div>
                    <div class="premium-card-footer">
                        <div class="premium-tags">
                            <span class="premium-tag">${j.category || 'Event'}</span>
                        </div>
                        <div class="premium-slots"><i class="fas fa-user-friends"></i> ${j.slots_left} slots</div>
                    </div>
                    <div class="premium-card-action">
                        ${actionBtn}
                    </div>
                </div>`;
        }).join('');
    }

    function openProjectModal(id) {
        const j = allJobs.find(job => job.id == id);
        if (!j) { console.error('Job not found:', id); return; }
        
        document.getElementById('modal-title').textContent = j.title;
        document.getElementById('modal-company').innerHTML = `<i class="fas fa-building"></i> ${j.company}`;
        document.getElementById('modal-location').textContent = j.location;
        document.getElementById('modal-date').textContent = j.date;
        document.getElementById('modal-pay').textContent = `EGP ${j.pay}/day`;
        document.getElementById('modal-slots').textContent = `${j.slots_left} slots left`;
        document.getElementById('modal-description').textContent = j.description || 'No description provided by the client.';
        
        document.getElementById('modal-badge').innerHTML = j.status === 'active' 
            ? '<span class="status-badge status-active" style="display:inline-block">Hiring</span>' 
            : '<span class="status-badge status-pending" style="display:inline-block">Coming Soon</span>';
            
        const applyBtn = document.getElementById('modal-apply-btn');
        applyBtn.onclick = () => applyJob(applyBtn, j.id);
        applyBtn.textContent = 'Apply Now';
        applyBtn.style.background = 'var(--accent)';
        applyBtn.style.pointerEvents = 'auto';
        
        const modal = document.getElementById('project-modal');
        modal.style.display = 'flex';
    }

    function closeProjectModal() {
        document.getElementById('project-modal').style.display = 'none';
    }

    function applyJob(btn, projectId) {
        btn.textContent = 'Applying...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
        fetch('../api/apply_job.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: projectId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Applied Successfully!';
                btn.style.background = 'var(--accent)';
                // Update local state
                const job = allJobs.find(j => j.id === projectId);
                if (job) job.already_applied = 'pending';
                renderJobs(allJobs);
                setTimeout(() => closeProjectModal(), 1500);
            } else {
                btn.textContent = data.error || 'Error';
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }
        });
    }

    // Filters
    document.getElementById('job-search').addEventListener('input', filterJobs);
    document.getElementById('filter-location').addEventListener('change', filterJobs);
    document.getElementById('filter-pay').addEventListener('change', filterJobs);

    // Map Logic
    let map = null;
    let markersLayer = null;
    let isMapView = false;

    // Hardcoded coordinates for the map
    const cityCoordinates = {
        'Riyadh': [24.7136, 46.6753],
        'Jeddah': [21.4858, 39.1925],
        'Cairo': [30.0444, 31.2357],
        'Alexandria': [31.2001, 29.9187],
        'Dubai': [25.2048, 55.2708],
        'Sharm Elsheikh': [27.9158, 34.3299],
        'North Coast': [30.8252, 28.9538],
        'El Gouna': [27.3942, 33.6782]
    };

    function toggleView(view) {
        isMapView = view === 'map';
        document.getElementById('jobs-grid').style.display = isMapView ? 'none' : 'grid';
        document.getElementById('wasla-map').style.display = isMapView ? 'block' : 'none';
        
        const btnGrid = document.getElementById('btn-grid-view');
        const btnMap = document.getElementById('btn-map-view');
        
        if (isMapView) {
            btnMap.style.background = 'var(--white)';
            btnMap.style.color = 'var(--primary)';
            btnMap.style.boxShadow = 'var(--shadow-sm)';
            btnGrid.style.background = 'transparent';
            btnGrid.style.color = 'var(--gray-500)';
            btnGrid.style.boxShadow = 'none';
            initMap();
        } else {
            btnGrid.style.background = 'var(--white)';
            btnGrid.style.color = 'var(--primary)';
            btnGrid.style.boxShadow = 'var(--shadow-sm)';
            btnMap.style.background = 'transparent';
            btnMap.style.color = 'var(--gray-500)';
            btnMap.style.boxShadow = 'none';
        }
    }

    function initMap() {
        if (!map) {
            map = L.map('wasla-map').setView([24.7136, 46.6753], 5); // Default to Riyadh/MENA
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);
            markersLayer = L.layerGroup().addTo(map);
        }
        updateMapMarkers(allJobs);
    }

    function updateMapMarkers(jobsData) {
        if (!map || !markersLayer) return;
        markersLayer.clearLayers();
        
        const bounds = [];
        jobsData.forEach(j => {
            // Check if city exists in coordinates, fallback to a random nearby offset to prevent overlapping
            let coords = cityCoordinates[j.city];
            if (!coords) {
                // If location contains the city name
                const matchedCity = Object.keys(cityCoordinates).find(c => j.location && j.location.includes(c));
                if (matchedCity) coords = cityCoordinates[matchedCity];
            }
            
            if (coords) {
                // Add tiny random offset to prevent identical markers from stacking perfectly
                const lat = coords[0] + (Math.random() - 0.5) * 0.05;
                const lng = coords[1] + (Math.random() - 0.5) * 0.05;
                bounds.push([lat, lng]);

                const markerIcon = L.divIcon({
                    html: `<div style="background:var(--accent);color:white;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 8px rgba(0,0,0,0.3);border:2px solid white;"><i class="fas fa-briefcase"></i></div>`,
                    className: '',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const marker = L.marker([lat, lng], {icon: markerIcon}).addTo(markersLayer);
                marker.bindPopup(`
                    <div style="text-align:center; padding:5px;">
                        <h4 style="font-family:'Inter', sans-serif; font-weight:700; color:var(--primary); margin:0 0 5px 0;">${j.title}</h4>
                        <p style="margin:0 0 10px 0; font-size:0.85rem; color:var(--gray-600);"><i class="fas fa-money-bill-wave"></i> EGP ${j.pay}/day</p>
                        <button onclick="openProjectModal(${j.id})" style="background:var(--accent); color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:600; width:100%;">View Details</button>
                    </div>
                `);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(L.latLngBounds(bounds), {padding: [50, 50], maxZoom: 12});
        }
    }

    function filterJobs() {
        const q = document.getElementById('job-search').value.toLowerCase();
        const loc = document.getElementById('filter-location').value;
        const pay = document.getElementById('filter-pay').value;
        let filtered = allJobs.filter(j => {
            if (q && !j.title.toLowerCase().includes(q) && !j.company.toLowerCase().includes(q)) return false;
            if (loc && !j.location.includes(loc)) return false;
            if (pay) {
                if (pay === '200-400' && (j.pay < 200 || j.pay > 400)) return false;
                if (pay === '400-600' && (j.pay < 400 || j.pay > 600)) return false;
                if (pay === '600+' && j.pay < 600) return false;
            }
            return true;
        });
        renderJobs(filtered);
        if (isMapView) {
            updateMapMarkers(filtered);
        }
    }
    </script>
</body>
</html>
