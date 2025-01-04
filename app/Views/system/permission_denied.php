<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body p-5 text-center">
                        <h2><i class="fa-solid fa-hand text-danger"></i><br><?= lang('System.permission_denied.page_title') ?></h2>
                        <p><?= lang('System.permission_denied.message') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>