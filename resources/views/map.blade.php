<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Predictive Route Optimization</title>
  
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
  
  <style>
    /* --- CRITICAL LAYOUT STYLES (EMBEDDED) --- */
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
    
    body, html { height: 100%; width: 100%; overflow: hidden; background-color: #f3f4f6; }
    
    /* Flex Container */
    .app { display: flex; height: 100vh; width: 100vw; }
    
    /* Sidebar */
    .panel { 
        width: 350px; 
        background: white; 
        border-right: 1px solid #e5e7eb; 
        display: flex; 
        flex-direction: column; 
        padding: 20px; 
        z-index: 20; 
        box-shadow: 4px 0 15px rgba(0,0,0,0.05); 
        overflow-y: auto;
        flex-shrink: 0; 
    }
    
    /* Map Container */
    .map-area { flex-grow: 1; position: relative; height: 100%; }
    #map-view { height: 100%; width: 100%; background: #e5e7eb; }

    /* UI Elements */
    h1 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
    .small { font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
    .input, .select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
    .row { display: flex; gap: 10px; }
    .btn { width: 100%; background-color: #2563eb; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; margin-top:10px; }
    .btn:hover { background-color: #1d4ed8; }
    .clear-btn { background: white; color: #4b5563; border: 1px solid #d1d5db; }

    /* Search Suggestions */
    .suggestions-dropdown {
        position: absolute; background: white; width: 100%; max-height: 200px;
        overflow-y: auto; border: 1px solid #d1d5db; border-top: none; z-index: 1000;
        display: none;
    }
    .suggestion-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #f3f4f6; }
    .suggestion-item:hover { background-color: #eff6ff; }
    .input-wrapper { position: relative; }

    /* Route Cards & Overlay */
    .route-card { background: #f9fafb; padding: 12px; margin-bottom: 10px; border-left: 4px solid #cbd5e1; border-radius: 4px; }
    .meta { display: flex; justify-content: space-between; }
    .ttime { font-weight: bold; font-size: 1.1rem; }

    .overlay-card { 
        position: absolute; top: 20px; right: 20px; 
        background: white; padding: 15px; border-radius: 12px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        display: flex; gap: 20px; z-index: 1000; 
    }
    .big { font-size: 1.5rem; font-weight: 800; color: #2563eb; }
    .tiny { font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; }
  </style>
</head>
<body>

  <div class="app">
    <aside class="panel">
      <h1>Route Optimizer</h1>
      <div class="small">Plan your trip with AI predictions.</div>

      <div class="form-group">
        <label>From</label>
        <div class="input-wrapper">
            <input id="from" class="input" placeholder="Search start..." autocomplete="off">
            <div id="from-suggestions" class="suggestions-dropdown"></div>
        </div>
      </div>

      <div class="form-group">
        <label>To</label>
        <div class="input-wrapper">
            <input id="to" class="input" placeholder="Search destination..." autocomplete="off">
            <div id="to-suggestions" class="suggestions-dropdown"></div>
        </div>
      </div>

    <div class="form-group">
      <label>Settings</label>
      <div class="row">
        <select id="vehicle" class="select">
            <option value="car">Car</option>
            <option value="motor">Motorcycle</option>
            <option value="jeep">Jeepney</option>
            <option value="tricycle">Tricycle</option>
            <option value="walking">Walking</option>
        </select>

        <select id="ai_mode" class="select" style="font-weight: bold; color: #2563eb;">
            <option value="optimized">✨ Optimized (AI)</option>
            <option value="raw">⛔ Not Optimized</option>
        </select>
      </div>
      
      <div class="row" style="margin-top: 10px;">
        <select id="traffic_manual" class="select">
            <option value="auto">Traffic: Auto (AI)</option>
            <option value="light">Force: Light</option>
            <option value="moderate">Force: Moderate</option>
            <option value="heavy">Force: Heavy</option>
        </select>

        <input id="speed" type="number" class="input" placeholder="Speed" value="40">
      </div>
    </div>

      <button id="calc" class="btn">Run Prediction</button>
      <button id="clear" class="btn clear-btn">Reset Map</button>
      
      <a href="{{ route('trips.index') }}" class="btn clear-btn" style="text-align:center; text-decoration:none; display:block; margin-top:5px;">View History</a>

      <div class="routes-list">
        <div class="route-card">
          <div class="meta"><div class="ttime">-- mins</div></div>
          <div class="small" style="margin:0">Ready to route</div>
        </div>
      </div>

      <div id="save-area" style="display: none; margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
        <div class="small" style="margin-bottom: 10px;">Save this trip?</div>
        <div class="row">
            <button id="confirm-save" class="btn" style="background-color: #10b981;">Yes</button>
            <button id="cancel-save" class="btn clear-btn">No</button>
        </div>
        <div id="save-msg" style="color: green; margin-top: 10px; display:none;">Saved!</div>
      </div>
    </aside>

    <div class="map-area">
      <div id="map-view"></div> <div class="overlay-card">
        <div class="pred">
          <div class="big" id="disp-time">--</div>
          <div class="tiny">Minutes</div>
        </div>
        <div class="pred">
          <div class="big" id="disp-dist">--</div>
          <div class="tiny">Km</div>
        </div>
      </div>
    </div>
  </div>

<script>
  // --- STATE ---
  let clickState = 0; 
  let startCoords = null; 
  let endCoords = null;
  let currentPredictionData = null; 

  // --- MAP INIT (Default: Victoria, Laguna) ---
  const map = L.map('map-view').setView([14.225, 121.325], 13); 
  
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  // Ask Browser for Real GPS Location
  map.locate({setView: true, maxZoom: 16});

  // Success: User allowed GPS
  map.on('locationfound', function(e) {
      L.circle(e.latlng, e.accuracy / 2).addTo(map);
      L.marker(e.latlng).addTo(map).bindPopup("You are here").openPopup();
  });

  // Error: User denied GPS
  map.on('locationerror', function(e) {
      console.log("GPS denied, staying on default view.");
  });

  // --- MARKER SETUP ---
  const startMarker = L.marker([0,0]).addTo(map).bindPopup('Start');
  const endMarker = L.marker([0,0]).addTo(map).bindPopup('Destination');
  const routeLine = L.polyline([], {color: '#2563eb', weight: 5}).addTo(map);
  
  map.removeLayer(startMarker);
  map.removeLayer(endMarker);

  // --- HELPER: GET REAL ADDRESS FROM COORDINATES ---
  async function getAddress(lat, lng) {
      try {
          const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
          const res = await fetch(url);
          const data = await res.json();
          if(data.display_name) {
              return data.display_name.split(',').slice(0, 3).join(',');
          }
          return "Unknown Location";
      } catch (e) {
          console.error("Geocoding failed", e);
          return `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
      }
  }

  // --- MAP CLICK (UPDATED) ---
  map.on('click', async function(e) {
      const lat = e.latlng.lat;
      const lng = e.latlng.lng;

      if (clickState === 0) {
          // Start Point
          startCoords = [lat, lng];
          startMarker.setLatLng(startCoords).addTo(map);
          document.getElementById('from').value = "Fetching address..."; 
          const address = await getAddress(lat, lng);
          document.getElementById('from').value = address; 
          clickState = 1;

      } else {
          // Destination
          endCoords = [lat, lng];
          endMarker.setLatLng(endCoords).addTo(map);
          document.getElementById('to').value = "Fetching address...";
          const address = await getAddress(lat, lng);
          document.getElementById('to').value = address; 
          clickState = 0;
          drawRoute();
      }
  });

  // --- AUTO-UPDATE SPEED ---
  document.getElementById('vehicle').addEventListener('change', function() {
      const type = this.value;
      const speedInput = document.getElementById('speed');
      const speeds = { 'car': 40, 'motor': 50, 'jeep': 30, 'tricycle': 25, 'walking': 5 };
      if(speeds[type]) speedInput.value = speeds[type];
  });

  // --- SEARCH SUGGESTIONS ---
  function debounce(func, wait) {
      let timeout;
      return function(...args) {
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(this, args), wait);
      };
  }

  async function fetchSuggestions(query, listId, isFrom) {
      if(query.length < 3) { document.getElementById(listId).style.display = 'none'; return; }
      try {
          const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
          const data = await res.json();
          const list = document.getElementById(listId);
          list.innerHTML = '';
          list.style.display = data.length ? 'block' : 'none';
          
          data.forEach(item => {
              const div = document.createElement('div');
              div.className = 'suggestion-item';
              div.innerText = item.display_name.split(',').slice(0,3).join(',');
              div.onclick = () => {
                  document.getElementById(isFrom ? 'from' : 'to').value = item.display_name;
                  list.style.display = 'none';
                  const lat = parseFloat(item.lat);
                  const lon = parseFloat(item.lon);
                  
                  if(isFrom) {
                      startCoords = [lat, lon];
                      startMarker.setLatLng(startCoords).addTo(map);
                      map.setView(startCoords, 14);
                  } else {
                      endCoords = [lat, lon];
                      endMarker.setLatLng(endCoords).addTo(map);
                      map.setView(endCoords, 14);
                  }
                  if(startCoords && endCoords) drawRoute();
              };
              list.appendChild(div);
          });
      } catch(e) { console.error(e); }
  }

  document.getElementById('from').addEventListener('input', debounce((e) => fetchSuggestions(e.target.value, 'from-suggestions', true), 500));
  document.getElementById('to').addEventListener('input', debounce((e) => fetchSuggestions(e.target.value, 'to-suggestions', false), 500));

  // --- ROUTING LOGIC ---
  async function drawRoute() {
      if(!startCoords || !endCoords) return null;
      const url = `https://router.project-osrm.org/route/v1/driving/${startCoords[1]},${startCoords[0]};${endCoords[1]},${endCoords[0]}?overview=full&geometries=geojson&steps=true`;
      
      try {
          const res = await fetch(url);
          const data = await res.json();
          if(data.routes && data.routes.length > 0) {
              const route = data.routes[0];
              const latlngs = route.geometry.coordinates.map(c => [c[1], c[0]]);
              routeLine.setLatLngs(latlngs);
              map.fitBounds(routeLine.getBounds(), {padding: [50,50]});

              let mainRoad = "Unnamed Road";
              let maxDist = 0;
              if (route.legs && route.legs[0].steps) {
                  route.legs[0].steps.forEach(step => {
                      if (step.distance > maxDist && step.name && step.name.trim() !== "") {
                          maxDist = step.distance;
                          mainRoad = step.name;
                      }
                  });
              }
              return { distance: (route.distance / 1000).toFixed(2), road: mainRoad };
          }
      } catch(e) { console.error(e); }
      return null;
  }

  // --- PREDICTION (Laravel API) ---
  document.getElementById('calc').addEventListener('click', async () => {
      const btn = document.getElementById('calc');
      if(!startCoords || !endCoords) { alert("Please set start and end points."); return; }
      
      btn.innerText = "Calculating...";
      const routeData = await drawRoute(); 
      if (!routeData) { alert("Could not calculate route."); btn.innerText = "Run Prediction"; return; }

      fetch("{{ route('predict') }}", {
          method: 'POST',
          headers: { 
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
              from: document.getElementById('from').value,
              to: document.getElementById('to').value,
              real_distance: routeData.distance, 
              user_speed: document.getElementById('speed').value,
              vehicle_type: document.getElementById('vehicle').value,
              detected_road: routeData.road,
              manual_traffic: document.getElementById('traffic_manual').value,
              ai_mode: document.getElementById('ai_mode').value // SEND MODE
          })
      })
      .then(res => res.json())
      .then(data => {
          btn.innerText = "Run Prediction";
          const p = data.routes.primary;
          document.getElementById('disp-time').innerText = p.duration;
          document.getElementById('disp-dist').innerText = p.distance;
          
          document.querySelector('.routes-list').innerHTML = `
              <div class="route-card" style="border-left-color: #2563eb">
                  <div class="meta"><div class="ttime">${p.duration} min</div><div>${p.distance} km</div></div>
                  <div class="small">Via ${p.via} (${p.traffic_level} Traffic)</div>
                  <div class="tiny" style="margin-top:5px; color:#6b7280;">Predicted for: ${data.routes.primary.type}</div>
              </div>
          `;

          // Store all data needed for saving
          currentPredictionData = {
              start: data.locations.origin,
              end: data.locations.destination,
              dist: p.distance,
              dur: p.duration,
              notes: p.traffic_level + " Traffic via " + p.via,
              traffic_level: p.traffic_level,
              route_type: data.routes.primary.type // Save whether it was Optimized/Raw
          };
          document.getElementById('save-area').style.display = 'block';
      })
      .catch(err => {
          console.error(err);
          alert("Error connecting to prediction server.");
          btn.innerText = "Error";
      });
  });

  // --- SAVE TRIP ---
  document.getElementById('confirm-save').addEventListener('click', () => {
      if(!currentPredictionData) return;
      const btn = document.getElementById('confirm-save');
      btn.innerText = "...";

      fetch("{{ route('trips.store') }}", {
          method: 'POST',
          headers: { 
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
              start_point: currentPredictionData.start,
              destination: currentPredictionData.end,
              distance_km: currentPredictionData.dist,
              duration_minutes: currentPredictionData.dur,
              // Use dynamic route type instead of hardcoded
              route_type: currentPredictionData.route_type || "Optimized", 
              start_date: new Date().toISOString().slice(0,10),
              end_date: new Date().toISOString().slice(0,10),
              notes: currentPredictionData.notes,
              traffic_condition: currentPredictionData.traffic_level, 
              vehicle_type: document.getElementById('vehicle').value
          })
      })
      .then(res => {
          if(res.ok) {
              document.getElementById('save-msg').style.display = 'block';
              btn.innerText = "Saved";
              setTimeout(() => document.getElementById('save-area').style.display = 'none', 1500);
          } else {
            res.json().then(d => alert("Save failed: " + JSON.stringify(d)));
          }
      });
  });
  
  document.getElementById('clear').addEventListener('click', () => location.reload());

</script>
</body>
</html>