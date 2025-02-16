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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/log/log-file') ?>"><?= lang('Log.file_list.page_title') ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <h6><?= lang('Log.file_view.file_name') ?>: <?= $file_name ?></h6>
                        <pre id="file-content"><?= $file_content ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileContent = document.getElementById('file-content');
            fileContent.innerHTML = fileContent.innerHTML.replace(/(ERROR|CRITICAL|ALERT|EMERGENCY)/g, '<span class="text-danger">$1</span>');
            fileContent.innerHTML = fileContent.innerHTML.replace(/(WARNING)/g, '<span class="text-warning">$1</span>');
            fileContent.innerHTML = fileContent.innerHTML.replace(/(DEBUG|INFO)/g, '<span class="text-info">$1</span>');
        });
    </script>
<?php $this->endSection() ?>