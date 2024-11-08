<?php
session_start();
require 'config.php'; // Połączenie z bazą danych

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby zobaczyć zaproszenia.";
    exit();
}

$receiver_id = $_SESSION['user_id']; // ID użytkownika, który ma zaproszenia

// Zapytanie do bazy danych, aby pobrać zaproszenia w statusie 'pending'
$sql = "SELECT fr.id, u.username as sender_username 
        FROM friend_requests fr
        JOIN users u ON fr.sender_id = u.id
        WHERE fr.receiver_id = ? AND fr.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

$pending_requests = [];
while ($row = $result->fetch_assoc()) {
    $pending_requests[] = $row;
}

// Wyświetlanie zaproszeń
if (!empty($pending_requests)) {
    foreach ($pending_requests as $request) {
        echo "<div class='friend-request-box'>";
        echo "<p>Zaproszenie od: " . htmlspecialchars($request['sender_username']) . "</p>";
        echo "<form action='accept_friends_request.php' method='POST'>";
        echo "<input type='hidden' name='request_id' value='" . $request['id'] . "'>";
        echo "<button type='submit' name='action' value='accept'>Zaakceptuj</button>";
        echo "<button type='submit' name='action' value='reject'>Odrzuć</button>";
        echo "</form>";
        echo "</div>";
    }
} else {
    echo "<p>Brak zaproszeń.</p>";
}
?>
