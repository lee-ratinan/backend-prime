<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Log Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\LogEmailModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Log extends BaseController
{

    const PERMISSION_REQUIRED = 'log';

    /************************************************************************
     * Log Page
     * GET office/log    index():string
     * POST office/log   list():ResponseInterface
     ************************************************************************/

    /**
     * Log page
     * @return string
     */
    public function index(): string
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied();
        }
        $model   = new LogActivityModel();
        $data    = [
            'page_title'    => lang('Log.index.page_title'),
            'slug'          => 'log',
            'activity_keys' => $model->getActivityKeys()
        ];
        return view('system/log_index', $data);
    }

    /**
     * Get the log
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied('datatables');
        }
        $model              = new LogActivityModel();
        $columns            = [
            'done_at',
            'done_by',
            'activity_key',
            'table_involved',
            'table_id_updated',
            'activity_detail'
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $activity_key       = $this->request->getPost('activity_key');
        $table_involved     = $this->request->getPost('table_involved');
        $table_id_updated   = intval($this->request->getPost('table_id_updated'));
        $date_start         = $this->request->getPost('date_start');
        $date_end           = $this->request->getPost('date_end');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $activity_key, $table_involved, $table_id_updated, $date_start, $date_end);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /************************************************************************
     * Email Log Page
     * GET office/log/email    email():string
     * POST office/log/email   emailList():ResponseInterface
     ************************************************************************/

    /**
     * Log (email) page
     * @return string
     */
    public function email(): string
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied();
        }
        $data    = [
            'page_title'    => lang('Log.email.page_title'),
            'slug'          => 'log-email'
        ];
        return view('system/log_email', $data);
    }

    /**
     * Get the email log
     * @return ResponseInterface
     */
    public function emailList(): ResponseInterface
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied('datatables');
        }
        $model              = new LogEmailModel();
        $columns            = [
            'created_at',
            'email_to',
            'email_subject',
            'email_status'
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $email_to           = $this->request->getPost('email_to');
        $email_subject      = $this->request->getPost('email_subject');
        $date_start         = $this->request->getPost('date_start');
        $date_end           = $this->request->getPost('date_end');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $email_to, $email_subject, $date_start, $date_end);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /************************************************************************
     * Log File Page
     * GET office/log/log-file          fileList():string
     * GET office/log/log-file/(:any)   fileView(date: string):string
     ************************************************************************/

    /**
     * Log file list page
     * @return string
     */
    public function fileList(): string
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied();
        }
        $data  = [
            'error_files'  => scandir(WRITEPATH . 'logs/'),
            'page_title'   => lang('Log.file_list.page_title'),
            'slug'         => 'log-file'
        ];
        return view('system/log_file_list', $data);
    }

    /**
     * Viet the content of the log file of the input $date
     * @param string $date
     * @return string
     */
    public function fileView(string $date): string
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied();
        }
        $file_name = 'log-' . $date . '.log';
        $file_path = WRITEPATH . 'logs/' . $file_name;
        if (!file_exists($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $file_content = file_get_contents($file_path);
        $data         = [
            'page_title'   => lang('Log.file_view.page_title', [date(DATE_FORMAT_UI, strtotime($date))]),
            'slug'         => 'log-file',
            'file_name'    => $file_name,
            'file_content' => $file_content
        ];
        return view('system/log_file_view', $data);
    }

}