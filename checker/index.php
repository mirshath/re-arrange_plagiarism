<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'plagiarism_checker') {
    echo '<script>window.location.href = "../login";</script>';
    exit();
}

?>

<!-- Page Wrapper -->
<div id="wrapper">

    <?php include("nav.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">Dashboard</h4>
                </div>

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
                            events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
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
                                    titleElement.style.color = 'red'; 
                                } else {
                                    titleElement.style.color = 'white'; 
                                }

                                titleElement.style.backgroundColor = 'transparent';

                                // Return the custom content for the event
                                return {
                                    domNodes: [titleElement]
                                };
                            }
                        });
                        calendar.render();
                    });
                </script>

            </div>

        </div>

    </div>

</div>


</body>
</html>
<?php $conn->close(); ?>