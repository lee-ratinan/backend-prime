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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <p><?= lang('System.switch_role.current_role_is', [$current_role]) ?></p>
                        <?php if (1 == count($roles)): ?>
                            <p><?= lang('System.switch_role.you_have_1_role') ?></p>
                        <?php else: ?>
                            <p><?= lang('System.switch_role.pick_new_role') ?></p>
                            <?php
                            $options = [];
                            foreach ($roles as $role) {
                                if ($role == $current_role) continue;
                                $options[$role] = $role;
                            }
                            generate_form_field('role', [
                                'type'        => 'select',
                                'label_key'   => 'System.switch_role.role',
                                'required'    => true,
                                'options'     => $options
                            ]);
                            ?>
                            <div class="text-end">
                                <button id="btn-switch" type="submit" class="btn btn-primary"><i class="fa-solid fa-arrows-rotate"></i> <?= lang('System.switch_role.switch_btn') ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-switch').on('click', function (e) {
                e.preventDefault();
                if ('' === $('#role').val()) {
                    $('#role').focus();
                    return;
                }
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/switch-role') ?>',
                    type: 'POST',
                    data: {role: $('#role').val()},
                    success: function (response) {
                        toastr.success('<?= lang('System.switch_role.switched') ?>');
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    },
                    error: function () {
                        toastr.error('<?= lang('System.status_message.generic_error') ?>');
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>