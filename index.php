<?php
// Include necessary files for constants, database configuration, and the Report model
require_once 'constants.php';  // Contains application-wide constants
require_once 'config/DatabaseConfiguration.php';  // Handles database connection setup
require_once 'models/Report.php';  // Defines the Report model for interacting with the database

// Check if a logout message is present in the query parameters
$showAlert = isset($_GET['message']) && $_GET['message'] === 'logged_out';

// Initialize the database connection and create a Report instance
$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

// Fetch all approved missing persons reports from the database
$missingPersons = $report->getApprovedMissingPersons();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Missing Person Application</title>
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/master.css" rel="stylesheet">
        
    </script>
    <style>
        body {
            padding-top: 56px;  /* Offset for fixed navbar */
            background: url('images/missingimg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
        }
        .navbar-dark .navbar-nav .nav-link {
            color: white;
        }
        #loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-1" href="#">Missing Person App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse me-1" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="views/login.php">Login <?php echo getIcon('login');?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="views/register.php">Register <?php echo getIcon('register');?></a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-12 my-5">

                <form id="searchForm" class="d-flex justify-content-center mb-4">
                    <div class="form-group mx-2">
                        <label for="searchName" class="visually-hidden">Name</label>
                        <input type="text" class="form-control" id="searchName" placeholder="Name">
                    </div>
                    <div class="form-group mx-2">
                        <label for="searchLocation" class="visually-hidden">Location</label>
                        <input type="text" class="form-control" id="searchLocation" placeholder="Location">
                    </div>
                    <div class="form-group mx-2">
                        <label for="searchAge" class="visually-hidden">Age</label>
                        <input type="number" class="form-control" id="searchAge" placeholder="Age">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                </form>
                <div class="text-center my-4">
                    <button id="viewMissingButton" class="btn btn-primary">View Missing People</button>
                </div>
                <div id="typed-Wrapper" class="row text-center text-info bg-black">
                    <h1 class="text-center text-info fw-bold text-capitalize text" id="welcome-text">Missing Persons Zambia</h1>
                </div>
                <hr>
                <div class="col-12 bg-dark text-info text-capitalize fw-bold fs-3 mb-2 shadow rounded-2">
                    People across the world are experiencing the devastating impact of having someone go missing. Others are on their own journey of being away from home. Find comfort and support through peer stories, share your own advice, meet in person or virtually, or join our private, online discussion space
                </div>



                <div class="row" id="missing-persons" style="display: none;">
                    <!-- Missing persons data will be loaded here -->
                </div>

                <div id="loading">
                    <p>Loading more items...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer inclusion -->
    <?php include 'views/templates/footer.php'; ?>

    <!-- JavaScript libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    
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
        $(document).ready(function() {
    let page = 1;  // Current page number for pagination
    let loading = false;  // Flag to prevent multiple simultaneous AJAX requests
    let searchParams = {};  // Object to store search parameters

    $('#viewMissingButton').on('click', function() {
        $('#missing-persons').show();
        loadMoreData();
        window.location.href = "#missing-persons";
    });
    function loadMoreData() {
        if (loading) return;  // Prevent further execution if already loading
        loading = true;  // Set loading flag to true
        $('#loading').show();  // Show loading indicator

        $.ajax({  
            url: 'fetch_data.php',  // URL of the script to fetch data  
            type: 'GET',  
            data: { page: page, ...searchParams },  // Include pagination and search parameters  
            success: function(response) {  
                if (response.trim() === "") {  
                    $(window).off('scroll');  // Stop loading more data if no more items  
                    $('#loading').html('<p>No more items to load.</p>');  
                } else {  
                    $('#missing-persons').append(response);  // Append new data to the container  
                    page++;  // Increment page number for the next load  
                }  
                loading = false;  // Reset loading flag  
                $('#loading').hide();  // Hide loading indicator  

                // Initialize the map when the modal is shown  
                $('[id^="staticBackdrop"]').on('shown.bs.modal', function (event) {  
                    const modalId = $(this).attr('id');  
                    const mapId = `map${modalId.replace('staticBackdrop', '')}`;  // Get unique map ID
                    const latitude = $(this).data('latitude');  
                    const longitude = $(this).data('longitude');

                    async function initMap(mapId,latitude,longitude) {
                
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

                // Create a new map centered at report coordinates
                var map = new Map(document.getElementById(''+mapId), {
                    center: {lat: parseFloat(latitude), lng: parseFloat(longitude)},
                    zoom: 8,
                    mapId: "roadmap",
                });

                // Create a  marker at report coordinates
                var marker = new AdvancedMarkerElement({
                    position: {lat: parseFloat(latitude), lng: parseFloat(longitude)},
                    map: map,
                    draggable: false
                });
            }
            initMap(mapId,latitude,longitude);
                });
            }
        });
    }

    // Function to update search parameters and reload data
    function updateSearchParams() {
        searchParams = {
            name: $('#searchName').val(),
            location: $('#searchLocation').val(),
            age: $('#searchAge').val()
        };
        page = 1;  // Reset page number to 1
        $('#missing-persons').empty();  // Clear the existing data
        loadMoreData();  // Load new data based on updated search parameters
    }

    // Infinite scroll functionality to load more data
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadMoreData();
        }
    });

    // Handle search form submission
    $('#searchForm').on('submit', function(event) {
        
        event.preventDefault();  // Prevent default form submission
        updateSearchParams();  // Update search parameters and reload data
        $('#missing-persons').show();//show the reports container
    });

});


        // Script to inform the user if they have been logged out
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($showAlert): ?>
                Swal.fire({
                    title: 'Logged out',
                    icon: 'success'
                }).then(() => {
                    // Remove 'message' query parameter from the URL
                    let url = new URL(window.location.href);
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url.toString());
                });
            <?php endif; ?>
        });
    </script>
    <script src="libraries/typed.js"></script>
    <script>
        
        var typed = new Typed("#welcome-text", {
            strings: [
            "Missing Persons Zambia",
            "Find Your Loved Ones",
            "Report Missing Persons",
            "Together We Can Make a Difference"
            ],
            typeSpeed: 60,
            backSpeed: 150,
            backDelay: 1000,
            loop: true,
            showCursor: true,
        });
    </script>
</body>
</html>
