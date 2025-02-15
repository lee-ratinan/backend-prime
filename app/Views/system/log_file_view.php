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
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <h6><?= lang('Log.file_view.file_name') ?>: <?= $file_name ?></h6>
                        <pre><?php
                            $lines = explode("\n", $file_content);
                            foreach ($lines as $line) {
                                // highlight the log level
                                $line = preg_replace('/(EMERGENCY|ALERT|CRITICAL|ERROR)/', '<b class="text-danger">$1</b>', $line);
                                $line = preg_replace('/(ErrorException:)/', '<em class="text-danger">$1</em>', $line);
                                $line = preg_replace('/(NOTICE|WARNING)/', '<b class="text-warning">$1</b>', $line);
                                $line = preg_replace('/(DEBUG|INFORMATIONAL)/', '<b class="text-info">$1</b>', $line);
                                echo $line . "\n";
                            }
                            ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>