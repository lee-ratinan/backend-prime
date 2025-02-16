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
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:150px;"><?= lang('Role.role_feature.table_header') ?></th>
                                    <?php foreach ($features as $feature) : ?>
                                        <th style="min-width:120px;"><?= $feature ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($role_accesses as $role_name => $accesses) : ?>
                                    <tr>
                                        <td><?= $role_name ?></td>
                                        <?php foreach ($features as $feature_name) : ?>
                                            <td>
                                                <?php if (isset($accesses[$feature_name])) : ?>
                                                    <?php if (1 == $accesses[$feature_name]) : ?>
                                                        <span class="badge rounded-pill bg-primary"><i class="fa-solid fa-eye"></i>
                                                    <?php elseif (2 == $accesses[$feature_name]) : ?>
                                                        <span class="badge rounded-pill bg-success"><i class="fa-solid fa-pencil"></i>
                                                    <?php endif; ?>
                                                    <?= lang('TablesRole.RoleAccess.access_level_values.' . $accesses[$feature_name]) ?></span>
                                                <?php else: ?>
                                                    <span class="text-secondary"><?= lang('TablesRole.RoleAccess.access_level_values.0') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>