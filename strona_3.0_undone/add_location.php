<?php 
include('server.php'); 

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit;
}

// Fetch the user's ID
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
$user_id = $user['id'];

if (isset($_POST['add_location'])) {
    $location_name = mysqli_real_escape_string($db, $_POST['location_name']);
    $latitude = mysqli_real_escape_string($db, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($db, $_POST['longitude']);

    // Validate form input
    if (empty($location_name) || empty($latitude) || empty($longitude)) {
        array_push($errors, "All fields are required");
    }

    // Add location to the database if no errors
    if (count($errors) == 0) {
        $query = "INSERT INTO locations (user_id, location_name, latitude, longitude) 
                  VALUES('$user_id', '$location_name', '$latitude', '$longitude')";
        mysqli_query($db, $query);
        $_SESSION['success'] = "Location added successfully";
        header('location: index.php'); // Redirect to main page
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Location</title>
</head>
<body>
    <form method="post" action="add_location.php">
        <?php include('errors.php'); ?>
        <div class="input-group">
            <label>Location Name</label>
            <input type="text" name="location_name" required>
        </div>
        <div class="input-group">
            <label>Latitude</label>
            <input type="text" name="latitude" required>
        </div>
        <div class="input-group">
            <label>Longitude</label>
            <input type="text" name="longitude" required>
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="add_location">Add Location</button>
        </div>
    </form>
</body>
</html>
