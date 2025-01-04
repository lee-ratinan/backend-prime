<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * User Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\LogEmailModel;
use App\Models\RoleMasterModel;
use App\Models\UserMasterModel;
use App\Models\UserRoleModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Psr\Http\Client\ClientExceptionInterface;
use ReflectionException;

class User extends BaseController
{

    const PERMISSION_REQUIRED = 'user_master';

    /************************************************************************
     * User Pages
     * GET office/user                        index():string
     * POST office/user                       list():ResponseInterface
     * GET office/user/create                 edit('new'):string
     * GET office/user/edit/(:any)            edit($1):string
     * POST office/user/edit                  editScript():ResponseInterface
     * GET office/user/public-profile/(:any)  publicProfile($1):string
     ************************************************************************/

    /**
     * User page
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
            'page_title'       => lang('User.index.page_title'),
            'slug'             => 'user',
            'permission_level' => $permission_level,
            'user_session'     => $session->user,
            'roles'            => $session->roles,
            'current_role'     => $session->current_role
        ];
        return view('system/user_index', $data);
    }

    /**
     * Retrieve the list of users for the DataTables
     * @return ResponseInterface
     */
    public function list():ResponseInterface
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied('datatables');
        }
        $model              = new UserMasterModel();
        $columns            = [
            '',
            'id',
            'email_address',
            'telephone_number',
            'user_name_family',
            'account_type',
            'account_status',
            'user_created_by',
            'user_created_at',
            'user_updated_at'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $account_type       = $this->request->getPost('account_type');
        $account_status     = $this->request->getPost('account_status');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $account_type, $account_status);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * User's profile page
     * @param string $encoded_email
     * @return string
     */
    public function edit(string $encoded_email): string
    {
        $permission_level = retrieve_permission_for_user(self::PERMISSION_REQUIRED);
        if (PERMISSION_EDITABLE != $permission_level) {
            return permission_denied();
        }
        $user_model = new UserMasterModel();
        $mode       = 'new';
        $user       = [];
        $more_roles = [];
        $user_roles = [];
        $page_title = lang('User.edit.page_title_new');
        if ('new' != $encoded_email) {
            $mode          = 'edit';
            $email_address = decode_caesar_cipher($encoded_email);
            $user          = $user_model->where('email_address', $email_address)->first();
            if (empty($user)) {
                throw PageNotFoundException::forPageNotFound();
            }
            $page_title        = lang('User.edit.page_title_edit', [$user['user_name_first'] . ' ' . $user['user_name_family']]);
            $role_master_model = new RoleMasterModel();
            $user_role_model   = new UserRoleModel();
            $more_roles        = $role_master_model->getUnassignedRoleForUser($user['id']);
            $granted           = $user_role_model->getRolesByUser($user['id']);
            foreach ($granted as $role) {
                $user_roles[$role['role_name']] = [
                    'id'  => $role['id'],
                    'is_default_role' => $role['is_default_role']
                ];
            }
            ksort($user_roles);
        }
        $data = [
            'page_title'         => $page_title,
            'slug'               => 'user',
            'permission_level'   => $permission_level,
            'user_configuration' => $user_model->getConfigurations(),
            'mode'               => $mode,
            'user'               => $user,
            'more_roles'         => $more_roles,
            'user_roles'         => $user_roles
        ];
        return view('system/user_edit', $data);
    }

    /**
     * Edit user script
     * @return ResponseInterface
     * @throws ReflectionException|ClientExceptionInterface
     */
    public function editScript(): ResponseInterface
    {
        $permission_level = retrieve_permission_for_user(self::PERMISSION_REQUIRED);
        if (PERMISSION_EDITABLE != $permission_level) {
            return permission_denied('json');
        }
        $action            = $this->request->getPost('action');
        $user_master_model = new UserMasterModel();
        $user_role_model   = new UserRoleModel();
        $log_model         = new LogActivityModel();
        $session           = session();
        if ('update-user-master' == $action) {
            $id            = $this->request->getPost('id');
            $mode          = $this->request->getPost('mode');
            $email         = strtolower(trim($this->request->getPost('email_address')));
            $encoded_email = encode_caesar_cipher($email);
            $data          = [
                'email_address'       => $email,
                'user_name_first'     => strtoupper(trim($this->request->getPost('user_name_first'))),
                'user_name_family'    => strtoupper(trim($this->request->getPost('user_name_family'))),
                'user_gender'         => $this->request->getPost('user_gender'),
                'user_nationality'    => $this->request->getPost('user_nationality'),
                'account_status'      => $this->request->getPost('account_status'),
                'account_type'        => $this->request->getPost('account_type'),
                'employee_id'         => trim($this->request->getPost('employee_id')) ?? null,
                'employee_start_date' => $this->request->getPost('employee_start_date') ?? null,
                'employee_end_date'   => $this->request->getPost('employee_end_date') ?? null,
                'employee_title'      => trim($this->request->getPost('employee_title')) ?? null
            ];
            if ('new' == $mode) {
                $data['user_created_by']         = $session->user_id;
                // CREATE PASSWORD
                $passwords                       = $user_master_model->generateRandomPassword();
                $data['account_password_hash']   = $passwords['hashed'];
                $data['account_password_expiry'] = date(DATE_FORMAT_DB, strtotime('-1 day'));
                // EMAIL
                $mailgun = new LogEmailModel();
                $mailgun->sendEmail($email, lang('User.edit.email.subject', [$session->app_name]), lang('User.edit.email.body', [$data['user_name_first'], $email, $passwords['plain']]));
                // INSERT
                if ($id = $user_master_model->insert($data)) {
                    $log_model->insertTableUpdate('user_master', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'message' => 'insert-user-master',
                        'toast'   => lang('User.edit.successful_create'),
                        'redirect' => base_url($session->locale . '/office/user/edit/' . $encoded_email)
                    ]);
                }
            } else {
                if ($user_master_model->update($id, $data)) {
                    $log_model->insertTableUpdate('user_master', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'message' => 'update-user-master',
                        'toast'   => lang('User.edit.successful_update'),
                        'redirect' => base_url($session->locale . '/office/user/edit/' . $encoded_email)
                    ]);
                }
            }
        } else if ('grant-user-role' == $action) {
            $user_id   = $this->request->getPost('user_id');
            $role_name = $this->request->getPost('role_name');
            $data      = [
                'user_id'         => $user_id,
                'role_name'       => $role_name,
                'is_default_role' => 'N',
                'role_created_by' => $session->user_id
            ];
            if ($id = $user_role_model->insert($data)) {
                $log_model->insertTableUpdate('user_role', $id, $data, $session->user_id);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'grant-user-role',
                    'toast'   => lang('User.edit.role_granted')
                ]);
            }
        } else if ('revoke-user-role' == $action) {
            $id = $this->request->getPost('user_role_id');
            if ($user_role_model->delete($id)) {
                $log_model->insertTableUpdate('user_role', $id, [], $session->user_id, 'delete-table');
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'revoke-user-role',
                    'toast'   => lang('System.status_message.generic_success')
                ]);
            }
        } else if ('make-default-user-role' == $action) {
            $user_id      = $this->request->getPost('user_id');
            $user_role_id = $this->request->getPost('user_role_id');
            if ($user_role_model->makeDefaultRole($user_id, $user_role_id)) {
                $log_model->insertTableUpdate('user_role', $user_role_id, ['is_default_role' => 'Y'], $session->user_id);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'make-default-user-role',
                    'toast'   => lang('User.edit.default_role_set')
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-action',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * User's public profile page, any user can see this page
     * @param string $slug
     * @return string
     */
    public function publicProfile(string $slug): string
    {
        $email_address = decode_caesar_cipher($slug);
        $model         = new UserMasterModel();
        $user          = $model->where('email_address', $email_address)->first();
        if (empty($user)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $phone_util    = PhoneNumberUtil::getInstance();
        $user['phone'] = '-';
        if (!empty($user['telephone_country_calling_code']) && !empty($user['telephone_number'])) {
            try {
                $phone_obj = $phone_util->parse($user['telephone_country_calling_code'] . $user['telephone_number'], null);
                $user['phone'] = $phone_util->format($phone_obj, PhoneNumberFormat::INTERNATIONAL);
            } catch (NumberParseException $e) {
                $user['phone'] = $user['telephone_country_calling_code'] . ' ' . $user['telephone_number'];
                log_message('error', 'Error parsing phone number: ' . $e->getMessage());
            }
        }
        $data = [
            'page_title' => $user['user_name_first'] . ' ' . $user['user_name_family'],
            'slug'       => 'public-profile',
            'user'       => $user
        ];
        return view('system/user_public_profile', $data);
    }
}