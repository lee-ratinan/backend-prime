<?php $session = session(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $page_title ?> | <?= $session->app_name ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- Favicons -->
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/quill/quill.snow.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/quill/quill.bubble.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/remixicon/remixicon.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/simple-datatables/style.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/toastrjs/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/DataTables/datatables.min.css') ?>" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <!-- =======================================================
    * Template Name: NiceAdmin
    * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
    * Updated: Apr 20 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>
<body>
<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= base_url($session->locale . '/office/dashboard') ?>" class="logo d-flex align-items-center">
            <?= $session->app_logo ?>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <!-- Header / Search Bar -->
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="<?= base_url($session->locale . '/office/search') ?>">
            <input type="text" name="q" placeholder="<?= lang('System.menu.search_placeholder') ?>" title="<?= lang('System.menu.search') ?>">
            <button type="submit" id="btn-search" title="<?= lang('System.menu.search') ?>"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
            </li>
            <!-- Header / Notifications -->
            <li class="nav-item dropdown d-none">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge bg-primary badge-number">4</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                    <li class="dropdown-footer">###</li>
                </ul>
            </li>
            <!-- Header / Profile -->
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <?= $session->avatar ?>
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= $session->display_name ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?= $session->display_name ?></h6>
                        <span><?= $session->user['employee_title'] ?><br><?= lang('System.menu.role_is', [$session->current_role]) ?></span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= base_url($session->locale . '/office/profile') ?>"><i class="fa-solid fa-user-cog fa-fw me-3"></i><span><?= lang('System.menu.my_profile') ?></span></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= base_url($session->locale . '/office/switch-role') ?>"><i class="fa-solid fa-arrows-rotate fa-fw me-3"></i><span><?= lang('System.menu.switch_role') ?></span></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= base_url('logout') ?>"><i class="fa-solid fa-right-from-bracket fa-fw me-3"></i><span><?= lang('System.menu.log_out') ?></span></a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link <?= ('dashboard' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/dashboard') ?>"><i class="fa-solid fa-house-chimney fa-fw me-3"></i><span><?= lang('System.dashboard.page_title') ?></span></a></li>
        <!-- AREA FOR OTHER MENU -->
        <!-- USER MASTER -->
        <?php if (isset($session->permitted_features['user_master'])): ?>
            <li class="nav-item"><a class="nav-link <?= ('user' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/user') ?>"><i class="fa-solid fa-user fa-fw me-3"></i><span><?= lang('User.index.page_title') ?></span></a></li>
        <?php endif; ?>
        <!-- ROLE MASTER -->
        <?php if (isset($session->permitted_features['role_master'])): ?>
            <li class="nav-item"><a class="nav-link <?= ('role' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/role') ?>"><i class="fa-solid fa-list-check fa-fw me-3"></i><span><?= lang('Role.index.page_title') ?></span></a></li>
        <?php endif; ?>
        <!-- LOG -->
        <?php if (isset($session->permitted_features['log'])): ?>
            <li class="nav-item"><a class="nav-link <?= ('log' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/log') ?>"><i class="fa-solid fa-list fa-fw me-3"></i><span><?= lang('Log.index.page_title') ?></span></a></li>
            <li class="nav-item"><a class="nav-link <?= ('log-email' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/log/email') ?>"><i class="fa-solid fa-list fa-fw me-3"></i><span><?= lang('Log.email.page_title') ?></span></a></li>
            <li class="nav-item"><a class="nav-link <?= ('log-file' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/log/log-file') ?>"><i class="fa-solid fa-list fa-fw me-3"></i><span><?= lang('Log.file_list.page_title') ?></span></a></li>
        <?php endif; ?>
        <!-- ORGANIZATION -->
        <?php if (isset($session->permitted_features['organization'])): ?>
            <li class="nav-item"><a class="nav-link <?= ('organization' == $slug ? '' : 'collapsed' ) ?>" href="<?= base_url($session->locale . '/office/organization') ?>"><i class="fa-solid fa-building fa-fw me-3"></i><span><?= lang('Organization.page_title') ?></span></a></li>
        <?php endif; ?>
    </ul>
</aside>
<!-- MAIN -->
<main id="main" class="main">
    <?= $this->renderSection('content') ?>
</main>
<!-- FOOTER -->
<footer id="footer" class="footer">
    <div class="copyright">
        <?= lang('System.menu.copyrights', [$session->organization['organization_name'], date('Y')]) ?>
    </div>
</footer>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="<?= base_url('assets/vendor/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fontawesome/js/all.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/chart.js/chart.umd.js') ?>"></script>
<script src="<?= base_url('assets/vendor/echarts/echarts.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/quill/quill.js') ?>"></script>
<script src="<?= base_url('assets/vendor/simple-datatables/simple-datatables.js') ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/php-email-form/validate.js') ?>"></script>
<script src="<?= base_url('assets/vendor/toastrjs/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/Luxon/luxon.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/DataTables/datatables.min.js') ?>"></script>
<!-- Template Main JS File -->
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script>
    $(function () { setTimeout(() => {window.location.href = '<?= base_url('logout') ?>';}, 300000); });
</script>
</body>
</html>