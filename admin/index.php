<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
// if ($_SESSION['role'] !== 'super_admin') {
//     // header("Location: ../login.php"); 
//     echo '<script>window.location.href = "../login.php";</script>';
//     exit();
// }



// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login";</script>';
    exit();
}




// Fetch the portal status from the database
$query = "SELECT portal_status FROM portal WHERE id = 1"; // Adjust the query if needed based on your database structure
$result = $conn->query($query);
$portalStatusMessage = '';

// If the portal status is found, check if it is 'off'
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['portal_status'] === 'off') {
        $portalStatusMessage = 'The portal is not available for use at the moment.';
    }
}




?>

<style>
    #calendar {
        /* max-width:500px; */
        margin: 40px auto;
        font-family: Arial, sans-serif;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        /* padding: 20px; */
        background-color: #f9f9f9;
        border-radius: 8px;
        font-size: 15px;
    }

    /* Custom CSS for green event titles */
    .green-event {
        color: green !important;
        /* Green color for module name */
    }

    .fc-h-event .fc-event-title-container {
        flex-grow: 1;
        flex-shrink: 1;
        min-width: 0px;
        font-size: 10px;
    }
</style>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include("nav.php"); ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <?php include("includes/topnav.php"); ?>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">Dashboard</h4>
                </div>

                <!-- Display Portal Status Message if Portal is Closed -->
                <?php if (!empty($portalStatusMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading">Portal is currently closed!</h5>
                        <p><?php echo $portalStatusMessage; ?></p>
                    </div>
                <?php else: ?>
                    <!-- Your normal dashboard content -->
                    <div class="container">
                        <?php
                        // echo "<h1>Welcome, Admin</h1>";
                        ?>
                    </div>
                <?php endif; ?>


                <!-- --------------------------------------------------------------  -->
                <div class="container mt-5 mb-5">
                    <div id="calendar"></div>
                </div>

                <!-- --------------------------------------------------------------  -->
                <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth', // Default month view
                            events: 'fetch_events.php', // Fetch events dynamically from the backend
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            eventContent: function(info) {
                                var eventTitle = info.event.title; // Get the title (module_name)
                                var eventDescription = info.event.extendedProps.description; // Get the description

                                // Check if the deadline has passed
                                var deadlineDate = new Date(info.event.start); // Event start date (deadline)
                                var currentDate = new Date(); // Current date
                                var isPast = deadlineDate < currentDate;

                                // Create a custom element for the title with inline style
                                var titleElement = document.createElement('div');
                                titleElement.innerHTML = eventTitle;

                                // Set the title color based on whether the deadline has passed
                                if (isPast) {
                                    titleElement.style.color = 'red'; // Red color for passed deadlines
                                } else {
                                    titleElement.style.color = 'white'; // Green color for upcoming deadlines
                                }

                                titleElement.style.backgroundColor = 'transparent'; // Ensure no background color

                                // Return the custom content for the event
                                return {
                                    domNodes: [titleElement]
                                };
                            }
                        });
                        calendar.render();
                    });
                </script>
                <!-- --------------------------------------------------------------  -->


            </div>
            <!-- End Page Content -->

        </div>
        <!-- End Main Content -->
    </div>
    <!-- End Content Wrapper -->
</div>
<!-- End Page Wrapper -->

</body>

</html>

<?php $conn->close(); ?>