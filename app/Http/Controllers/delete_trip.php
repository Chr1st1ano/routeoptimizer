<?php
// delete_trip.php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'route_optimizer';
$user = 'root';
$pass = '';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM trip_history WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>