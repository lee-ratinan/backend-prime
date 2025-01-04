<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <div class="pagetitle">
        <h1><?= lang('System.dashboard.welcome', [$session->display_name]) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
<?php $this->endSection() ?>