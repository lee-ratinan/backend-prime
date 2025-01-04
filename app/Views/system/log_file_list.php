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
                    <div class="card-body pt-3">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <tbody>
                                <?php foreach ($error_files as $file) : ?>
                                    <?php
                                    if (in_array($file, ['.', '..', 'index.html'])) {
                                        continue;
                                    }
                                    $date_part = str_replace(['log-', '.log'], '', $file);
                                    $file_url  = base_url($session->locale . '/office/log/log-file/' . $date_part);
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="btn btn-outline-primary btn-sm me-3" href="<?= $file_url ?>"><i class="fa-solid fa-eye"></i></a>
                                            <?= date(DATE_FORMAT_UI, strtotime($date_part)) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>