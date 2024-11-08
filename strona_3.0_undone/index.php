<?php 
include('server.php'); 

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Fetch user ID and saved locations from the database
$query = "SELECT id FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
$user_id = $user['id'];

$locations_query = "SELECT * FROM locations WHERE user_id='$user_id'";
$locations_result = mysqli_query($db, $locations_query);
$saved_locations = [];
while ($row = mysqli_fetch_assoc($locations_result)) {
    $saved_locations[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - View and Add Locations</title>
    <link rel="stylesheet" href="style_glowna.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h2>Menu</h2>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        <ul>
            <li><a href="powiadomienia/notifications.html">Powiadomienia</a></li>
            <li><a href="#">Losuj SPOT-a</a></li>
            <li><a href="miasto.php">Miasto</a></li>
            <li><a href="#">Twoje komentarze</a></li>
            <li><a href="#">Filtry</a></li>
            <li><a href="znajomi/znajomi.html">Znajomi</a></li>
            <li><a href="ustawienia/ustawienia.html">Ustawienia</a></li>
            <li><a href="index.php?logout='1'" class="logout">Wyloguj</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="map-container">
        <div id="map"></div>
        <div id="message"></div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Pobranie współrzędnych z URL, jeśli są dostępne
            const urlParams = new URLSearchParams(window.location.search);
            const lat = parseFloat(urlParams.get('lat')) || 53.4285;  // Domyślna szerokość geograficzna
            const lon = parseFloat(urlParams.get('lon')) || 14.5523;  // Domyślna długość geograficzna

            // Inicjalizacja mapy z domyślną lub przekazaną lokalizacją
            var map = L.map("map").setView([lat, lon], 13);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            var markers = {};

            var savedLocations = <?php echo json_encode($saved_locations); ?>;
            savedLocations.forEach(function(location) {
                var latlng = [location.latitude, location.longitude];
                var marker = L.marker(latlng).addTo(map).bindPopup(location.location_name);
                markers[latlng] = marker;
            });

            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                var latlngKey = lat + ',' + lng;

                if (markers[latlngKey]) {
                    map.removeLayer(markers[latlngKey]);
                    delete markers[latlngKey];
                } else {
                    var marker = L.marker([lat, lng]).addTo(map);
                    markers[latlngKey] = marker;
                    var popupContent = document.createElement('div');

                    var inputField = document.createElement('input');
                    inputField.type = 'text';
                    inputField.id = 'spot-name';
                    inputField.placeholder = 'Enter location name';

                    var saveBtn = document.createElement('button');
                    saveBtn.textContent = 'Save';
                    
                    var deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Delete';

                    popupContent.appendChild(inputField);
                    popupContent.appendChild(saveBtn);
                    popupContent.appendChild(deleteBtn);

                    marker.bindPopup(popupContent).openPopup();

                    saveBtn.addEventListener('click', function() {
                        var spotName = inputField.value.trim();
                        if (spotName) {
                            $.ajax({
                                url: 'save_location.php',
                                method: 'POST',
                                data: {
                                    user_id: <?php echo json_encode($user_id); ?>,
                                    location_name: spotName,
                                    latitude: lat,
                                    longitude: lng
                                },
                                success: function(response) {
                                    $('#message').html(response);
                                    marker.bindPopup(spotName);
                                },
                                error: function() {
                                    $('#message').html("<p style='color:red;'>Failed to save location. Try again.</p>");
                                }
                            });
                        } else {
                            alert("Please enter a location name.");
                        }
                    });

                    deleteBtn.addEventListener('click', function() {
                        map.removeLayer(marker);
                        delete markers[latlngKey];
                    });
                }
            });
        });
        </script>
    </div>
</body>
</html>

