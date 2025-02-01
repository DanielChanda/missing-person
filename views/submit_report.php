<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/DatabaseConfiguration.php';
require_once '../models/Report.php';
require_once '../models/Security.php';
require_once '../models/user.php';
require_once 'uniqueIdentifierGenerator.php';
require_once '../constants.php';
require_once '../models/Audit_log.php';
require_once 'navbar.php';

$type = '';
$name = '';
$age = '';
$age = '';
$gender = '';
$description = '';
$contact_info = '';
$last_seen = '';
$location = '';
$latitude = '';
$longitude = '';

//get user role to use to determine the content available for them
$role = $_SESSION['role'];

// Create database and user object
$database = new DatabaseConfiguration();
$db = $database->getConnection();
$user = new User($db);

//creating the audit object
$auditLog = new AuditLog($db);
// Get the user's profile picture
$profilePic = $user->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));
$email = $user->getUserEmailByUserId(security::sanitizeInput($_SESSION['user_id']));

if (isset($_POST['submit']) && Security::validateCsrfToken($_POST['csrf_token'])) {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $last_seen = $_POST['last_seen'];
    $description = $_POST['description'];
    $contact_info = $_POST['contact_info'];
    $location = $_POST['location'];
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];

    $image_path = null;

    // Handle image upload if $_FILES['image'] is set in the request
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        //require the unique ID generator file
        require_once 'uniqueIdentifierGenerator.php';
        //requre the upload files file
        require_once '../utils/UploadFiles.php';

        //create an instance of upload files
        $uploading = new UploadFiles();

        //provide the allowed extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        //the directory to upload a file to
        $upload_dir = '../uploads/reports/';
        //the unique name provided by unique ID generator ,user email and the current microtime
        $unique_name = generateUUID() . '_' . $email . '_' . microtime(true);
        //the maximum allowed file size
        $maxFileSize = 5000000;
        //where to redirect to when there is an error while submitting the file
        $redirectTo = "submit_report.php";
        //upload the file and return it's path
        $image_path = $uploading->upload($upload_dir, $allowed_extensions, $maxFileSize, $unique_name, $redirectTo);
    }
    

    //validate all the inputs
    if (
        Security::validateSelect($type, ['missing', 'found']) &&
        Security::validateString($name) &&
        Security::validateInt($age) &&
        Security::validateSelect($gender, ['male', 'female', 'other']) &&
        Security::validateDate($last_seen) &&
        Security::validateDescription($description) &&
        Security::validateString($location) &&
        Security::validateString($contact_info)
    ) {
        //create database and report objects
        $database = new DatabaseConfiguration();
        $db = $database->getConnection();
        $report = new Report($db);

        //sanitize the input
        $report->type = Security::sanitize($type);
        $report->name = Security::sanitize($name);
        $report->age = Security::sanitize($age);
        $report->gender = Security::sanitize($gender);
        $report->last_seen = Security::sanitize($last_seen);
        $report->description = Security::sanitize($description);
        $report->contact_info = Security::sanitize($contact_info);
        $report->location = Security::sanitize($location);
        $report->latitude = Security::sanitize($latitude);
        $report->longitude = Security::sanitize($longitude);
        $report->user_id = $_SESSION['user_id'];
        
        $report->image_path = $image_path ? Security::sanitize($image_path) : null;

        //create the report
        if ($report->create()) {
            //report successfully submitted
            // Log the action
            $auditLog->logAction($_SESSION['user_id'], "Submit Report", json_encode(['report_id' => $report->id, 'status' => $report->status]));
            ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'Report submition',
                        text: 'Report submitted successfully. Wait for approval',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = "submit_report.php";
                    });
                });
            </script>
            <?php 
        } else {
            
            ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'submition eeror',
                        text: 'Failed to submit report.',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = "submit_report.php";
                    });
                });
            </script>
            <?php 
        }
    } else {
        
        ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'validation error',
                        text: 'Invalid input.',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = "submit_report.php";
                    });
                });
            </script>
        <?php 
    }
} else {
    echo "Invalid CSRF token.";
}
?>
<style>
        .userbody {
                
            background: url('../images/missingimg2.jpg') no-repeat center center fixed;
            background-size: cover;
        }
