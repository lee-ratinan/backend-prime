<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Role Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\RoleAccessModel;
use App\Models\RoleMasterModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Role extends BaseController
{

    const PERMISSION_REQUIRED = 'role_master';

    /************************************************************************
     * Role Pages
     * GET office/role             index():string
     * POST office/role            list():ResponseInterface
     * GET office/role/create      edit('new-role'):string
     * GET office/role/edit/(:any) edit($role_name):string
     * POST office/role/edit       editScript():ResponseInterface
     * GET office/role/feature     feature():string
     ************************************************************************/

    /**
     * Role page
     * @return string
     */
    public function index(): string
    {
        $permission_level = retrieve_permission_for_user(self::PERMISSION_REQUIRED);
        if (PERMISSION_NOT_PERMITTED == $permission_level) {
            return permission_denied();
        }
        $session = session();
        $data    = [
            'page_title'       => lang('Role.index.page_title'),
            'slug'             => 'role',
            'user_session'     => $session->user,
            'roles'            => $session->roles,
            'current_role'     => $session->current_role,
            'permission_level' => $permission_level
        ];
        return view('system/role_index', $data);
    }

    /**
     * List all roles
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied('datatables');
        }
        $model              = new RoleMasterModel();
        $columns            = [
            '',
            'id',
            'role_name',
            'role_description',
            'created_by',
            'created_at',
            'updated_at'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * Edit role
     * When the $role_name is 'new-role', it's the create new role page
     * @param $role_name
     * @return string
     */
    public function edit($role_name): string
    {
        $permission_level = retrieve_permission_for_user(self::PERMISSION_REQUIRED);
        if (PERMISSION_NOT_PERMITTED == $permission_level) {
            return permission_denied();
        }
        $role_master_model = new RoleMasterModel();
        $role_access_model = new RoleAccessModel();
        if ('new-role' == $role_name) {
            if (PERMISSION_READ_ONLY == $permission_level) {
                return permission_denied();
            }
            $mode          = 'new';
            $role_accesses = [];
        } else {
            $mode          = 'edit';
            $role_accesses = $role_access_model->getAccessByRoleName($role_name);
            if (empty($role_accesses)) {
                throw PageNotFoundException::forPageNotFound();
            }
        }
        $data = [
            'page_title'         => ($mode == 'edit' ? lang('Role.edit.page_title', [$role_accesses['role_master']['role_name']]) : lang('Role.new.page_title')),
            'slug'               => 'role',
            'permission_level'   => $permission_level,
            'mode'               => $mode,
            'feature_master'     => retrieve_feature_master(),
            'role_accesses'      => $role_accesses,
            'role_master_config' => $role_master_model->getConfigurations(),
            'role_access_config' => $role_access_model->getConfigurations()
        ];
        return view('system/role_edit', $data);
    }

    /**
     * Insert or update role_master/role_access
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function editScript(): ResponseInterface
    {
        if (PERMISSION_EDITABLE != retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied('json');
        }
        $session           = session();
        $action            = $this->request->getPost('action');
        $role_master_model = new RoleMasterModel();
        $role_access_model = new RoleAccessModel();
        $log_model         = new LogActivityModel();
        if ('save-role-master' == $action) {
            $id               = $this->request->getPost('id') ?? 0;
            $role_name        = $this->request->getPost('role_name');
            $role_description = $this->request->getPost('role_description');
            if (0 < $id) {
                // Edit, role_name can't be changed
                $data = [
                    'role_description' => htmlentities($role_description)
                ];
                if ($role_master_model->update($id, $data)) {
                    $log_model->insertTableUpdate('role_master', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => lang('Role.edit.edit_success'),
                        'redirect' => base_url($session->locale . '/office/role/edit/' . $role_name)
                    ]);
                }
            } else {
                // Insert, role_name must be cleaned, redirect to edit page
                $role_name = strtolower(trim($role_name));
                $role_name = str_replace(['_', ' '], '-', $role_name);
                $data      = [
                    'role_name'        => strtolower($role_name),
                    'role_description' => htmlentities($role_description),
                    'created_by'       => $session->user_id,
                ];
                try {
                    if ($id = $role_master_model->insert($data)) {
                        $log_model->insertTableUpdate('role_master', $id, $data, $session->user_id);
                        return $this->response->setJSON([
                            'status'   => 'success',
                            'toast'    => lang('Role.edit.insert_success'),
                            'redirect' => base_url($session->locale . '/office/role/edit/' . $role_name)
                        ]);
                    }
                } catch (DatabaseException|Exception $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'duplicate-role-name',
                            'toast'   => lang('Role.edit.something_wrong_could_be_duplicate')
                        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
                    }
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'duplicate-role-name',
                        'toast'   => lang('System.status_message.generic_error') . ' (' . $e->getMessage() . ')'
                    ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
                }
            }
            // Something went wrong
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'failed-update-role-master',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        } else if ('save-role-access' == $action) {
            $feature      = $this->request->getPost('feature');
            $role_id      = $this->request->getPost('role_id');
            $access_level = $this->request->getPost('access_level');
            $access_row   = $role_access_model->where('role_id', $role_id)->where('access_feature', $feature)->first();
            if (empty($access_row)) {
                // Record is not found
                if (PERMISSION_NOT_PERMITTED != $access_level) {
                    // Record must be created
                    $data = [
                        'role_id'        => $role_id,
                        'access_feature' => $feature,
                        'access_level'   => $access_level,
                        'created_by'     => $session->user_id,
                        'created_at'     => date(DATETIME_FORMAT_DB),
                        'updated_at'     => date(DATETIME_FORMAT_DB)
                    ];
                    if ($id = $role_access_model->insert($data)) {
                        $log_model->insertTableUpdate('role_access', $id, $data, $session->user_id);
                        return $this->response->setJSON([
                            'status'   => 'success',
                            'toast'    => lang('Role.edit.update_access_success')
                        ]);
                    }
                    // Error - fall back to error response below
                } else {
                    // It's ok, do nothing
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => lang('Role.edit.update_access_success')
                    ]);
                }
            } else {
                // Found the record
                if (PERMISSION_NOT_PERMITTED == $access_level) {
                    // Does not need anymore
                    if ($role_access_model->delete($access_row['id'])) {
                        $log_model->insertTableUpdate('role_access', $access_row['id'], [], $session->user_id, 'delete-table');
                        return $this->response->setJSON([
                            'status'   => 'success',
                            'toast'    => lang('Role.edit.update_access_success')
                        ]);
                    }
                } else {
                    // Update the record
                    $data = [
                        'access_level' => $access_level
                    ];
                    if ($role_access_model->update($access_row['id'], $data)) {
                        $log_model->insertTableUpdate('role_access', $access_row['id'], $data, $session->user_id);
                        return $this->response->setJSON([
                            'status' => 'success',
                            'toast'    => lang('Role.edit.update_access_success')
                        ]);
                    }
                }
            }
            // Generic error response
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'save-error',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-action',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * Show the list of features and the roles that have access to them
     * @return string
     */
    public function feature(): string
    {
        $permission_level = retrieve_permission_for_user(self::PERMISSION_REQUIRED);
        if (PERMISSION_NOT_PERMITTED == $permission_level) {
            return permission_denied();
        }
        $role_access_model = new RoleAccessModel();
        $data              = [
            'page_title'    => lang('Role.role_feature.page_title'),
            'slug'          => 'role',
            'role_accesses' => $role_access_model->getAllRoles(),
            'features'      => retrieve_feature_master(),
        ];
        return view('system/role_feature', $data);
    }
}