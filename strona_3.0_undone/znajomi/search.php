<?php
header("Content-Type: application/json");

// Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// Pobranie frazy wyszukiwania z żądania
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Przygotowanie zapytania SQL
$sql = "SELECT id, username AS name FROM users WHERE username LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("s", $searchTerm);

// Wykonanie zapytania
$stmt->execute();
$result = $stmt->get_result();

// Przygotowanie wyników do formatu JSON
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = ["id" => $row['id'], "name" => $row['name']];
}

// Zwrócenie wyników w formacie JSON
echo json_encode($users);

// Zamknięcie połączenia
$stmt->close();
$conn->close();
?>
