<?php
session_start();
header("Content-Type: application/json");

// Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Błąd połączenia z bazą danych: " . $conn->connect_error]);
    exit();
}

// Sprawdzenie, czy użytkownik jest zalogowany
if (isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];  // Zalogowany użytkownik
} else {
    $sender_id = NULL;  // Brak zalogowanego użytkownika
}

// Pobranie ID odbiorcy
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;

// Sprawdzenie poprawności ID odbiorcy
if ($receiver_id <= 0) {
    echo json_encode(["error" => "Nieprawidłowy odbiorca"]);
    exit();
}

// Wstawienie zaproszenia do tabeli friend_requests
$sql = "INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Błąd przygotowania zapytania SQL: " . $conn->error]);
    exit();
}

$stmt->bind_param("ii", $sender_id, $receiver_id);  // Przekazujemy sender_id (może być NULL) i receiver_id

if ($stmt->execute()) {
    // Jeśli użytkownik jest zalogowany, dodaj powiadomienie do odbiorcy
    if ($sender_id !== NULL) {
        $message = "Masz nowe zaproszenie do znajomych od użytkownika (ID: $sender_id)";
        $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        
        if (!$notif_stmt) {
            echo json_encode(["error" => "Błąd przygotowania zapytania do powiadomienia: " . $conn->error]);
            exit();
        }
        
        $notif_stmt->bind_param("is", $receiver_id, $message);
        
        if (!$notif_stmt->execute()) {
            echo json_encode(["error" => "Błąd wysyłania powiadomienia: " . $conn->error]);
            exit();
        }
        
        $notif_stmt->close();
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Błąd wysyłania zaproszenia: " . $conn->error]);
}

// Zamknięcie połączenia
$stmt->close();
$conn->close();
?>
