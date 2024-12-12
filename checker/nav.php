<?php
$current_url = basename($_SERVER['REQUEST_URI'], ".php");
?>

<style>
    .nav-item.active .nav-link {
        color: white !important;
        background: rgb(203, 0, 0);
        background: radial-gradient(circle, rgba(203, 0, 0, 1) 0%, rgba(110, 4, 4, 1) 92%);
        /* border-radius: 5px; */
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }

    #navHover:hover .nav-link {
        color: white !important;
        background: rgb(203, 0, 0);
        background: radial-gradient(circle, rgba(203, 0, 0, 1) 0%, rgba(110, 4, 4, 1) 92%);
        /* border-radius: 5px; */
        transition: background-color 0.3s ease;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }
</style>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center mt-4 mb-4" href="index">
        <div class="sidebar-brand-icon rotate-n-15">
            <!-- <i class="fas fa-laugh-wink"></i> -->
        </div>
        <!-- <div class="sidebar-brand-text mx-3">IMS <sup>2</sup></div> -->
        <div class="sidebar-brand-text mx-3"><img src="http://184.174.39.59:216/img/logo4.png" class="img-fluid"></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= ($current_url == 'index') ? 'active' : '' ?>" id="navHover">
        <a class="nav-link" href="index">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>


    <li class="nav-item <?= ($current_url == 'allocated_students') ? 'active' : '' ?>" id="navHover">
        <a class="nav-link" href="allocated_students">
            <i class="fas fa-fw fa-table"></i>
            <span>Allocated Students</span></a>
    </li>


    <li class="nav-item <?= ($current_url == 'report_submitted_students') ? 'active' : '' ?>" id="navHover">
        <a class="nav-link" href="report_submitted_students">
            <i class="fas fa-fw fa-table"></i>
            <span>Pending Doc </span></a>
    </li>


    <li class="nav-item <?= ($current_url == 'report_submitted_students_checked') ? 'active' : '' ?>" id="navHover">
        <a class="nav-link" href="report_submitted_students_checked">
            <i class="fas fa-fw fa-table"></i>
            <span>Checked Doc</span></a>
    </li>

</ul>
<!-- End of Sidebar -->