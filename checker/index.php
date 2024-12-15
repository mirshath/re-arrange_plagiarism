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

                <div class=" mb-5">
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
                    //         events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
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
                    //                 titleElement.style.color = 'red';
                    //             } else {
                    //                 titleElement.style.color = 'white';
                    //             }

                    //             titleElement.style.backgroundColor = 'transparent';
                    //             titleElement.style.padding = '5px 10px';
                    //             titleElement.style.borderRadius = '5px';

                    //             // Return the custom content for the event
                    //             return {
                    //                 domNodes: [titleElement]
                    //             };
                    //         }
                    //     });
                    //     calendar.render();
                    // });

                    // document.addEventListener('DOMContentLoaded', function() {
                    //     var calendarEl = document.getElementById('calendar');
                    //     var calendar = new FullCalendar.Calendar(calendarEl, {
                    //         initialView: 'dayGridMonth', // Default month view
                    //         events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
                    //         headerToolbar: {
                    //             left: 'prev,next today',
                    //             center: 'title',
                    //             right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    //         },
                    //         eventContent: function(info) {
                    //             var eventTitle = info.event.title; // Get the full title (program_name - batch_name - module_name)
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
                    //                 titleElement.style.color = 'red'; // For past events
                    //             } else {
                    //                 titleElement.style.color = 'white'; // For upcoming events
                    //             }

                    //             titleElement.style.backgroundColor = 'transparent';
                    //             titleElement.style.padding = '5px 10px';
                    //             titleElement.style.borderRadius = '5px';

                    //             // Return the custom content for the event
                    //             return {
                    //                 domNodes: [titleElement]
                    //             };
                    //         }
                    //     });
                    //     calendar.render();
                    // });




                    // document.addEventListener('DOMContentLoaded', function() {
                    //     var calendarEl = document.getElementById('calendar');
                    //     var calendar = new FullCalendar.Calendar(calendarEl, {
                    //         initialView: 'dayGridMonth', // Default month view
                    //         events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
                    //         headerToolbar: {
                    //             left: 'prev,next today',
                    //             center: 'title',
                    //             right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    //         },
                    //         eventContent: function(info) {
                    //             var eventTitle = info.event.title; // Initially show program_name
                    //             var eventDescription = info.event.extendedProps.description; // Get the description

                    //             // Create a custom element for the title with inline style
                    //             var titleElement = document.createElement('div');
                    //             titleElement.innerHTML = eventTitle; // Display only the program_name initially

                    //             // Set the title color based on whether the deadline has passed
                    //             var deadlineDate = new Date(info.event.start); // Event start date (deadline)
                    //             var currentDate = new Date(); // Current date
                    //             var isPast = deadlineDate < currentDate;

                    //             // Set color of title
                    //             if (isPast) {
                    //                 titleElement.style.color = 'red'; // Past event
                    //             } else {
                    //                 titleElement.style.color = 'white'; // Upcoming event
                    //             }

                    //             titleElement.style.backgroundColor = 'transparent';
                    //             titleElement.style.padding = '5px 10px';
                    //             titleElement.style.borderRadius = '5px';

                    //             return {
                    //                 domNodes: [titleElement]
                    //             };
                    //         },
                    //         eventMouseEnter: function(info) {
                    //             var tooltip = document.createElement('div');
                    //             tooltip.innerHTML =
                    //                 '<strong>Program:</strong> ' + info.event.extendedProps.program_name +
                    //                 '<br><strong>Batch:</strong> ' + info.event.extendedProps.batch_name +
                    //                 '<br><strong>Module:</strong> ' + info.event.extendedProps.module_name;
                    //             tooltip.style.position = 'absolute';
                    //             tooltip.style.backgroundColor = '#333';
                    //             tooltip.style.color = '#fff';
                    //             tooltip.style.padding = '10px';
                    //             tooltip.style.borderRadius = '5px';
                    //             tooltip.style.zIndex = '9999';
                    //             tooltip.style.pointerEvents = 'none'; // Prevent tooltip from blocking mouse events

                    //             // Set tooltip position
                    //             var rect = info.el.getBoundingClientRect();
                    //             tooltip.style.top = rect.top + 10 + 'px'; // Position below the event
                    //             tooltip.style.left = rect.left + 'px';

                    //             document.body.appendChild(tooltip);

                    //             // Store tooltip reference to remove it later
                    //             info.el.tooltip = tooltip;
                    //         },
                    //         eventMouseLeave: function(info) {
                    //             // Remove tooltip when mouse leaves event
                    //             if (info.el.tooltip) {
                    //                 document.body.removeChild(info.el.tooltip);
                    //             }
                    //         }
                    //     });
                    //     calendar.render();
                    // });



                    // document.addEventListener('DOMContentLoaded', function() {
                    //     var calendarEl = document.getElementById('calendar');
                    //     var calendar = new FullCalendar.Calendar(calendarEl, {
                    //         initialView: 'dayGridMonth', // Default month view
                    //         events: '../admin/fetch_events.php', // Fetch events dynamically from the backend
                    //         headerToolbar: {
                    //             left: 'prev,next today',
                    //             center: 'title',
                    //             right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    //         },
                    //         eventContent: function(info) {
                    //             var eventTitle = info.event.title; // Initially show program_name
                    //             var eventDescription = info.event.extendedProps.description; // Get the description

                    //             // Create a custom element for the title with inline style
                    //             var titleElement = document.createElement('div');
                    //             titleElement.innerHTML = eventTitle; // Display only the program_name initially

                    //             // Set the title color based on whether the deadline has passed
                    //             var deadlineDate = new Date(info.event.start); // Event start date (deadline)
                    //             var currentDate = new Date(); // Current date
                    //             var isPast = deadlineDate < currentDate;

                    //             // Set color of title
                    //             if (isPast) {
                    //                 titleElement.style.color = 'red'; // Past event
                    //             } else {
                    //                 titleElement.style.color = 'white'; // Upcoming event
                    //             }

                    //             titleElement.style.backgroundColor = 'transparent';
                    //             titleElement.style.padding = '5px 10px';
                    //             titleElement.style.borderRadius = '5px';

                    //             return {
                    //                 domNodes: [titleElement]
                    //             };
                    //         },
                    //         eventMouseEnter: function(info) {
                    //             var tooltip = document.createElement('div');
                    //             tooltip.innerHTML =
                    //                 '<strong>Program:</strong> ' + info.event.extendedProps.program_name +
                    //                 '<br><strong>Batch:</strong> ' + info.event.extendedProps.batch_name +
                    //                 '<br><strong>Module:</strong> ' + info.event.extendedProps.module_name;
                    //             tooltip.style.position = 'absolute';
                    //             tooltip.style.backgroundColor = '#333';
                    //             tooltip.style.color = '#fff';
                    //             tooltip.style.padding = '10px';
                    //             tooltip.style.borderRadius = '5px';
                    //             tooltip.style.zIndex = '9999';
                    //             tooltip.style.pointerEvents = 'none'; // Prevent tooltip from blocking mouse events

                    //             // Set tooltip position
                    //             var rect = info.el.getBoundingClientRect();
                    //             tooltip.style.top = rect.top + 10 + 'px'; // Position below the event
                    //             tooltip.style.left = rect.left + 'px';

                    //             document.body.appendChild(tooltip);

                    //             // Store tooltip reference to remove it later
                    //             info.el.tooltip = tooltip;
                    //         },
                    //         eventMouseLeave: function(info) {
                    //             // Remove tooltip when mouse leaves event
                    //             if (info.el.tooltip) {
                    //                 document.body.removeChild(info.el.tooltip);
                    //             }
                    //         }
                    //     });
                    //     calendar.render();
                    // });


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
                            eventMouseEnter: function(info) {
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
                            eventMouseLeave: function(info) {
                                // Remove tooltip when mouse leaves event
                                if (info.el.tooltip) {
                                    document.body.removeChild(info.el.tooltip);
                                }
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