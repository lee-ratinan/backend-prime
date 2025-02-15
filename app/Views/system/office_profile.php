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
                    <div class="card-body pt-3">
                        <div class="float-end">
                            <?= $session->avatar ?>
                        </div>
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <h6><i class="fa-solid fa-user-lock"></i> <?= lang('System.my_profile.controlled_account_data') ?></h6>
                        <div class="row">
                            <div class="col-12"><p><small><?= lang($user_config['email_address']['label_key']) ?></small><br><?= $user_session['email_address'] ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['user_name_first']['label_key']) ?></small><br><?= $user_session['user_name_first'] ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['user_name_family']['label_key']) ?></small><br><?= $user_session['user_name_family'] ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['employee_id']['label_key']) ?></small><br><?= $user_session['employee_id'] ?? '-' ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['employee_title']['label_key']) ?></small><br><?= $user_session['employee_title'] ?? '-' ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['employee_start_date']['label_key']) ?></small><br><?= (empty($user_session['employee_start_date']) || '0000-00-00' == $user_session['employee_start_date'] ? '-' : date(DATE_FORMAT_UI, strtotime($user_session['employee_start_date']))) ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['employee_end_date']['label_key']) ?></small><br><?= (empty($user_session['employee_end_date']) || '0000-00-00' == $user_session['employee_end_date'] ? '-' : date(DATE_FORMAT_UI, strtotime($user_session['employee_end_date']))) ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['account_status']['label_key']) ?></small><br><?= lang('TablesUser.UserMaster.account_status_values.' . $user_session['account_status']) ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['account_type']['label_key']) ?></small><br><?= lang('TablesUser.UserMaster.account_type_values.' . $user_session['account_type']) ?></p></div>
                            <div class="col-6"><p><small><?= lang($user_config['account_password_expiry']['label_key']) ?></small><br><?= date(DATE_FORMAT_UI, strtotime($user_session['account_password_expiry'])) ?></p></div>
                        </div>
                        <hr class="my-3"/>
                        <!-- PROFILE DATA -->
                        <h6><i class="fa-solid fa-user"></i> <?= lang('System.my_profile.profile_data') ?></h6>
                        <?php
                        generate_form_field('telephone_number', $user_config['telephone_number'], [$user_session['telephone_country_calling_code'], $user_session['telephone_number']]);
                        $fields = ['user_gender', 'user_nationality', 'user_date_of_birth', 'user_profile_status', 'preferred_language'];
                        foreach ($fields as $field) {
                            generate_form_field($field, $user_config[$field], $user_session[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button id="btn-save-changes" type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= lang('System.my_profile.save') ?></button>
                        </div>
                        <hr class="my-3"/>
                        <!-- UPLOAD AVATAR -->
                        <h6><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('System.my_profile.upload_avatar') ?></h6>
                        <form id="form-upload-avatar" action="<?= base_url($session->locale . '/office/profile') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="script_action" value="upload-avatar"/>
                            <input type="file" id="avatar" name="avatar" class="form-control my-3"/>
                            <p class="small"><?= lang('System.my_profile.upload_explanation') ?></p>
                            <div class="text-end">
                                <button id="btn-upload-avatar" type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('System.my_profile.upload') ?></button>
                                <button id="btn-remove-avatar" type="button" class="btn btn-outline-danger"><i class="fa-solid fa-eraser"></i> <?= lang('System.my_profile.remove_avatar') ?></button>
                                <button id="btn-remove-avatar-confirm" type="button" class="btn btn-outline-danger" style="display:none"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('System.my_profile.remove_avatar_confirm') ?></button>
                            </div>
                        </form>
                        <hr class="my-3"/>
                        <!-- CHANGE PASSWORD -->
                        <h6><i class="fa-solid fa-key"></i> <?= lang('System.my_profile.change_password') ?></h6>
                        <?php
                        generate_form_field('current_password', [
                            'type' => 'password',
                            'minlength' => 8,
                            'required' => true,
                            'label_key' => 'TablesUser.UserMaster.account_password_values.current_password'
                        ]);
                        generate_form_field('new_password', [
                            'type' => 'password',
                            'minlength' => 8,
                            'required' => true,
                            'label_key' => 'Auth.login.expired_password.new_password'
                        ]);
                        generate_form_field('confirm_password', [
                            'type' => 'password',
                            'minlength' => 8,
                            'required' => true,
                            'label_key' => 'Auth.login.expired_password.confirm_password'
                        ]);
                        ?>
                        <p class="small">
                            <?= lang('System.my_profile.para_password_requirements.title') ?><br>
                            <?php for ($i = 1; $i <= 8; $i++) : ?>
                                <i class="fa-solid <?= (5 < $i ? 'password-strength-circle-invert fa-circle-check text-success' : 'password-strength-circle fa-circle-xmark text-danger') ?>" id="password-requirement-item-<?= $i ?>"></i> <?= lang('System.my_profile.para_password_requirements.item_' . $i) ?><br>
                            <?php endfor; ?>
                            <label><input type="hidden" id="password-strength-count" name="password-strength-count" value="0" /></label>
                        </p>
                        <div class="text-end">
                            <button id="btn-change-password" type="submit" class="btn btn-primary"><i class="fa-solid fa-key"></i>  <?= lang('System.my_profile.change_password') ?></button>
                        </div>
                        <hr class="my-3"/>
                        <a class="btn btn-outline-primary btn-sm float-end" href="<?= base_url($session->locale . '/office/switch-role') ?>"><i class="fa-solid fa-arrows-rotate"></i> <?= lang('System.menu.switch_role') ?></a>
                        <h6><i class="fa-solid fa-list-check"></i> <?= lang('System.my_profile.my_roles') ?></h6>
                        <div class="row">
                            <div class="col">
                                <ul>
                                    <?php foreach ($session->roles as $role) : ?>
                                        <li><code><?= $role ?></code></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col">
                                <p><small><?= lang('System.my_profile.current_role') ?></small><br><code><?= $session->current_role ?></code></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#new_password')
                .on('keyup', function () {
                    // Reset
                    $('.password-strength-circle').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    $('.password-strength-circle-invert').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    // Check password
                    let password = $(this).val();
                    let strength = 0;
                    if (password.length >= 8) { // Password must be at least 8 characters long.
                        strength++;
                        $('#password-requirement-item-1').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[0-9]/)) { // Password must contain at least one number.
                        strength++;
                        $('#password-requirement-item-2').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[A-Z]/)) { // Password must contain at least one uppercase letter.
                        strength++;
                        $('#password-requirement-item-3').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[a-z]/)) { // Password must contain at least one lowercase letter.
                        strength++;
                        $('#password-requirement-item-4').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[@$!%*?&]/)) { // Password must contain at least one special character: @$!%*?&
                        strength++;
                        $('#password-requirement-item-5').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    let regex_name = new RegExp('^(?:(?!<?= strtolower($user_session['user_name_first']) ?>|<?= strtolower($user_session['user_name_family']) ?>).)*$', 'i');
                    if (password.match(regex_name)) { // Password must not contain first and/or family name(s).
                        strength++;
                        $('#password-requirement-item-6').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    } else {
                        $('#password-requirement-item-6').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    }
                    let regex_common = new RegExp('^(?:(?!<?= retrieve_common_password() ?>).)*$', 'i');
                    if (password.match(regex_common)) { // Password must not be too common.
                        strength++;
                        $('#password-requirement-item-7').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    } else {
                        $('#password-requirement-item-7').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    }
                    let regex_illegal = new RegExp('^[0-9A-Za-z@$!%*?&]*$', 'i');
                    if (password.match(regex_illegal)) { // Password must not contain illegal letters (letters apart from number, uppercase and lowercase Latin characters, and @$!%*?&).
                        strength++;
                        $('#password-requirement-item-8').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    } else {
                        $('#password-requirement-item-8').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    }
                    $('#password-strength-count').val(strength);
                })
                .on('change', function () {
                        if (8 > parseInt($('#password-strength-count').val())) {
                            $(this).val('').focus();
                            toastr.warning('<?= lang('System.my_profile.password_requirements') ?>');
                        }
                    }
                );
            $('#btn-save-changes').on('click', function (e) {
                e.preventDefault();
                let field_ids = ['telephone_country_calling_code', 'telephone_number', 'user_gender', 'user_date_of_birth', 'preferred_language'];
                for (let i = 0; i < field_ids.length; i++) {
                    if ($('#' + field_ids[i]).val() === '') {
                        toastr.warning('<?= lang('System.status_message.please_check_empty_field') ?>');
                        $('#' + field_ids[i]).focus();
                        return;
                    }
                }
                $('#btn-save-changes').prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/profile') ?>',
                    type: 'POST',
                    data: {
                        script_action: 'save-info',
                        telephone_country_calling_code: $('#telephone_country_calling_code').val(),
                        telephone_number: $('#telephone_number').val(),
                        user_gender: $('#user_gender').val(),
                        user_date_of_birth: $('#user_date_of_birth').val(),
                        user_profile_status: $('#user_profile_status').val(),
                        preferred_language: $('#preferred_language').val(),
                        user_nationality: $('#user_nationality').val()
                    },
                    success: function (response) {
                        $('#btn-save-changes').prop('disabled', false);
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
                        $('#btn-save-changes').prop('disabled', false);
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        toastr.error(error_message);
                    }
                });
            });
            $('#btn-upload-avatar').on('click', function (e) {
                e.preventDefault();
                // check if the file is selected
                if ($('#avatar').val() === '') {
                    toastr.warning('<?= lang('System.my_profile.please_select_avatar') ?>');
                    $('#avatar').focus();
                    return;
                }
                $('#btn-upload-avatar').prop('disabled', true);
                // submit #form-upload-avatar form in AJAX
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/profile') ?>',
                    type: 'POST',
                    data: new FormData($('#form-upload-avatar')[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#btn-upload-avatar').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = '<?= base_url($session->locale . '/office/profile') ?>';
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-upload-avatar').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            $('#btn-remove-avatar').on('click', function (e) {
                e.preventDefault();
                $('#btn-remove-avatar').hide();
                $('#btn-remove-avatar-confirm').show();
            });
            $('#btn-remove-avatar-confirm').on('click', function (e) {
                e.preventDefault();
                $('#btn-remove-avatar-confirm').prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/profile') ?>',
                    type: 'POST',
                    data: {
                        script_action: 'remove-avatar'
                    },
                    success: function (response) {
                        $('#btn-remove-avatar-confirm').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = '<?= base_url($session->locale . '/office/profile') ?>';
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-remove-avatar-confirm').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            $('#btn-change-password').on('click', function (e) {
                e.preventDefault();
                let field_ids = ['current_password', 'new_password', 'confirm_password'];
                for (let i = 0; i < field_ids.length; i++) {
                    if ($('#' + field_ids[i]).val() === '') {
                        toastr.warning('<?= lang('System.status_message.please_check_empty_field') ?>');
                        $('#' + field_ids[i]).focus();
                        return;
                    }
                }
                if ($('#new_password').val() !== $('#confirm_password').val()) {
                    toastr.warning('<?= lang('System.my_profile.password_does_not_matched') ?>');
                    $('#confirm_password').val('').focus();
                    return;
                }
                $('#btn-change-password').prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/profile') ?>',
                    type: 'POST',
                    data: {
                        script_action: 'change-password',
                        current_password: $('#current_password').val(),
                        new_password: $('#new_password').val(),
                        confirm_password: $('#confirm_password').val()
                    },
                    success: function (response) {
                        $('#btn-change-password').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            $('#current_password').val('');
                            $('#new_password').val('');
                            $('#confirm_password').val('');
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#btn-change-password').prop('disabled', false);
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>