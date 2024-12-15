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
    <!-- Nav Item - Dashboard (Super Admin & IT Department Only) -->
    <li class="nav-item <?= ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department'  || $_SESSION['role'] === 'exam_department' ) ? 'active' : 'disabled' ?>"
        id="navHover">
        <a class="nav-link <?= ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department' || $_SESSION['role'] === 'exam_department' ) ? '' : 'disabled' ?>"
            href="<?= ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department') ? 'index' : '#' ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <!-- OLD Master File (Super Admin Only) -->
    <!-- OLD Master File (Super Admin Only) -->
    <li class="nav-item <?= ($_SESSION['role'] === 'super_admin') ? (($current_url == 'masterFile' || $current_url == 'allocate_module_date' || $current_url == 'admin_allocate') ? 'active' : '') : 'disabled' ?>"
        id="navHover">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true"
                aria-controls="collapseTwo">
                <i class="fas fa-fw fa-exchange-alt"></i>
                <span>OLD Master File</span>
            </a>
            <div id="collapseTwo" class="collapse <?= ($_SESSION['role'] === 'super_admin') ? '' : 'disabled' ?>"
                aria-labelledby="headingOne" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Components</h6>
                    <a class="collapse-item <?= ($current_url == 'masterFile') ? 'active' : '' ?>"
                        href="masterFile">Master</a>
                    <a class="collapse-item <?= ($current_url == 'allocate_module_date') ? 'active' : '' ?>"
                        href="allocate_module_date">Module Dead Old</a>
                    <a class="collapse-item <?= ($current_url == 'admin_allocate') ? 'active' : '' ?>"
                        href="admin_allocate">Allocate Checker</a>
                </div>
            </div>
        <?php else: ?>
            <!-- <a class="nav-link disabled" href="#" style="pointer-events: none; opacity: 0.6;">
                <i class="fas fa-fw fa-exchange-alt"></i>
                <span>OLD Master File</span>
            </a> -->
        <?php endif; ?>
    </li>


    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item <?= ($current_url == '' || $current_url == 'portal_status' || $current_url == 'students_upload') ? 'active' : '' ?>"
        id="navHover">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true"
                aria-controls="collapseFour">
                <i class="fas fa-fw fa-exchange-alt"></i>
                <span>Master File</span>
            </a>
            <div id="collapseFour" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Components</h6>
                    <a class="collapse-item <?= ($current_url == 'portal_status') ? 'active' : '' ?>"
                        href="portal_status">Portal Active</a>
                    <a class="collapse-item <?= ($current_url == 'students_upload') ? 'active' : '' ?>"
                        href="students_upload">Upload Students</a>
                </div>
            </div>
        <?php else: ?>
            <a class="nav-link disabled" href="#" style="pointer-events: none; opacity: 0.6;">
                <i class="fas fa-fw fa-exchange-alt"></i>
                <span>Master File</span>
            </a>
        <?php endif; ?>
    </li>



    <!-- Module Dead New (Super Admin & IT Department Only) -->
    <li class="nav-item <?= ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department') ? (($current_url == 'allocate_module_date_new') ? 'active' : '') : 'disabled' ?>"
        id="navHover">
        <a class="nav-link <?= ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department'  || $_SESSION['role'] === 'exam_department' ) ? '' : 'disabled' ?>"
            href="allocate_module_date_new">
            <i class="fas fa-fw fa-table"></i>
            <span>Module Dead New</span></a>
    </li>

    <li class="nav-item <?= ($current_url == 'checkerAndstudent') ? 'active' : '' ?>" id="navHover">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a class="nav-link" href="checkerAndstudent">
                <i class="fas fa-fw fa-table"></i>
                <span>Std Submitted</span>
            </a>
        <?php else: ?>
            <a class="nav-link disabled" href="#" style="pointer-events: none; opacity: 0.6;">
                <i class="fas fa-fw fa-table"></i>
                <span>Std Submitted</span>
            </a>
        <?php endif; ?>
    </li>

    <li class="nav-item <?= ($current_url == 'checker_student_views') ? 'active' : '' ?>" id="navHover">
        <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'it_department' ): ?>
            <a class="nav-link" href="checker_student_views">
                <i class="fas fa-fw fa-table"></i>
                <span>Allocations</span></a>
        <?php else: ?>
            <a class="nav-link disabled" href="#" style="pointer-events: none; opacity: 0.6;">
                <i class="fas fa-fw fa-table"></i>
                <span>Allocations</span></a>
        <?php endif; ?>
    </li>


    <li class="nav-item <?= ($current_url == 'checker_reg') ? 'active' : '' ?>" id="navHover">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a class="nav-link" href="checker_reg">
                <i class="fas fa-fw fa-table"></i>
                <span>Checker Reg</span>
            </a>
        <?php else: ?>
            <a class="nav-link disabled" href="#" style="pointer-events: none; opacity: 0.6;">
                <i class="fas fa-fw fa-table"></i>
                <span>Checker Reg</span>
            </a>
        <?php endif; ?>
    </li>



</ul>
<!-- End of Sidebar -->