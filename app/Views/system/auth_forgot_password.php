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
                    <h5 class="card-title text-center pb-0 fs-4"><?= lang('Auth.forgot_password.heading') ?></h5>
                    <p class="text-center small"><?= lang('Auth.forgot_password.subheading') ?></p>
                </div>
                <div class="col-12">
                    <div id="error-message"></div>
                    <?php generate_form_field('email_address', [
                        'label_key' => 'TablesUser.UserMaster.email_address',
                        'type'      => 'email'
                    ]) ?>
                </div>
                <div class="col-12 text-center">
                    <button class="btn btn-primary w-100" type="submit" id="btn-forgot-password"><?= lang('Auth.forgot_password.reset_button') ?></button>
                    <a class="btn btn-link w-100 mt-3" href="<?= base_url('login') ?>"><?= lang('Auth.forgot_password.back_button') ?></a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-forgot-password').on('click', function(e) {
                e.preventDefault();
                let email_address = $('#email_address').val();
                $.ajax({
                    url: '<?= base_url('forgot-password') ?>',
                    type: 'POST',
                    data: {email_address: email_address},
                    success: function(response) {
                        console.log(response);
                        if ('error' === response.status) {
                            $('#error-message').html('<div class="alert alert-danger" role="alert">' + response.toast + '</div>');
                        } else {
                            $('#error-message').html('<div class="alert alert-success" role="alert">' + response.toast + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').html('<div class="alert alert-danger" role="alert">' + response.toast + '</div>');
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>