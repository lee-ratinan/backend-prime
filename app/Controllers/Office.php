<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Office Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\RoleMasterModel;
use App\Models\UserMasterModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Office extends BaseController
{

    /************************************************************************
     * DASHBOARD
     * GET /en/office/dashboard   - index(): string
     ************************************************************************/

    /**
     * Dashboard page
     * @return string
     */
    public function index(): string
    {
        $session = session();
        $data    = [
            'page_title'   => lang('System.dashboard.page_title'),
            'slug'         => 'dashboard',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('system/office_dashboard', $data);
    }

    /************************************************************************
     * PROFILE
     * GET /en/office/profile   - profile(): string
     * POST /en/office/profile  - profileScript(): ResponseInterface
     ************************************************************************/

    /**
     * Profile page, everyone has the right to view and edit their own profile
     * @return string
     */
    public function profile(): string
    {
        $session    = session();
        $user_model = new UserMasterModel();
        $data       = [
            'page_title'   => lang('System.my_profile.page_title'),
            'slug'         => 'profile',
            'user_session' => $session->user,
            'user_config'  => $user_model->getConfigurations()
        ];
        return view('system/office_profile', $data);
    }

    /**
     * Profile script, used when the profile form is submitted
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function profileScript(): ResponseInterface
    {
        $session    = session();
        $user_model = new UserMasterModel();
        $log_model  = new LogActivityModel();
        $action     = $this->request->getPost('script_action');
        if ('save-info' === $action) {
            $new_locale   = $this->request->getPost('preferred_language');
            $phone_number = $this->request->getPost('telephone_number');
            $data         = [
                'telephone_country_calling_code' => $this->request->getPost('telephone_country_calling_code'),
                'telephone_number'               => preg_replace('/[^0-9]/', '', $phone_number),
                'user_gender'                    => $this->request->getPost('user_gender'),
                'user_date_of_birth'             => $this->request->getPost('user_date_of_birth'),
                'user_profile_status'            => htmlentities($this->request->getPost('user_profile_status')),
                'user_nationality'               => $this->request->getPost('user_nationality'),
                'preferred_language'             => $new_locale
            ];
            if ($user_model->update($session->user_id, $data)) {
                $log_model->insertTableUpdate('user_master', $session->user_id, $data, $session->user_id);
                if ($new_locale !== $session->locale) {
                    $session->set('locale', $new_locale);
                }
                $user = $user_model->find($session->user_id);
                $session->set('user', $user);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'updated',
                    'toast'   => lang('System.my_profile.saved'),
                    'redirect' => base_url($session->locale . '/office/profile')
                ]);
            }
        } else if ('change-password' === $action) {
            $current_password  = $this->request->getPost('current_password');
            $new_password      = $this->request->getPost('new_password');
            $confirm_confirm   = $this->request->getPost('confirm_password');
            $authenticate_user = $user_model->authenticateUser($session->user['email_address'], $current_password);
            if (!empty($authenticate_user)) {
                if ($new_password == $confirm_confirm) {
                    if ($user_model->updatePassword($session->user_id, $new_password)) {
                        return $this->response->setJSON([
                            'status'  => 'success',
                            'message' => 'password-changed',
                            'toast'   => lang('System.my_profile.password_changed')
                        ]);
                    }
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'generic-error',
                        'toast'   => lang('System.status_message.generic_error')
                    ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
                }
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'wrong-password',
                'toast'   => lang('System.my_profile.password_issues')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        } else if ('upload-avatar' == $action) {
            helper(['form']);
            $validationRule = [
                'avatar' => [
                    'label' => 'Avatar File',
                    'rules' => [
                        'uploaded[avatar]',
                        'is_image[avatar]',
                        'mime_in[avatar,image/jpg,image/jpeg,image/png]',
                        'max_size[avatar,200]',
                        'max_dims[avatar,1024,1024]',
                    ],
                ],
            ];
            if (! $this->validateData([], $validationRule)) {
                $errors = $this->validator->getErrors();
                $toast  = lang('System.my_profile.uploaded_avatar_validation_errors');
                foreach ($errors as $error) {
                    $toast .= '<br>- ' . $error;
                }
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'failed-validation',
                    'toast'   => $toast
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
            try {
                $img                  = $this->request->getFile('avatar');
                list($width, $height) = getimagesize($img->getPathname());
                $side                 = round(min($width, $height));
                $file_type            = $img->getClientMimeType();
                $email_address        = $session->user['email_address'];
                $file_name            = 'profile_' . preg_replace('/[^a-z0-9]/i', '', strtolower($email_address)) . '.jpg';
                if ('image/png' == $file_type) {
                    $source = imagecreatefrompng($img->getPathname());
                } else {
                    $source = imagecreatefromjpeg($img->getPathname());
                }
                $destination = imagecreatetruecolor($side, $side);
                $x           = round(($width - $side) / 2);
                $y           = round(($height - $side) / 2);
                imagecopyresampled($destination, $source, 0, 0, $x, $y, $side, $side, $side, $side);
                imagejpeg($destination, WRITEPATH . 'uploads/profile_pictures/' . $file_name, 90);
                imagedestroy($source);
                imagedestroy($destination);
                $session->set(['avatar' => retrieve_avatars($session->user['email_address'], $session->user['user_name_first'], $session->user['user_name_family'])]);
                $log_model->insertTableUpdate('user_master', $session->user_id, ['avatar' => $file_name], $session->user_id, 'update-avatar');
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => 'avatar-uploaded',
                    'toast'    => lang('System.my_profile.avatar_uploaded')
                ]);
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'generic-error',
                    'toast'   => lang('System.status_message.generic_error')
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
        } else if ('remove-avatar' == $action) {
            $email_address = $session->user['email_address'];
            $file_name     = 'profile_' . preg_replace('/[^a-z0-9]/i', '', strtolower($email_address)) . '.jpg';
            $file_path     = WRITEPATH . 'uploads/profile_pictures/' . $file_name;
            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    $session->set(['avatar' => retrieve_avatars($session->user['email_address'], $session->user['user_name_first'], $session->user['user_name_family'])]);
                    $log_model->insertTableUpdate('user_master', $session->user_id, ['avatar' => ''], $session->user_id, 'remove-avatar');
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'message' => 'avatar-removed',
                        'toast'   => lang('System.my_profile.avatar_removed')
                    ]);
                }
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'avatar-removed-failed',
                'toast'   => lang('System.my_profile.avatar_removed_failed')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-action',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /************************************************************************
     * SWITCH ROLE
     * GET /en/office/switch-role   - switchRole(): string
     * POST /en/office/switch-role  - switchRoleScript(): ResponseInterface
     ************************************************************************/

    /**
     * Switch Role page
     * @return String
     */
    public function switchRole(): String
    {
        $session = session();
        $data    = [
            'page_title'   => lang('System.switch_role.page_title'),
            'slug'         => 'switch-role',
            'user_session' => $session->user,
            'current_role' => $session->current_role,
            'roles'        => $session->roles
        ];
        return view('system/office_switch_role', $data);
    }

    /**
     * Switch Role script, used when the switch role form is submitted
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function switchRoleScript(): ResponseInterface
    {
        $session = session();
        $role    = $this->request->getPost('role');
        if (in_array($role, $session->roles))
        {
            $role_master        = new RoleMasterModel();
            $log_model          = new LogActivityModel();
            $current_role       = $session->current_role;
            $permitted_features = $role_master->retrieveAccessRightsByRole($role);
            $session->set(['current_role' => $role]);
            $session->set(['permitted_features' => $permitted_features]);
            $log_model->insertTableUpdate('session', $session->user_id, ['from' => $current_role, 'to' => $role], $session->user_id, 'switch-role');
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'switched',
                'toast'   => lang('System.switch_role.switched'),
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-role',
            'toast'   => lang('System.switch_role.failed')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }
}