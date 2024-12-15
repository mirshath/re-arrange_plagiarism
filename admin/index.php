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
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
//     // Redirect to login page if not logged in or role is incorrect
//     echo '<script>window.location.href = "../login";</script>';
//     exit();
// }


// Ensure the user is logged in and has the correct role
if (
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'it_department' && $_SESSION['role'] !== 'exam_department')
) {
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
                <?php if (!empty($portalStatusMessage)): ?>
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
                    // document.addEventListener('DOMContentLoaded', function() {
                    //     var calendarEl = document.getElementById('calendar');
                    //     var calendar = new FullCalendar.Calendar(calendarEl, {
                    //         initialView: 'dayGridMonth', // Default month view
                    //         events: 'fetch_events.php', // Fetch events dynamically from the backend
                    //         headerToolbar: {
                    //             left: 'prev,next today',
                    //             center: 'title',
                    //             right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    //         },
                    //         eventContent: function(info) {
                    //             var eventTitle = info.event.title; // Get the title (module_name)
                    //             var eventDescription = info.event.extendedProps.description; // Get the description

                    //             // Check if the deadline has passed
                    //             var deadlineDate = new Date(info.event.start); // Event start date (deadline)
                    //             var currentDate = new Date(); // Current date
                    //             var isPast = deadlineDate < currentDate;

                    //             // Create a custom element for the title with inline style
                    //             var titleElement = document.createElement('div');
                    //             titleElement.innerHTML = eventTitle;

                    //             // Set the title color based on whether the deadline has passed
                    //             if (isPast) {
                    //                 titleElement.style.color = 'red'; // Red color for passed deadlines
                    //             } else {
                    //                 titleElement.style.color = 'white'; // Green color for upcoming deadlines
                    //             }

                    //             titleElement.style.backgroundColor = 'transparent'; // Ensure no background color

                    //             // Return the custom content for the event
                    //             return {
                    //                 domNodes: [titleElement]
                    //             };
                    //         }
                    //     });
                    //     calendar.render();
                    // });

                    document.addEventListener('DOMContentLoaded', function () {
                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth', // Default month view
                            events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            eventContent: function (info) {
                                var eventTitle = info.event.title; // Initially show program_name
                                var eventDescription = info.event.extendedProps.description; // Get the description

                                // Create a custom element for the title with inline style
                                var titleElement = document.createElement('div');
                                titleElement.innerHTML = eventTitle; // Display only the program_name initially

                                // Set the title color based on whether the deadline has passed
                                var deadlineDate = new Date(info.event.start); // Event start date (deadline)
                                var currentDate = new Date(); // Current date
                                var isPast = deadlineDate < currentDate;

                                // Set color of title
                                if (isPast) {
                                    titleElement.style.color = 'red'; // Past event
                                } else {
                                    titleElement.style.color = 'white'; // Upcoming event
                                }

                                titleElement.style.backgroundColor = 'transparent';
                                titleElement.style.padding = '5px 10px';
                                titleElement.style.borderRadius = '5px';

                                return {
                                    domNodes: [titleElement]
                                };
                            },
                            eventMouseEnter: function (info) {
                                // Create and show a tooltip with additional event details
                                var tooltip = document.createElement('div');
                                tooltip.innerHTML =
                                    '<strong>Program:</strong> ' + info.event.extendedProps.program_name +
                                    '<br><strong>Batch:</strong> ' + info.event.extendedProps.batch_name +
                                    '<br><strong>Module:</strong> ' + info.event.extendedProps.module_name +
                                    '<br><strong>Students Enrolled:</strong> ' + info.event.extendedProps.student_count; // Add student count
                                tooltip.style.position = 'absolute';
                                tooltip.style.backgroundColor = '#333';
                                tooltip.style.color = '#fff';
                                tooltip.style.padding = '10px';
                                tooltip.style.borderRadius = '5px';
                                tooltip.style.zIndex = '9999';
                                tooltip.style.pointerEvents = 'none'; // Prevent tooltip from blocking mouse events

                                // Get event element position and adjust tooltip position to be below the event
                                var rect = info.el.getBoundingClientRect();
                                tooltip.style.top = rect.bottom + 5 + 'px'; // Position below the event with a 5px margin
                                tooltip.style.left = rect.left + 'px';

                                document.body.appendChild(tooltip);

                                // Store tooltip reference to remove it later
                                info.el.tooltip = tooltip;
                            },
                            eventMouseLeave: function (info) {
                                // Remove tooltip when mouse leaves event
                                if (info.el.tooltip) {
                                    document.body.removeChild(info.el.tooltip);
                                }
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

<style>
    /* Add custom styling for the calendar */
    #calendar {
        max-width: 90%;
        margin: 0 auto;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .fc-header-toolbar {
        background-color: #4e73df;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .fc-button {
        background-color: #4e73df;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 8px 15px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .fc-button:hover {
        background-color: #2e59d9;
    }

    .fc-daygrid-day-number {
        font-size: 1.2em;
        font-weight: bold;
    }

    .fc-daygrid-event {
        border-radius: 5px;
        /* padding: 5px; */
        /* font-size: 0.9em; */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .fc-daygrid-event.fc-daygrid-event-dot {
        background-color: #f4f6f9;
        color: #333;
        border: none;
    }

    .fc-daygrid-day.fc-day-today {
        background-color: #f0f4f7;
    }

    .fc-daygrid-day.fc-day-other {
        background-color: #f9f9f9;
    }

    .fc-toolbar-title {
        font-size: 1.5em;
        font-weight: bold;
    }
</style>