</style>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Submit Report</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/css/master.css" rel="stylesheet">
        
        <!-- Custom styles for the map container -->
        <style>
            #map {
                height: 400px; /* Set the height of the map container */
                width: 100%;   /* Set the width of the map container */
            }
        </style>
    </head>
    <body class="userbody">

        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand ms-1" href="#"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <!-- implementing an offcanvas nav bar for small screen-->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <a href = "#" class="offcanvas-title" id="offcanvasNavbarLabel"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body bg-dark">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <?php echo getNavBar($role,'','', '', '', '', 'active" aria-current="page"'); ?>
                </ul>
            </div>
        </nav>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Submit Report</h3>
                        </div>
                        <div class="card-body">
                            <form action="submit_report.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                                <div class="form-group">
                                    <label for="type">Type of Report</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="missing" <?php if($type == 'missing'){ echo 'selected';}?> >Missing Person</option>
                                        <option value="found" <?php if($type == 'found'){ echo 'selected';}?>>Found Person</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required value=<?php echo $name;?> >
                                </div>
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" name="age" id="age" class="form-control" required value=<?php echo $age;?> >
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php if($gender == 'male'){ echo 'selected';}?> >Male</option>
                                        <option value="female" <?php if($gender == 'female'){ echo 'selected';}?> >Female</option>
                                        <option value="other" <?php if($gender == 'other'){ echo 'selected';}?> >Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="last_seen">Last Seen</label>
                                    <input type="date" name="last_seen" id="last_seen" class="form-control" required value=<?php echo $last_seen;?> >
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" name="location" id="location" class="form-control" placeholder="Location" required value=<?php echo $last_seen;?> >
                                </div>
                                <div class="form-group">
                                    <label">Last Seen Location</label> <!-- Label for the map -->
                                    <div id="map"></div> <!-- The map container where the Google Map will be displayed -->
                                    <!-- Hidden input fields to store the selected latitude and longitude -->
                                    <input type="hidden" id="lat" name="latitude"> <!-- Latitude field -->
                                    <input type="hidden" id="lng" name="longitude"> <!-- Longitude field -->
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" required value=<?php echo $description;?> ></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="contact_info">Contact Information</label>
                                    <input type="text" name="contact_info" id="contact_info" class="form-control" required value=<?php echo $contact_info;?> >
                                </div>
                                <div class="form-group">
                                    <label for="image">Upload Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary btn-block">Submit Report</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap JS, Popper.js, and jQuery -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Include the Google Maps JavaScript API using your API key -->
        <script>
            (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",
                q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,
                e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));
                e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));
                d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
                key: "AIzaSyAV3XDFGZcX1LeCtg-RWuGGw7T8-ey4_SM",
                v: "weekly",
                // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
                // Add other bootstrap parameters as needed, using camel case.
            });
        </script>
        <script>
            // Function to initialize the Google Map
            async function initMap() {
                // Default coordinates (zambia) to initialize the map
                var defaultLat = -13.1339;
                var defaultLng = 27.8493;
                
                var infoWindow;

                // Check if coordinates are valid numbers
                function isValidCoordinate(value) {
                    return !isNaN(value) && value >= -90 && value <= 90;
                }

                //handling location error
                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                    infoWindow.setPosition(pos);
                    infoWindow.setContent(
                        browserHasGeolocation
                        ? "Error: The Geolocation service failed."
                        : "Error: Your browser doesn't support geolocation.",
                    );
                    infoWindow.open(map);
                }

                const { Map } = await google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                // Create a new map centered at default coordinates
                var map = new Map(document.getElementById('map'), {
                    center: {lat: defaultLat, lng: defaultLng},
                    zoom: 8,
                    mapId: "DEMO_MAP_ID",
                });

                //create an information window
                infoWindow = new google.maps.InfoWindow();
                //create the button that takes the user to the current location
                const locationButton = document.createElement("button");

                locationButton.textContent = "Pan to Current Location";
                locationButton.classList.add("custom-map-control-button");

                //append it to the map
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
                //add and handle a click event of the current location button
                locationButton.addEventListener("click", (event) => {
                    
                    // Try HTML5 geolocation.
                    if (navigator.geolocation) {
                        
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        
                        // Validate and set the latitude and longitude in hidden input fields
                        if (isValidCoordinate(pos.lat) && isValidCoordinate(pos.lng)) {
                            document.getElementById('lat').value = pos.lat;
                            document.getElementById('lng').value = pos.lng;
                            
                            
                        }

                        //set the position of the information window to the current location
                        infoWindow.setPosition(pos);
                        //add some text
                        infoWindow.setContent("your current location.");
                        infoWindow.open(map);
                        map.setCenter(pos);
                        },
                        () => {
                        handleLocationError(true, infoWindow, map.getCenter());
                        },
                    );
                    } else {
                        // Browser doesn't support Geolocation
                        handleLocationError(false, infoWindow, map.getCenter());
                    }
                    event.preventDefault();
                });

                // Create a draggable marker at default coordinates
                var marker = new AdvancedMarkerElement({
                    position: {lat: defaultLat, lng: defaultLng},
                    map: map,
                    draggable: true
                });

                // Add an event listener to capture the marker's position
                google.maps.event.addListener(marker, 'position_changed', function() {
                    var lat = marker.getPosition().lat();
                    var lng = marker.getPosition().lng();

                    // Validate and set the latitude and longitude in hidden input fields
                    if (isValidCoordinate(lat) && isValidCoordinate(lng)) {
                        document.getElementById('lat').value = lat;
                        document.getElementById('lng').value = lng;
                    }
                });
            }
            initMap();

        </script>
    </body>
</html>