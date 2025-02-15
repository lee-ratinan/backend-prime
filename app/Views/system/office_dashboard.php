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
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        Anything
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.btn-switch-role').on('click', function (e) {
                e.preventDefault();
                $('.btn-switch-role').prop('disabled', true);
                let new_role = $(this).data('role');
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/switch-role') ?>',
                    type: 'POST',
                    data: {role: new_role},
                    success: function (response) {
                        toastr.success('<?= lang('System.switch_role.switched') ?>');
                        setTimeout(function () {
                            window.location.href = '<?= base_url($session->locale . '/office/dashboard') ?>';
                        }, 3000);
                    },
                    error: function () {
                        $('.btn-switch-role').prop('disabled', false);
                        toastr.error('<?= lang('System.status_message.generic_error') ?>');
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>