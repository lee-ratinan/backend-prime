<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Cron Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\LogEmailModel;

class Cron extends BaseController
{

    const RETENTION_DAYS_LOG_TABLES = 180;
    const RETENTION_DAYS_CACHE_FILES = 7;
    const RETENTION_DAYS_LOG_FILES = 180;

    /**
     * MONTHLY CRON JOB
     * This cron job is designed to run on the first day of every month.
     * Purposes
     * - Delete old logs in log_activity and log_email tables
     * - Delete old files in the cache, debugbar, logs, and session directories
     * @return void
     */
    public function runMonthly(): void
    {
        log_message('info', '----------------------------------------');
        log_message('info', 'Running monthly cron job');
        // Delete old logs in log_activity and log_email tables
        $log_activity_model   = new LogActivityModel();
        $log_email_model      = new LogEmailModel();
        $deleted_log_activity = $log_activity_model->deleteOldLog('-' . self::RETENTION_DAYS_LOG_TABLES . ' days');
        $deleted_log_email    = $log_email_model->deleteOldLog('-' . self::RETENTION_DAYS_LOG_TABLES . ' days');
        log_message('info', '+ Deleted log_activity: ' . $deleted_log_activity['before'] . ' ->' . $deleted_log_activity['after'] . ' (' . $deleted_log_activity['delta'] . ' rows deleted - ' . ($deleted_log_activity['deleted']?'Successfully':'Failed') . ')');
        log_message('info', '+ Deleted log_email:    ' . $deleted_log_email['before'] .    ' ->' . $deleted_log_email['after'] .    ' (' . $deleted_log_email['delta'] .    ' rows deleted - ' . ($deleted_log_email['deleted']?'Successfully':'Failed') .    ')');
        // Delete old files in the cache, debugbar, logs, and session directories
        $directories = [
            'cache',
            'debugbar',
            'logs',
            'session'
        ];
        foreach ($directories as $directory) {
            $retention_days = ('logs' == $directory ? self::RETENTION_DAYS_LOG_FILES : self::RETENTION_DAYS_CACHE_FILES);
            $path           = WRITEPATH . $directory . '/';
            $file_deleted   = 0;
            $files          = glob($path . '*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < strtotime('-' . $retention_days . ' days')) {
                    unlink($file);
                    $file_deleted++;
                }
            }
            log_message('info', '+ Deleted ' . $file_deleted . ' files in ' . $path);
        }
        log_message('info', '+ Monthly cron job completed');
        log_message('info', '----------------------------------------');
        die();
    }
}