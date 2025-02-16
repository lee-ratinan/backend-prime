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
                        <?php if (PERMISSION_EDITABLE == $permission_level) : ?>
                            <div class="text-end">
                                <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/user/create') ?>"><i class="fa-solid fa-plus"></i> <?= lang('User.edit.page_title_new') ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col">
                                <label for="account_type"><?= lang('TablesUser.UserMaster.account_type') ?></label>
                                <select class="form-select form-select-sm" id="account_type" name="account_type">
                                    <option value=""><?= lang('TablesUser.UserMaster.account_type') ?></option>
                                    <option value="S"><?= lang('TablesUser.UserMaster.account_type_values.S') ?></option>
                                    <option value="C"><?= lang('TablesUser.UserMaster.account_type_values.C') ?></option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="account_status"><?= lang('TablesUser.UserMaster.account_status') ?></label>
                                <select class="form-select form-select-sm" id="account_status" name="account_status">
                                    <option value=""><?= lang('TablesUser.UserMaster.account_status') ?></option>
                                    <option value="A"><?= lang('TablesUser.UserMaster.account_status_values.A') ?></option>
                                    <option value="B"><?= lang('TablesUser.UserMaster.account_status_values.B') ?></option>
                                    <option value="T"><?= lang('TablesUser.UserMaster.account_status_values.T') ?></option>
                                    <option value="P"><?= lang('TablesUser.UserMaster.account_status_values.P') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-end">
                                <button class="btn btn-sm btn-primary mt-3" type="button" id="btn-filter"><i class="fa-solid fa-filter"></i> <?= lang('System.menu.filter') ?></button>
                                <button class="btn btn-sm btn-link mt-3" type="button" id="btn-reset"><?= lang('System.menu.reset') ?></button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:80px"></th>
                                    <th style="min-width:50px"><?= lang('TablesUser.UserMaster.id') ?></th>
                                    <th style="min-width:220px"><?= lang('TablesUser.UserMaster.email_address') ?></th>
                                    <th style="min-width:150px"><?= lang('TablesUser.UserMaster.telephone_number') ?></th>
                                    <th style="min-width:200px"><?= lang('TablesUser.UserMaster.user_name') ?></th>
                                    <th style="min-width:100px"><?= lang('TablesUser.UserMaster.account_type') ?></th>
                                    <th style="min-width:100px"><?= lang('TablesUser.UserMaster.account_status') ?></th>
                                    <th style="min-width:150px"><?= lang('TablesUser.UserMaster.user_created_by') ?></th>
                                    <th style="min-width:200px"><?= lang('TablesUser.UserMaster.user_created_at') ?></th>
                                    <th style="min-width:200px"><?= lang('TablesUser.UserMaster.user_updated_at') ?></th>
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
                    url: '<?= base_url($session->locale . '/office/user') ?>',
                    type: 'POST',
                    data: function (data) {
                        data.account_type = $('#account_type').val();
                        data.account_status = $('#account_status').val();
                    }
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
            $('#btn-filter').on('click', function () {
                table.draw();
            });
            $('#btn-reset').on('click', function () {
                $('#account_status').val('');
                $('#account_type').val('');
                table.draw();
            });
        });
    </script>
<?php $this->endSection() ?>