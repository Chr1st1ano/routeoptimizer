<?php
// save_trip.php
header('Content-Type: application/json');

// 1. Database Connection
$host = 'localhost';
$db   = 'route_optimizer';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// 2. Get Data from the Form
$input = json_decode(file_get_contents('php://input'), true);

// 3. Insert into Database
try {
    $stmt = $pdo->prepare("INSERT INTO trip_history 
        (start_point, end_point, distance_km, duration_minutes, time_of_day, traffic_condition, route_type) 
        VALUES (:start, :end, :dist, :dur, :time, :traffic, :type)");

    $stmt->execute([
        ':start'   => $input['start'],
        ':end'     => $input['end'],
        ':dist'    => $input['distance'],
        ':dur'     => $input['duration'],
        ':time'    => $input['time'],
        ':traffic' => $input['traffic'],
        ':type'    => $input['type']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Trip saved successfully!']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Save failed: ' . $e->getMessage()]);
}
?>