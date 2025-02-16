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
                        <div class="text-end">
                            <a class="btn btn-outline-primary btn-sm ms-3" href="<?= base_url($session->locale . '/office/role/feature') ?>"><i class="fa-solid fa-eye"></i> <?= lang('Role.role_feature.page_title') ?></a>
                            <?php if (PERMISSION_EDITABLE == $permission_level) : ?>
                                <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/role/create') ?>"><i class="fa-solid fa-plus"></i> <?= lang('Role.index.new_role') ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th><?= lang('TablesRole.RoleMaster.id') ?></th>
                                    <th><?= lang('TablesRole.RoleMaster.role_name') ?></th>
                                    <th><?= lang('TablesRole.RoleMaster.role_description') ?></th>
                                    <th><?= lang('TablesRole.RoleMaster.created_by') ?></th>
                                    <th><?= lang('TablesRole.RoleMaster.created_at') ?></th>
                                    <th><?= lang('TablesRole.RoleMaster.updated_at') ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: true,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/role') ?>',
                    type: 'POST'
                },
                order: [[2, 'asc']],
                columnDefs: [{orderable: false, targets: 0}],
                drawCallback: function () {
                    let DateTime = luxon.DateTime;
                    $('.utc-to-local-time').each(function () {
                        const utc = $(this).text();
                        if ('' !== utc) {
                            $(this).text(DateTime.fromISO(utc).toLocaleString(DateTime.DATETIME_MED));
                        } else {
                            $(this).text('-');
                        }
                    });
                },
            });
        });
    </script>
<?php $this->endSection() ?>