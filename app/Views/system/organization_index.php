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
                        <!-- GENERIC DATA -->
                        <h6><i class="fa-solid fa-user-lock"></i> <?= lang('Organization.heading_1') ?></h6>
                        <?php
                        $fields_1 = [
                            'organization_name',
                            'organization_address_1',
                            'organization_address_2',
                            'organization_address_3',
                            'organization_address_country_code',
                            'organization_address_postal_code',
                            'organization_phone_number',
                            'organization_email_address',
                            'organization_website_url',
                        ];
                        $fields_2 = [
                            'app_name',
                            'trade_name',
                            'registration_number',
                            'incorporation_date',
                        ];
                        echo '<h6>' . lang('Organization.contact_information') . '</h6>';
                        foreach ($fields_1 as $field) {
                            generate_form_field($field, ('organization_phone_number' == $field ? $configurations['organization_phone'] : $configurations[$field]), ('organization_phone_number' == $field ? [$organization['organization_phone_country_calling_code'], $organization['organization_phone_number']] : $organization[$field]));
                        }
                        echo '<h6>' . lang('Organization.social_media') . '</h6>';
                        $social_links = json_decode($organization['organization_social_links'], true);
                        foreach ($configurations['organization_social_links'] as $value) {
                            generate_form_field('organization_social_links_' . $value['key'], $value, $social_links[$value['key']] ?? '');
                        }
                        echo '<h6>' . lang('Organization.other_information') . '</h6>';
                        foreach ($fields_2 as $field) {
                            generate_form_field($field, $configurations[$field], $organization[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button id="btn-save-changes" type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= lang('Organization.btn_save') ?></button>
                        </div>
                        <!-- UPLOAD LOGO -->
                        <h6><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('Organization.heading_2') ?></h6>
                        <form id="form-upload-logo" action="<?= base_url($session->locale . '/office/organization') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="script_action" value="upload-logo"/>
                            <input type="file" id="logo" name="logo" class="form-control my-3"/>
                            <p class="small"><?= lang('Organization.upload_logo_explanation') ?></p>
                            <div class="text-end">
                                <button id="btn-upload-logo" type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('Organization.btn_upload') ?></button>
                            </div>
                        </form>
                        <!-- UPLOAD FAVICON -->
                        <h6><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('Organization.heading_3') ?></h6>
                        <form id="form-upload-favicon" action="<?= base_url($session->locale . '/office/organization') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="script_action" value="upload-favicon"/>
                            <input type="file" id="favicon" name="favicon" class="form-control my-3"/>
                            <p class="small"><?= lang('Organization.upload_favicon_explanation') ?></p>
                            <div class="text-end">
                                <button id="btn-upload-favicon" type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> <?= lang('Organization.btn_upload') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-changes').on('click', function () {
                let ids = ['organization_name', 'organization_address_1', 'organization_address_2', 'organization_address_country_code', 'app_name'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('<?= lang('System.status_message.please_check_empty_field') ?>');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/organization') ?>',
                    type: 'post',
                    data: {
                        script_action: 'save-data',
                        organization_name: $('#organization_name').val(),
                        organization_address_1: $('#organization_address_1').val(),
                        organization_address_2: $('#organization_address_2').val(),
                        organization_address_3: $('#organization_address_3').val() ?? null,
                        organization_address_country_code: $('#organization_address_country_code').val(),
                        organization_address_postal_code: $('#organization_address_postal_code').val() ?? null,
                        organization_phone_country_calling_code: $('#organization_phone_country_calling_code').val() ?? null,
                        organization_phone_number: $('#organization_phone_number').val() ?? null,
                        organization_email_address: $('#organization_email_address').val() ?? null,
                        organization_website_url: $('#organization_website_url').val() ?? null,
                        organization_social_links: {
                            facebook: $('#organization_social_links_facebook').val() ?? null,
                            linkedin: $('#organization_social_links_linkedin').val() ?? null,
                            x: $('#organization_social_links_x').val() ?? null,
                            youtube: $('#organization_social_links_youtube').val() ?? null
                        },
                        app_name: $('#app_name').val(),
                        trade_name: $('#trade_name').val() ?? null,
                        registration_number: $('#registration_number').val() ?? null,
                        incorporation_date: $('#incorporation_date').val() ?? null
                    },
                    success: function (response) {
                        $('#btn-save-changes').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = '<?= base_url($session->locale . '/office/organization') ?>';
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
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
            $('#btn-upload-logo').on('click', function (e) {
                e.preventDefault();
                // check if the file is selected
                if ($('#logo').val() === '') {
                    toastr.warning('<?= lang('Organization.please_select_logo') ?>');
                    $('#logo').focus();
                    return;
                }
                $('#btn-upload-logo').prop('disabled', true);
                // submit #form-upload-logo form in AJAX
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/organization') ?>',
                    type: 'POST',
                    data: new FormData($('#form-upload-logo')[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#btn-upload-logo').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = '<?= base_url($session->locale . '/office/organization') ?>';
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-upload-logo').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
            $('#btn-upload-favicon').on('click', function (e) {
                e.preventDefault();
                // check if the file is selected
                if ($('#favicon').val() === '') {
                    toastr.warning('<?= lang('Organization.please_select_favicon') ?>');
                    $('#favicon').focus();
                    return;
                }
                $('#btn-upload-favicon').prop('disabled', true);
                // submit #form-upload-logo form in AJAX
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/organization') ?>',
                    type: 'POST',
                    data: new FormData($('#form-upload-favicon')[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#btn-upload-favicon').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location = '<?= base_url($session->locale . '/office/organization') ?>';
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        $('#btn-upload-logo').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>