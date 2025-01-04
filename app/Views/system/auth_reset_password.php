<?php
$layout = getenv('LAYOUT_FILE_PUBLIC');
$layout = (!empty($layout) ? $layout : 'system/_layout_public');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <div class="d-flex justify-content-center px-5 py-4">
        <?= $session->app_logo ?>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-12 pt-4">
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.reset_password.heading') ?></h5>
                    <p class="text-center small"><?= lang('Auth.reset_password.subheading') ?></p>
                </div>
                <div class="col-12">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa-solid fa-exclamation-triangle"></i><br>
                            <?php foreach ($error as $e): ?>
                                - <?= lang('Auth.reset_password.errors.' . $e) ?><br>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= base_url('forgot-password') ?>" class="btn btn-primary w-100"><?= lang('Auth.forgot_password.page_title') ?></a>
                    <?php else: ?>
                        <div id="error-message"></div>
                        <?php
                        generate_form_field('user_id', [
                            'type' => 'hidden'
                        ], $user_id);
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
                        <button type="submit" class="btn btn-primary w-100" id="btn-reset-password"><?= lang('Auth.login.expired_password.update_password_button') ?></button>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-primary w-100 mt-3"><?= lang('Auth.login.page_title') ?></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
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
                    let regex_name = new RegExp('<?= $name_regex ?>', 'i');
                    if (password.match(regex_name)) { // Password must not contain first and/or family name(s).
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
            $('#btn-reset-password').on('click', function (e) {
                e.preventDefault();
                let password = $('#new_password').val(),
                    confirm = $('#confirm_password').val();
                if (password !== confirm) {
                    $('#error-message').html('<div class="alert alert-danger" role="alert"><i class="fa-solid fa-exclamation-triangle"></i> <?= lang('Auth.login.expired_password.password_not_match') ?></div>');
                    return;
                }
                $.ajax({
                        url: '<?= base_url('reset-password') ?>',
                        type: 'POST',
                        data: {
                            user_id: $('#user_id').val(),
                            new_password: password
                        },
                        success: function (response) {
                            if ('success' === response.status) {
                                window.location.href = '<?= base_url('login') ?>';
                            } else {
                                $('#error-message').html('<div class="alert alert-danger" role="alert"><i class="fa-solid fa-exclamation-triangle"></i> ' + response.message + '</div>');
                            }
                        },
                        error: function () {
                            $('#error-message').html('<div class="alert alert-danger" role="alert"><i class="fa-solid fa-exclamation-triangle"></i> <?= lang('Auth.login.expired_password.unknown_error') ?></div>');
                        }
                    }
                );
            });
        });
    </script>
<?php $this->endSection() ?>