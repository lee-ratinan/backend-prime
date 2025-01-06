<?php
$layout = getenv('LAYOUT_FILE_PUBLIC');
$layout = (!empty($layout) ? $layout : 'system/_layout_public');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <?php $google_client_id = getenv('GOOGLE_CLIENT_ID'); ?>
    <?php $use_google_signin = (!empty($google_client_id) && 'production' == getenv('CI_ENVIRONMENT')); ?>
    <?php if ($use_google_signin) : ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
    <div class="d-flex justify-content-center px-5 py-4">
        <?= $session->app_logo ?>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" id="login-form">
                <div class="col-12 pt-4">
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.login.heading') ?></h5>
                    <p class="text-center small"><?= lang('Auth.login.subheading') ?></p>
                </div>
                <div class="col-12">
                    <div id="error-message-1"></div>
                    <?php generate_form_field('email_address', $columns['email_address']) ?>
                    <?php generate_form_field('account_password', $columns['account_password_hash']) ?>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit" id="btn-login"><?= lang('Auth.login.login_button') ?></button>
                    <!-- OPTION FOR GOOGLE SIGN IN -->
                    <?php if ($use_google_signin) : ?>
                        <script>
                            function handleCredentialResponse(response) {
                                const idToken = response.credential;
                                if (idToken) {
                                    console.log('Calling <?= base_url('google-signin') ?>');
                                    $.ajax({
                                        url: '<?= base_url('google-signin') ?>',
                                        type: 'POST',
                                        data: {id_token: idToken},
                                        success: function(response) {
                                            console.log(response);
                                            if ('success' === response.status) {
                                                window.location.href = response.url;
                                            } else {
                                                $('#error-message-1').html('<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> '+response.toast+'</div>');
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            let response = (xhr.responseJSON.toast ?? '<?= lang('Auth.login.google_signin_error') ?>');
                                            $('#error-message-1').html('<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> '+response+'</div>');
                                        }
                                    });
                                }
                            }
                        </script>
                        <div class="row py-2">
                            <div class="col-12 text-center">
                                <?= lang('Auth.login.or') ?>
                                <style>.g_id_signin {display:inline-block;width:300px;height:40px;margin:0 auto;}</style>
                                <div id="g_id_onload" data-client_id="<?= $google_client_id ?>" data-callback="handleCredentialResponse"></div>
                                <div class="text-center float-center"><div class="g_id_signin" data-type="standard" data-size="large" data-logo_alignment="left" data-text="continue_with" data-shape="pill" data-width="300" data-locale="<?= $session->locale ?>"></div></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col"><a class="btn btn-link mt-3 w-100" href="<?= base_url('forgot-password') ?>"><?= lang('Auth.forgot_password.page_title') ?></a></div>
                        <div class="col d-none"><a class="btn btn-link mt-3 w-100" href="<?= base_url('register') ?>"><?= lang('Auth.register.page_title') ?></a></div>
                    </div>
                </div>
            </form>
            <form class="row g-3" id="expired-password-form" style="display: none">
                <div class="col-12 pt-4">
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.login.expired_password.heading') ?></h5>
                    <p class="text-center small mt-3"><?= lang('Auth.login.expired_password.subheading') ?></p>
                </div>
                <div class="col-12">
                    <div id="error-message-2"></div>
                    <?php
                    generate_form_field('user_id', [
                        'type' => 'hidden'
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
                </div>
                <p class="small">
                    <?= lang('System.my_profile.para_password_requirements.title') ?><br>
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <i class="fa-solid <?= (5 < $i ? 'password-strength-circle-invert fa-circle-check text-success' : 'password-strength-circle fa-circle-xmark text-danger') ?>" id="password-requirement-item-<?= $i ?>"></i> <?= lang('System.my_profile.para_password_requirements.item_' . $i) ?><br>
                    <?php endfor; ?>
                    <label><input type="hidden" id="password-strength-count" name="password-strength-count" value="0" /></label>
                </p>
                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit" id="btn-update-password"><?= lang('Auth.login.expired_password.update_password_button') ?></button>
                </div>
            </form>
            <form class="row g-3" id="otp-form" style="display: none">
                <div class="col-12 pt-4">
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.login.otp.heading') ?></h5>
                    <p class="text-center small"><?= lang('Auth.login.otp.subheading') ?></p>
                </div>
                <div class="col-12">
                    <div id="error-message-3"></div>
                    <?php generate_form_field('otp', [
                        'type' => 'number',
                        'max'  => 999999,
                        'min'  => 0,
                        'required' => true,
                        'label_key' => 'Auth.login.otp.otp'
                    ]) ?>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit" id="btn-verify-otp"><?= lang('Auth.login.otp.verify_otp_button') ?></button>
                    <button class="btn btn-outline-primary mt-3 w-100" href="#" id="btn-resend-otp" disabled="disabled"><?= lang('Auth.login.otp.resend_otp') ?></button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let name_regex = null;
            $('#btn-resend-otp').prop('disabled', true);
            // LOGIN FLOW
            $('#btn-login').on('click', function(e) {
                e.preventDefault();
                if ('' === $('#email_address').val() || '' === $('#account_password').val()) {
                    $('#error-message-1').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('Auth.login.empty_fields') ?></div>`);
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('login') ?>',
                    type: 'POST',
                    data: {
                        email_address: $('#email_address').val(),
                        account_password: $('#account_password').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#login-form').slideUp();
                        if ('success' === response.status) {
                            $('#otp-form').slideDown();
                            $('#btn-resend-otp').prop('disabled', true);
                            let seconds = 60;
                            let interval = setInterval(function() {
                                seconds--;
                                $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?> (${seconds})`);
                                if (0 === seconds) {
                                    clearInterval(interval);
                                    $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?>`).prop('disabled', false);
                                }
                            }, 1000);
                        } else if ('expired-password' === response.status) {
                            name_regex = new RegExp(response.name_regex, 'i');
                            $('#user_id').val(response.user_id);
                            $('#expired-password-form').slideDown();
                        }
                    },
                    error: function(xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        response = response.toast ?? '<?= lang('System.status_message.generic_error') ?>';
                        $('#error-message-1').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> ${response}</div>`);
                        $('#btn-login').prop('disabled', false);
                    }
                });
            });
            $('#btn-resend-otp').on('click', function (e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('resend-otp') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        $('#btn-resend-otp').prop('disabled', true);
                        toastr.success(response.toast);
                        let seconds = 60;
                        let interval = setInterval(function() {
                            seconds--;
                            $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?> (${seconds})`);
                            if (0 === seconds) {
                                clearInterval(interval);
                                $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?>`).prop('disabled', false);
                            }
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
            // UPDATE PASSWORD FLOW
            $('#new_password')
                .on('keyup', function () {
                    // Reset
                    $('.password-strength-circle').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    $('.password-strength-circle-invert').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    // Check password
                    let password = $(this).val();
                    let strength = 0;
                    if (password.length >= 8) { // Password must be at least 8 characters long.
                        strength++; $('#password-requirement-item-1').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[0-9]/)) { // Password must contain at least one number.
                        strength++; $('#password-requirement-item-2').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[A-Z]/)) { // Password must contain at least one uppercase letter.
                        strength++; $('#password-requirement-item-3').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[a-z]/)) { // Password must contain at least one lowercase letter.
                        strength++; $('#password-requirement-item-4').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(/[@$!%*?&]/)) { // Password must contain at least one special character: @$!%*?&
                        strength++; $('#password-requirement-item-5').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    }
                    if (password.match(name_regex)) { // Password must not contain first and/or family name(s).
                        strength++; $('#password-requirement-item-6').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    } else {
                        $('#password-requirement-item-6').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    }
                    let regex_common = new RegExp('^(?:(?!<?= retrieve_common_password() ?>).)*$', 'i');
                    if (password.match(regex_common)) { // Password must not be too common.
                        strength++; $('#password-requirement-item-7').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
                    } else {
                        $('#password-requirement-item-7').removeClass('fa-circle-check text-success').addClass('fa-circle-xmark text-danger');
                    }
                    let regex_illegal = new RegExp('^[0-9A-Za-z@$!%*?&]*$', 'i');
                    if (password.match(regex_illegal)) { // Password must not contain illegal letters (letters apart from number, uppercase and lowercase Latin characters, and @$!%*?&).
                        strength++; $('#password-requirement-item-8').removeClass('fa-circle-xmark text-danger').addClass('fa-circle-check text-success');
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
            $('#btn-update-password').on('click', function(e) {
                e.preventDefault();
                if ('' === $('#new_password').val() || '' === $('#confirm_password').val()) {
                    $('#error-message-2').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('Auth.login.expired_password.empty_fields') ?></div>`);
                    return;
                }
                if ($('#new_password').val() !== $('#confirm_password').val()) {
                    $('#error-message-2').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('Auth.login.expired_password.password_not_match') ?></div>`);
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('update-expired-password') ?>',
                    type: 'POST',
                    data: {
                        user_id: $('#user_id').val(),
                        new_password: $('#new_password').val(),
                        confirm_password: $('#confirm_password').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#expired-password-form').slideUp();
                        $('#otp-form').slideDown();
                        $('#btn-resend-otp').prop('disabled', true);
                        let seconds = 60;
                        let interval = setInterval(function() {
                            seconds--;
                            $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?> (${seconds})`);
                            if (0 === seconds) {
                                clearInterval(interval);
                                $('#btn-resend-otp').html(`<?= lang('Auth.login.otp.resend_otp') ?>`).prop('disabled', false);
                            }
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        response = response.toast ?? '<?= lang('System.status_message.generic_error') ?>';
                        $('#error-message-2').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> ${response}</div>`);
                        $('#btn-update-password').prop('disabled', false);
                    }
                });
            });
            $('#btn-verify-otp').click(function(e) {
                e.preventDefault();
                if ('' === $('#otp').val()) {
                    $('#error-message-3').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('Auth.login.otp.empty_otp') ?></div>`);
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('verify-otp') ?>',
                    type: 'POST',
                    data: {
                        otp: $('#otp').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if ('success' === response.status) {
                            window.location.href = response.dashboard;
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#error-message-3').html(`<div class="alert alert-danger" role="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('Auth.login.otp.wrong_otp') ?></div>`);
                        $('#btn-verify-otp').prop('disabled', false);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>