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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/role') ?>"><?= lang('Role.index.page_title') ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <h6><i class="fa-solid fa-circle-info"></i> <?= lang('Role.edit.details') ?></h6>
                        <!-- FORM 1: ROLE_MASTER -->
                        <?php
                        echo '<div class="small">' . lang('Role.edit.role_name_note') . '</div>';
                        $role_master_config['role_name']['readonly'] = ('edit' == $mode);
                        $role_master_config['role_name']['disabled'] = ('edit' == $mode);
                        $fields = ['id', 'role_name', 'role_description'];
                        foreach ($fields as $field) {
                            generate_form_field($field, $role_master_config[$field], @$role_accesses['role_master'][$field]);
                        }
                        ?>
                        <?php if ('edit' == $mode) : ?>
                        <div class="row">
                            <?php
                            generate_label_column_from_field(lang($role_master_config['created_by']['label_key']), @$role_accesses['role_master']['user_name_first'] . ' ' . @$role_accesses['role_master']['user_name_family']);
                            generate_label_column_from_field(lang($role_master_config['created_at']['label_key']), @$role_accesses['role_master']['created_at'] ?? '', 'datetime');
                            generate_label_column_from_field(lang($role_master_config['updated_at']['label_key']), @$role_accesses['role_master']['updated_at'] ?? '', 'datetime');
                            ?>
                        </div>
                        <?php endif; ?>
                        <?php if (PERMISSION_EDITABLE == $permission_level) : ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-role-master"><i class="fa-solid fa-save"></i> <?= lang('System.menu.save') ?></button>
                        </div>
                        <?php endif; ?>
                        <!-- FORM 2: ROLE_ACCESS -->
                        <?php if ('edit' == $mode) : ?>
                            <?php
                            $accesses = [];
                            foreach ($role_accesses['role_accesses'] as $row) {
                                $accesses[$row['access_feature']] = $row;
                            }
                            ?>
                            <h6><i class="fa-solid fa-list-check"></i> <?= lang('Role.edit.granted_access') ?></h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th><?= lang('Role.edit.feature') ?></th>
                                        <th><?= lang('Role.edit.access') ?></th>
                                        <th><?= lang('Role.edit.details') ?></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($feature_master as $feature) : ?>
                                        <tr>
                                            <td><label for="access_level_<?= $feature ?>"><?= $feature ?></label></td>
                                            <td>
                                                <?php
                                                $this_access_level = @$accesses[$feature]['access_level'] ?? 0;
                                                ?>
                                                <select class="form-select form-select-sm" id="access_level_<?= $feature ?>" data-feature="<?= $feature ?>" data-role-id="<?= $role_accesses['role_master']['id'] ?>">
                                                    <?php for ($i = 0; $i < 3; $i++) : ?>
                                                        <option value="<?= $i ?>" <?= ("{$i}" == $this_access_level ? 'selected' : '') ?>><?= lang('TablesRole.RoleAccess.access_level_values.' . $i) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if (isset($accesses[$feature])) : ?>
                                                    <div class="row">
                                                        <?php
                                                        generate_label_column_from_field(lang($role_access_config['created_by']['label_key']), $accesses[$feature]['user_name_first'] . ' ' . $accesses[$feature]['user_name_family']);
                                                        generate_label_column_from_field(lang($role_access_config['created_at']['label_key']), $accesses[$feature]['created_at'] ?? '', 'datetime');
                                                        generate_label_column_from_field(lang($role_access_config['updated_at']['label_key']), $accesses[$feature]['updated_at'] ?? '', 'datetime');
                                                        ?>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (PERMISSION_EDITABLE == $permission_level) : ?>
                                                    <button class="btn btn-primary btn-sm btn-save-feature-access-level w-100" id="btn-access-level-<?= $feature ?>" data-for="access_level_<?= $feature ?>"><i class="fa-solid fa-save"></i> <?= lang('System.menu.save') ?></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let DateTime = luxon.DateTime;
            $('.utc-to-local-time').each(function () {
                const utc = $(this).text();
                $(this).text(DateTime.fromISO(utc).toLocaleString(DateTime.DATETIME_MED));
            });
            $('#role_name').on('keyup', function (e) {
                let role_name = $(this).val();
                role_name = role_name.toLowerCase().replace(/[_ ]/g, '-').replace(/[^a-z-]/g, '').replace(/-{2,}/g, '-');
                $(this).val(role_name);
            });
            $('#role_name').on('change', function (e) {
                let role_name = $(this).val();
                role_name = role_name.replace(/^-+|-+$/g, '');
                $(this).val(role_name);
            });
            $('#btn-save-role-master').on('click', function (e) {
                e.preventDefault();
                const id = $('#id').val(),
                    role_name = $('#role_name').val(),
                    role_description = $('#role_description').val();
                if ('' === role_name) {
                    toastr.error('<?= lang('Role.edit.role_name_required') ?>');
                    $('#role_name').focus();
                    return;
                } else if ('' === role_description) {
                    toastr.error('<?= lang('Role.edit.role_description_required') ?>');
                    $('#role_description').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/role/edit') ?>',
                    type: 'POST',
                    data: {
                        action: 'save-role-master',
                        id: id,
                        role_name: role_name,
                        role_description: role_description
                    },
                    success: function (response) {
                        $('#btn-save-role-master').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = response.redirect;
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let message = xhr.responseJSON.toast;
                        $('#btn-save-role-master').prop('disabled', false);
                        toastr.error(message);
                    }
                });
            });
            $('.btn-save-feature-access-level').on('click', function (e) {
                e.preventDefault();
                const target_id = $(this).data('for'),
                    feature = $('#' + target_id).data('feature'),
                    role_id = $('#' + target_id).data('role-id'),
                    access_level = $('#' + target_id).val();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/role/edit') ?>',
                    type: 'POST',
                    data: {
                        action: 'save-role-access',
                        feature: feature,
                        role_id: role_id,
                        access_level: access_level
                    },
                    success: function (response) {
                        $('#btn-access-level-' + feature).prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                location.reload();
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseJSON),
                            message = response.toast;
                        $('#btn-save-role-master').prop('disabled', false);
                        toastr.error(message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>