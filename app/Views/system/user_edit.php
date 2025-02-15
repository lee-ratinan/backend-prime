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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/user') ?>"><?= lang('User.index.page_title') ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <h6><i class="fa-solid fa-user-lock"></i> <?= lang('User.edit.controlled_account_data') ?></h6>
                        <?php
                        $fields = ['id', 'email_address', 'user_name_first', 'user_name_family', 'user_gender', 'user_nationality', 'account_status', 'account_type', 'employee_id', 'employee_start_date', 'employee_end_date', 'employee_title'];
                        foreach ($fields as $field) {
                            generate_form_field($field, $user_configuration[$field], @$user[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-user-master"><i class="fa-solid fa-save"></i> <?= lang('System.menu.save') ?></button>
                        </div>
                        <?php if ('edit' == $mode) : ?>
                            <?php $disabled = ($session->user_id == $user['id'] ? 'disabled' : ''); ?>
                            <hr class="my-3"/>
                            <h6><i class="fa-solid fa-list-check"></i> <?= lang('User.edit.grant_roles') ?></h6>
                            <h6><?= lang('User.edit.granted_roles') ?></h6>
                            <?php if (empty($user_roles)) : ?>
                                <div class="alert alert-warning" role="alert"><?= lang('User.edit.no_roles_granted') ?></div>
                            <?php else : ?>
                                <table class="table table-sm table-striped table-hover">
                                    <?php foreach ($user_roles as $role_name => $details) : ?>
                                        <tr>
                                            <td><?= $role_name ?></td>
                                            <td>
                                                <?php if ('N' == $details['is_default_role']) : ?>
                                                    <button class="btn btn-danger btn-sm btn-revoke-role" <?= $disabled ?> data-user-role-id="<?= $details['id'] ?>"><i class="fa-regular fa-trash-can"></i> <?= lang('System.menu.revoke') ?></button>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ('N' == $details['is_default_role']) : ?>
                                                    <button class="btn btn-primary btn-sm btn-make-default-role" <?= $disabled ?> data-user-role-id="<?= $details['id'] ?>" data-user-id="<?= $user['id'] ?>"><i class="fa-regular fa-star"></i> <?= lang('User.edit.make_default_role') ?></button>
                                                <?php else: ?>
                                                    <i class="fa-solid fa-star text-warning"></i> <?= lang('User.edit.default_role') ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                            <h6><?= lang('User.edit.grant_more_role') ?></h6>
                            <?php
                            $options = [];
                            foreach ($more_roles as $role) {
                                $options[$role['role_name']] = $role['role_name'];
                            }
                            generate_form_field('grant_new_role', [
                                'type'    => 'select',
                                'options' => $options,
                                'label_key' => lang('TablesRole.RoleMaster.role_name')
                            ], '');
                            ?>
                            <div class="text-end">
                                <button class="btn btn-primary btn-sm" id="btn-save-user-role"><i class="fa-solid fa-save"></i> <?= lang('System.menu.grant') ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#email_address').change(function () {
                $(this).val($(this).val().toLowerCase());
            });
            $('#user_name_first, #user_name_family, #employee_id, #employee_title').change(function () {
                $(this).val($(this).val().toUpperCase());
            });
            $('#btn-save-user-master').click(function (e) {
                e.preventDefault();
                let ids = ['email_address', 'user_name_first', 'user_name_family', 'user_gender', 'user_nationality', 'account_status', 'account_type'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('<?= lang('System.status_message.please_check_empty_field') ?>');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                let email = $('#email_address').val();
                let email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                if (!email_regex.test(email)) {
                    toastr.warning('<?= lang('System.status_message.email_address_invalid') ?>');
                    $('#email_address').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/user/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        action: 'update-user-master',
                        id: $('#id').val(),
                        email_address: $('#email_address').val(),
                        user_name_first: $('#user_name_first').val(),
                        user_name_family: $('#user_name_family').val(),
                        user_gender: $('#user_gender').val(),
                        user_nationality: $('#user_nationality').val(),
                        account_status: $('#account_status').val(),
                        account_type: $('#account_type').val(),
                        employee_id: $('#employee_id').val(),
                        employee_start_date: $('#employee_start_date').val(),
                        employee_end_date: $('#employee_end_date').val(),
                        employee_title: $('#employee_title').val()
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 5000);
                        } else {
                            let message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(message);
                            $('#btn-save-user-master').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-save-changes').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            <?php if ('edit' == $mode) : ?>
            $('#btn-save-user-role').click(function (e) {
                e.preventDefault();
                let role = $('#grant_new_role').val();
                if ('' === role) {
                    toastr.warning('<?= lang('System.status_message.please_check_empty_field') ?>');
                    $('#grant_new_role').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/user/edit') ?>',
                    type: 'post',
                    data: {
                        action: 'grant-user-role',
                        user_id: '<?= $user['id'] ?>',
                        role_name: role
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        } else {
                            let message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(message);
                            $('#btn-save-user-role').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-save-user-role').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            $('.btn-revoke-role').click(function (e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/user/edit') ?>',
                    type: 'post',
                    data: {
                        action: 'revoke-user-role',
                        user_role_id: $(this).data('user-role-id')
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        } else {
                            let message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(message);
                            $('.btn-revoke-role').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('.btn-revoke-role').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            $('.btn-make-default-role').click(function (e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/user/edit') ?>',
                    type: 'post',
                    data: {
                        action: 'make-default-user-role',
                        user_role_id: $(this).data('user-role-id'),
                        user_id: $(this).data('user-id')
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        } else {
                            let message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(message);
                            $('.btn-make-default-role').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('.btn-make-default-role').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            <?php endif; ?>
        });
    </script>
<?php $this->endSection() ?>