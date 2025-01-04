<?php
$layout = getenv('LAYOUT_FILE_PUBLIC');
$layout = (!empty($layout) ? $layout : 'system/_layout_public');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <div class="d-flex justify-content-center py-4">
        <div class="d-flex justify-content-center px-5 py-4">
            <?= $session->app_logo ?>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-12 pt-4">
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.register.heading') ?></h5>
                    <p class="text-center small"><?= lang('Auth.register.subheading') ?></p>
                </div>
                <div class="col-12 text-center">
                    <p>This feature is reserved for future use.</p>
                    <a class="btn btn-link mt-3 w-100" href="<?= base_url('login') ?>"><?= lang('Auth.login.page_title') ?></a>
                </div>
            </form>
        </div>
    </div>
<?php $this->endSection() ?>