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
                                <label for="activity_key"></label>
                                <select class="form-select form-select-sm form-filter" id="activity_key" name="activity_key">
                                    <option value="">== <?= lang('Log.index.table.activity') ?> ==</option>
                                    <?php foreach ($activity_keys as $key => $value): ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="table_involved"></label>
                                <input type="text" class="form-control form-control-sm form-filter" id="table_involved" name="table_involved" placeholder="table involved" />
                            </div>
                            <div class="col">
                                <label for="table_id_updated"></label>
                                <input type="number" class="form-control form-control-sm form-filter" id="table_id_updated" name="table_id_updated" placeholder="table id updated" min="0" />
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
                                    <th style="min-width:200px;"><?= lang('Log.index.table.logged_at') ?></th>
                                    <th style="min-width:150px;"><?= lang('Log.index.table.performed_by') ?></th>
                                    <th style="min-width:150px;"><?= lang('Log.index.table.activity') ?></th>
                                    <th style="min-width:150px;"><?= lang('Log.index.table.table') ?></th>
                                    <th style="min-width:80px;"><?= lang('Log.index.table.id') ?></th>
                                    <th style="min-width:200px;"><?= lang('Log.index.table.details') ?></th>
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
                    url: '<?= base_url($session->locale . '/office/log') ?>',
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
                        d.activity_key = $('#activity_key').val();
                        d.date_start = date_start;
                        d.date_end = date_end;
                        d.table_involved = $('#table_involved').val();
                        d.table_id_updated = $('#table_id_updated').val();
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
                $('#activity_key').val('');
                $('#table_involved').val('');
                $('#table_id_updated').val('');
                $('#date_start').val('');
                $('#date_end').val('');
                table.draw();
            });
        });
    </script>
<?php $this->endSection() ?>