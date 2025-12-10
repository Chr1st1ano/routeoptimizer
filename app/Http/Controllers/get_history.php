<?php
// get_history.php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'route_optimizer';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all trips, newest first
    $stmt = $pdo->query("SELECT * FROM trip_history ORDER BY id DESC");
    $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($trips);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>