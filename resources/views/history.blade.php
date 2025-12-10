<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Route History Viewer</title>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    body { background-color: #f3f4f6; padding: 20px; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
    th { background-color: #f9fafb; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 700; }
    tr:hover { background-color: #f8fafc; }
    
    .badge { padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
    .badge-heavy { background: #fee2e2; color: #dc2626; }   /* Red */
    .badge-mod   { background: #fef3c7; color: #d97706; }   /* Orange */
    .badge-light { background: #d1fae5; color: #059669; }   /* Green */
    
    .badge-vehicle { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; } 
    .badge-opt     { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; } 

    .del-btn { background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition: 0.2s; }
    .del-btn:hover { background: #dc2626; }
    .btn-back { text-decoration: none; background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 600; }
    
    .chart-area { margin-bottom: 40px; height: 300px; }
  </style>
</head>
<body>

  <div class="container">
    <div class="header">
      <div>
        <h2 style="font-weight: 800; color: #111827;">Trip History Database</h2>
        <div style="color: #6b7280;">View and manage saved route predictions.</div>
      </div>
      <a href="{{ url('/map') }}" class="btn-back">&larr; Back to Map</a>
    </div>

    <div class="chart-area">
        <canvas id="trafficChart"></canvas>
    </div>

    <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Vehicle</th> 
              <th>Route Type</th> 
              <th>Start Point</th>
              <th>Destination</th>
              <th>Stats</th>
              <th>Traffic</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($trips as $trip)
                <tr>
                    <td style="color: #9ca3af;">#{{ $trip->id }}</td>
                    
                    <td>
                        <span class="badge badge-vehicle">
                            {{ ucfirst($trip->vehicle_type ?? 'Car') }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-opt">
                            {{ ucfirst($trip->route_type ?? 'Optimized') }}
                        </span>
                    </td>

                    <td style="font-weight: 600;">{{ Str::limit($trip->start_point ?? 'N/A', 20) }}</td>
                    <td>{{ Str::limit($trip->destination, 20) }}</td>
                    
                    <td>
                        <div style="font-weight: bold;">{{ $trip->duration_minutes }} min</div>
                        <div style="font-size: 0.8rem; color: #6b7280;">{{ $trip->distance_km }} km</div>
                    </td>

                    <td>
                        @php
                            $badgeClass = 'badge-light';
                            $badgeText = 'Light'; // Default text matches default color

                            // Check explicit column first, then notes
                            $condition = strtolower($trip->traffic_condition ?? $trip->notes ?? '');
                            
                            if(str_contains($condition, 'heavy')) {
                                $badgeClass = 'badge-heavy';
                                $badgeText = 'Heavy';
                            } elseif(str_contains($condition, 'mod')) {
                                $badgeClass = 'badge-mod';
                                $badgeText = 'Moderate';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $badgeText }}
                        </span>
                    </td>
                    
                    <td>
                        <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="del-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; padding: 40px; color: #6b7280;">No history found. Go save some trips!</td></tr>
            @endforelse
          </tbody>
        </table>
    </div>
  </div>

<script>
  const tripsData = @json($trips);
    
  let heavy = 0, mod = 0, light = 0;
  
  tripsData.forEach(t => {
      const cond = (t.traffic_condition || t.notes || '').toLowerCase();
      
      if(cond.includes('heavy')) heavy++;
      else if(cond.includes('mod')) mod++;
      else light++;
  });

  const ctx = document.getElementById('trafficChart').getContext('2d');
  new Chart(ctx, {
      type: 'bar',
      data: {
          labels: ['Light Traffic', 'Moderate Traffic', 'Heavy Traffic'],
          datasets: [{
              label: 'Trips per Traffic Condition',
              data: [light, mod, heavy],
              backgroundColor: ['#34d399', '#fbbf24', '#f87171'],
              borderRadius: 4
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
  });
</script>

</body>
</html>