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
                            <?= retrieve_avatars($user['email_address'], $user['user_name_first'], $user['user_name_family']); ?>
                        </div>
                        <?php if (!empty($user['user_profile_status'])) : ?>
                            <p class="card-text"><i class="fa-regular fa-comment-dots"></i> <?= $user['user_profile_status'] ?></p>
                        <?php endif; ?>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.user_name') ?></td>
                                <td><?= $user['user_name_first'] ?> <?= $user['user_name_family'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.email_address') ?></td>
                                <td><?= $user['email_address'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.telephone_number') ?></td>
                                <td><?= $user['phone'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.user_gender') ?></td>
                                <td><?= lang('TablesUser.UserMaster.user_gender_values.' . $user['user_gender']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.user_nationality') ?></td>
                                <td><?= empty($user['user_nationality']) ? '-' : lang('ListCountries.countries.' . $user['user_nationality'] . '.common_name') ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.user_date_of_birth') ?></td>
                                <td class="utc-to-local-date"><?= $user['user_date_of_birth'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-end pe-3"><?= lang('TablesUser.UserMaster.preferred_language') ?></td>
                                <td><?= lang('TablesUser.UserMaster.preferred_language_values.' . $user['preferred_language']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let DateTime = luxon.DateTime;
            $('.utc-to-local-date').each(function () {
                const dt = $(this).text();
                console.log(dt);
                if ('' !== dt) {
                    $(this).text(DateTime.fromISO(dt).toLocaleString(DateTime.DATE_MED));
                } else {
                    $(this).text('-');
                }
            });
        });
    </script>
<?php $this->endSection() ?>