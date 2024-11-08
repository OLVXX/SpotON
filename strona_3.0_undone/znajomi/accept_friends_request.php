<?php
session_start();
require 'config.php'; // Połączenie z bazą danych

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby zaakceptować lub odrzucić zaproszenie.";
    exit();
}

$request_id = $_POST['request_id']; // ID zaproszenia
$action = $_POST['action']; // Akcja (zaakceptować lub odrzucić)
$user_id = $_SESSION['user_id']; // ID użytkownika

// Sprawdzamy, czy zaproszenie należy do użytkownika
$sql = "SELECT * FROM friend_requests WHERE id = ? AND receiver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Jeśli zaproszenie istnieje, aktualizujemy status
    if ($action == 'accept') {
        // Zmiana statusu na 'accepted'
        $update_sql = "UPDATE friend_requests SET status = 'accepted' WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("i", $request_id);
        if ($stmt_update->execute()) {
            // Dodatkowo dodajemy użytkowników do tabeli znajomych
            $update_friends_sql = "INSERT INTO friends (user1_id, user2_id) VALUES (?, ?)";
            $stmt_friends = $conn->prepare($update_friends_sql);
            $stmt_friends->bind_param("ii", $user_id, $request_id);
            $stmt_friends->execute();
            echo "Zaproszenie zaakceptowane i dodano do znajomych!";
        } else {
            echo "Wystąpił błąd przy akceptowaniu zaproszenia.";
        }
    } else {
        // Zmiana statusu na 'rejected'
        $update_sql = "UPDATE friend_requests SET status = 'rejected' WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("i", $request_id);
        if ($stmt_update->execute()) {
            echo "Zaproszenie zostało odrzucone.";
        } else {
            echo "Wystąpił błąd przy odrzucaniu zaproszenia.";
        }
    }
} else {
    echo "Nie masz dostępu do tego zaproszenia.";
}
?>
