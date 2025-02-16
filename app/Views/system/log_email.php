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
                        <div class="row">
                            <div class="col">
                                <label for="email_to"></label>
                                <input type="email" class="form-control form-control-sm form-filter" id="email_to" name="email_to" placeholder="email to" />
                            </div>
                            <div class="col">
                                <label for="email_subject"></label>
                                <input type="text" class="form-control form-control-sm form-filter" id="email_subject" name="email_subject" placeholder="email subject" />
                            </div>
                            <div class="col">
                                <label for="date_start"></label>
                                <input type="date" class="form-control form-control-sm form-filter" id="date_start" name="date_start" />
                            </div>
                            <div class="col">
                                <label for="date_end"></label>
                                <input type="date" class="form-control form-control-sm form-filter" id="date_end" name="date_end" />
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
                                    <th style="min-width:200px;"><?= lang('Log.email.table.created_at') ?></th>
                                    <th style="min-width:160px;"><?= lang('Log.email.table.email_to') ?></th>
                                    <th style="min-width:160px;"><?= lang('Log.email.table.email_subject') ?></th>
                                    <th style="min-width:300px;"><?= lang('Log.email.table.email_status') ?></th>
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
                searching: false,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/log/email') ?>',
                    data: function (d) {
                        let date_start = $('#date_start').val(),
                            date_end = $('#date_end').val(),
                            DateTime = luxon.DateTime;
                        if (date_start && date_end && date_end < date_start) {
                            $('#date_start').val(date_end);
                            date_start = date_end;
                        }
                        if (date_start) {
                            date_start = DateTime.fromISO(date_start + 'T00:00:00').toUTC().toISO();
                            date_start = date_start.replace('T', ' ').replace('.000Z', '');
                        }
                        if (date_end) {
                            date_end = DateTime.fromISO(date_end + 'T23:59:59').toUTC().toISO();
                            date_end = date_end.replace('T', ' ').replace('.000Z', '');
                        }
                        d.date_start = date_start;
                        d.date_end = date_end;
                        d.email_subject = $('#email_subject').val();
                        d.email_to = $('#email_to').val();
                    },
                    type: 'POST'
                },
                order: [[0, 'desc']],
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
                $('#email_to').val('');
                $('#date_start').val('');
                $('#date_end').val('');
                table.draw();
            });
        });
    </script>
<?php $this->endSection() ?>