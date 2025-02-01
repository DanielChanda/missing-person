<?php
// Include the database configuration and Report model
require_once 'config/DatabaseConfiguration.php';
require_once 'models/Report.php';

// Initialize database connection and Report model
$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

// Get pagination and search parameters
// Get the current page for pagination (default to 1 if not provided)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Set the number of records to display per page
$limit = 10;

// Calculate the offset for the SQL query based on the current page
$offset = ($page - 1) * $limit;

//Get the person's name to search for (default to empty string if not provided)
$name = isset($_GET['name']) ? $_GET['name'] : '';

//Get the person's location to search for (default to empty string if not provided)
$location = isset($_GET['location']) ? $_GET['location'] : '';

//Get the person's age to search for (default to empty string if not provided)
$age = isset($_GET['age']) ? $_GET['age'] : '';

// Fetch reports based on search criteria and pagination
$reports = $report->searchReportsAdvanced($name, $location, $age, $limit, $offset);

// Check if there are any reports to display
if (empty($reports)) {
    echo ""; // Return empty string if no reports found
} else {
    // Loop through each report and format it as HTML
    foreach ($reports as $person) {
        // Sanitize and prepare data for display
        $imagePath = str_replace('../', '', htmlspecialchars($person['image_path']));
        $name = htmlspecialchars($person['name']);
        $lastSeen = htmlspecialchars($person['last_seen']);
        $age = htmlspecialchars($person['age']);
        $type = htmlspecialchars($person['type']);
        $gender = htmlspecialchars($person['gender']);
        $description = htmlspecialchars($person['description']);
        $contactInfo = htmlspecialchars($person['contact_info']);
        $latitude = htmlspecialchars($person['latitude']);
        $longitude = htmlspecialchars($person['longitude']);
        $id = htmlspecialchars($person['id']);
        
    
        echo <<<HTML
        <div class="col-md-4">
            <!-- Display the list of missing persons as cards -->
            <div class="card mb-4 shadow-sm">
                <div class="card-img-container">
                    <!-- Display the image of the missing person -->
                    <img src="$imagePath" class="card-img" alt="Missing Person">
                </div>
                <div class="card-body">
                    <!-- Display the name and basic details of the missing person -->
                    <h5 class="card-title">$name</h5>
                    <p class="card-text">Last seen: $lastSeen</p>
                    <p class="card-text">Age: $age</p>
                    <p class="card-text">Status: $type</p>
                    <!-- Button to open the modal with more details -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop$id">View Details</button>
                </div>
            </div>
        </div>
        <!-- Modal for detailed view of the missing person -->
        <div class="modal fade" id="staticBackdrop$id" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" data-latitude="$latitude" data-longitude="$longitude">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- Modal header with title -->
                        <h5 class="modal-title" id="staticBackdropLabel">Details for $name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Detailed card inside the modal -->
                        <div class="card w-100 mb-4 shadow-sm">
                            <!-- Display the image of the missing person in the modal -->
                            <img src="$imagePath" class="card-img-top" alt="Missing Person">
                            <div class="card-body">
                                <!-- Display detailed information of the missing person -->
                                <h5 class="card-title">$name</h5>
                                <p class="card-text"><strong>Last seen:</strong> $lastSeen</p>
                                <p class="card-text"><strong>Age:</strong> $age</p>
                                <p class="card-text"><strong>Status:</strong> $type</p>
                                <p class="card-text"><strong>Gender:</strong> $gender</p>
                                <p class="card-text"><strong>Description:</strong> $description</p>
                                <p class="card-text"><strong>Contact Information:</strong> <a href="tel:+26$contactInfo"> $contactInfo</a></p>
                                <!-- Map container for the last seen location -->
                                <div id="map$id" style="height: 400px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Modal footer with a close button -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of the modal -->
        HTML;
    
        // Add script to initialize the map when the modal is shown
        echo <<<SCRIPT
        <script>
            document.getElementById('staticBackdrop$id').addEventListener('shown.bs.modal', function () {
                const { Map } = google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = google.maps.importLibrary("marker");

                // Create a new map centered at default coordinates
                var map = new Map(document.getElementById('map$id'), {
                    center: {lat: $latitude, lng: $longitude},
                    zoom: 12,
                    mapId: "DEMO_MAP_ID",
                });
                
                // Create a  marker at given coordinates
                var marker = new AdvancedMarkerElement({
                    position: {lat: $latitude, lng: $longitude},
                    map: map,
                    draggable: false
                });
            });
        </script>
        SCRIPT;
    }
}
?>