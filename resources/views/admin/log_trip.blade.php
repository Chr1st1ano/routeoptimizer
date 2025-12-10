<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log New Trip Data</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Slight override to center the form for the "Admin" view */
    .admin-container {
        width: 500px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .back-link { display: block; margin-bottom: 20px; color: #6b7280; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    h2 { margin-top: 0; color: #111827; }
    .status-msg { margin-top: 15px; font-weight: bold; text-align: center; display: none; }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>

  <div class="admin-container">
    <a href="index.html" class="back-link">← Back to Map</a>
    <h2>Log a Completed Trip</h2>
    <div class="small">Add real-world data to train the prediction model.</div>

    <div class="form-group">
      <label>Start Location</label>
      <input id="start" class="input" placeholder="e.g. Home">
    </div>

    <div class="form-group">
      <label>Destination</label>
      <input id="end" class="input" placeholder="e.g. Office">
    </div>

    <div class="row">
        <div class="form-group" style="flex:1">
            <label>Distance (km)</label>
            <input id="distance" type="number" step="0.1" class="input" placeholder="0.0">
        </div>
        <div class="form-group" style="flex:1">
            <label>Duration (mins)</label>
            <input id="duration" type="number" class="input" placeholder="0">
        </div>
    </div>

    <div class="form-group">
      <label>Time of Trip</label>
      <input id="time" type="time" class="input">
    </div>

    <div class="row">
        <div class="form-group" style="flex:1">
            <label>Traffic Level</label>
            <select id="traffic" class="select">
                <option value="Light">Light</option>
                <option value="Moderate">Moderate</option>
                <option value="Heavy">Heavy</option>
            </select>
        </div>
        <div class="form-group" style="flex:1">
            <label>Route Type</label>
            <select id="type" class="select">
                <option value="highway">Highway</option>
                <option value="shortcut">Shortcut</option>
            </select>
        </div>
    </div>

    <button id="saveBtn" class="btn">Save Trip to Database</button>
    <div id="status" class="status-msg"></div>

  </div>

<script>
    document.getElementById('saveBtn').addEventListener('click', function() {
        const btn = document.getElementById('saveBtn');
        const statusDiv = document.getElementById('status');
        
        // Collect Data
        const data = {
            start: document.getElementById('start').value,
            end: document.getElementById('end').value,
            distance: document.getElementById('distance').value,
            duration: document.getElementById('duration').value,
            time: document.getElementById('time').value,
            traffic: document.getElementById('traffic').value,
            type: document.getElementById('type').value
        };

        // Simple Validation
        if(!data.start || !data.end || !data.distance || !data.duration) {
            alert("Please fill in all fields.");
            return;
        }

        btn.disabled = true;
        btn.innerText = "Saving...";

        // Send to PHP
        fetch('save_trip.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            statusDiv.style.display = 'block';
            if(result.status === 'success') {
                statusDiv.className = 'status-msg success';
                statusDiv.innerText = result.message;
                // Clear form
                document.getElementById('distance').value = "";
                document.getElementById('duration').value = "";
            } else {
                statusDiv.className = 'status-msg error';
                statusDiv.innerText = result.message;
            }
            btn.disabled = false;
            btn.innerText = "Save Trip to Database";
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerText = "Error";
        });
    });
</script>

</body>
</html>