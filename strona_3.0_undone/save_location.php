<?php 
include('server.php');

// Ensure a user is logged in and data is coming from AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);
    $location_name = mysqli_real_escape_string($db, $_POST['location_name']);
    $latitude = mysqli_real_escape_string($db, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($db, $_POST['longitude']);

    // Insert location into the database
    $query = "INSERT INTO locations (user_id, location_name, latitude, longitude) 
              VALUES('$user_id', '$location_name', '$latitude', '$longitude')";

    if (mysqli_query($db, $query)) {
        echo "<p style='color:green;'>Location '$location_name' saved successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error saving location: " . mysqli_error($db) . "</p>";
    }
} else {
    echo "<p style='color:red;'>Invalid request. Please try again.</p>";
}
?>
