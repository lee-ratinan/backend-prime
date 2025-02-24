<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Auth Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\LogEmailModel;
use App\Models\OrganizationMasterModel;
use App\Models\RoleMasterModel;
use App\Models\UserMasterModel;
use App\Models\UserRoleModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Google_Client;
use Psr\Http\Client\ClientExceptionInterface;
use Random\RandomException;
use ReflectionException;

class Auth extends BaseController
{

    const FAILED_LOGGED_IN_THRESHOLD = 3;
    const TOKEN_EXPIRY_IN_SECONDS = 300;

    /**
     * Generate OTP (ranging from 100000 to 999999), and send the email to the user
     * - When the email is sent, it's done.
     * - If the email failed to send, the user will have to try again.
     * - Although the email was successfully sent, the user will have to try again if they can't see the email anyway.
     * @return void
     * @throws ReflectionException
     * @throws ClientExceptionInterface
     * @throws RandomException
     */
    private function generateLoginOTP(): void
    {
        $session = session();
        $otp     = random_int(100000, 999999);
        $session->set(['otp' => $otp . ';' . time() + self::TOKEN_EXPIRY_IN_SECONDS]);
        // Email the OTP
        $mailgun = new LogEmailModel();
        $mailgun->sendEmail($session->user['email_address'], lang('Auth.login.otp.email.subject'), lang('Auth.login.otp.email.body', [$session->user['user_name_first'], $otp]));
    }

    /**
     * Set the organization session
     * @return void
     */
    private function setOrganizationSession(): void
    {
        $session      = session();
        $org_model    = new OrganizationMasterModel();
        $organization = $org_model->getOrganization();
        $app_logo     = retrieve_app_logo($organization['app_name']);
        $session->set(['organization' => $organization]);
        $session->set(['app_name' => $organization['app_name']]);
        $session->set(['app_logo' => $app_logo]);
    }

    /************************************************************************
     * LOGIN FLOW
     * GET /login                    - login():RedirectResponse|string - Login page
     * POST /update-expired-password - updateExpiredPassword():ResponseInterface Update expired password script
     * POST /resend-otp              - resendOTP():ResponseInterface Resend OTP script
     * POST /login                   - loginScript():ResponseInterface Login script
     * POST /verify-otp              - verifyOTP():ResponseInterface Verify OTP script
     * POST /google-signin           - loginGoogle():ResponseInterface Google Sign-in script
     ************************************************************************/

    /**
     * Login page
     * It contains login form, change password form (for expired password), and OTP form (for 2FA)
     * - Exception: if the user is already logged in, redirect to the dashboard
     * @return string|RedirectResponse
     */
    public function login(): string|RedirectResponse
    {
        $session       = session();
        $organization  = $session->organization;
        if (empty($organization)) {
            $this->setOrganizationSession();
        }
        if ($session->logged_in) {
            return redirect()->to(base_url($session->locale . '/office/dashboard'));
        }
        $userMasterModel = new UserMasterModel();
        $data            = [
            'columns'    => $userMasterModel->getConfigurations(['email_address', 'account_password_hash']),
            'page_title' => lang('Auth.login.page_title')
        ];
        return view('system/auth_login', $data);
    }

    /**
     * Update expired password script and generate OTP for the user to eventually log in
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function updateExpiredPassword(): ResponseInterface
    {
        $user_model   = new UserMasterModel();
        $new_password = $this->request->getPost('new_password');
        $user_id      = $this->request->getPost('user_id');
        if ($user_model->updatePassword($user_id, $new_password)) {
            try {
                $this->generateLoginOTP();
            } catch (ClientExceptionInterface|RandomException|ReflectionException $e) {
                // Do nothing, get the user to send again if failed.
            }
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'updated',
                'toast'   => lang('Auth.login.expired_password.password_updated')
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'unknown-error',
            'toast'   => lang('Auth.login.unknown_error'),
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * This function simply resends OTP to the user
     * @return ResponseInterface
     */
    public function resendOTP(): ResponseInterface
    {
        try {
            $this->generateLoginOTP();
        } catch (ClientExceptionInterface|RandomException|ReflectionException $e) {
            // Do nothing, get the user to send again if failed.
        }
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'resent',
            'toast'   => lang('Auth.login.otp.resend_success')
        ]);
    }

    /**
     * Login script
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function loginScript(): ResponseInterface
    {
        $session   = session();
        $email     = $this->request->getPost('email_address');
        $password  = $this->request->getPost('account_password');
        $model     = new UserMasterModel();
        $log_model = new LogActivityModel();
        $user      = $model->authenticateUser($email, $password);
        if (empty($user)) {
            if (!isset($session->failed_logged_in)) {
                $session->set(['failed_logged_in' => 1]);
            } else {
                $session->set(['failed_logged_in' => $session->failed_logged_in + 1]);
            }
            log_message('warning', 'A person with ' . $email . ' has attempted to login but failed. [' . $session->failed_logged_in . ']');
            if (self::FAILED_LOGGED_IN_THRESHOLD == $session->failed_logged_in) {
                $model->blockUser($email);
                $session->set(['failed_logged_in' => 0]);
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'wrong-pw-3-times-hence-blocked',
                    'toast'   => lang('Auth.login.wrong_pw_3_times')
                ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
            }
            $log_model->insertLogin(0, 'failed');
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'invalid-password',
                'toast'   => lang('Auth.login.invalid_password'),
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        } else if ('B' == $user['account_status']) {
            $log_model->insertLogin($user['id'], 'failed');
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'blocked',
                'toast'   => lang('Auth.login.blocked_account')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        } else if ('T' == $user['account_status']) {
            $log_model->insertLogin($user['id'], 'failed');
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'terminated',
                'toast'   => lang('Auth.login.terminated_account')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        } else if ('P' == $user['account_status']) {
            $log_model->insertLogin($user['id'], 'failed');
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'pending',
                'toast'   => lang('Auth.login.pending_account')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        }
        $today                = date('Y-m-d');
        $expiry               = $user['account_password_expiry'];
        $session->set(['user' => $user]);
        $session->set(['user_id' => $user['id']]);
        $session->set(['display_name' => $user['user_name_first'] . ' ' . substr($user['user_name_family'], 0, 1) . '.']);
        $session->set(['avatar' => retrieve_avatars($user['email_address'], $user['user_name_first'], $user['user_name_family'])]);
        $session->set(['locale' => $user['preferred_language']]);
        // Assign roles first
        $role_model = new UserRoleModel();
        $all_roles  = $role_model->getRolesByUser($user['id']);
        if (empty($all_roles)) {
            // can't login, no roles assigned
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'no-roles',
                'toast'   => lang('Auth.login.no_roles')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        }
        $roles      = [];
        $role_now   = '';
        foreach ($all_roles as $role) {
            $roles[] = $role['role_name'];
            if ('Y' == $role['is_default_role']) {
                $role_now = $role['role_name'];
            }
        }
        if (empty($role_now)) {
            $role_now = $roles[0];
        }
        $session->set([
            'roles'        => $roles,
            'current_role' => $role_now
        ]);
        // Check expiry
        if ($today > $expiry) {
            return $this->response->setJSON([
                'status'     => 'expired-password',
                'user_id'    => $user['id'],
                'message'    => 'expired-password',
                'name_regex' => '^(?:(?!' . strtolower($user['user_name_first']) . '|' . strtolower($user['user_name_family']) . ').)*$',
                'toast'      => lang('Auth.login.expired_password.subheading')
            ]);
        }
        try {
            $this->generateLoginOTP();
        } catch (ReflectionException|RandomException|ClientExceptionInterface $e) {
            // Do nothing, get the user to send again if failed.
        }
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'successfully-logged-in-now-otp',
            'toast'   => lang('Auth.login.otp.heading')
        ]);
    }

    /**
     * Verify OTP and set the session to logged in
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function verifyOTP(): ResponseInterface
    {
        $session     = session();
        $otp         = $this->request->getPost('otp');
        $now         = time();
        $session_otp = explode(';', $session->otp);
        $saved_otp   = $session_otp[0];
        $expiry      = $session_otp[1];
        if ($otp == $saved_otp && $now < $expiry) {
            $session->remove('otp');
            $role_master        = new RoleMasterModel();
            $permitted_features = $role_master->retrieveAccessRightsByRole($session->current_role);
            $session->set(['logged_in' => true]);
            $session->set(['permitted_features' => $permitted_features]);
            $log_model = new LogActivityModel();
            $log_model->insertLogin($session->user_id, 'success', $session->current_role);
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'verified',
                'toast'     => lang('Auth.login.otp.verified'),
                'dashboard' => base_url($session->locale . '/office/dashboard')
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-otp',
            'toast'   => lang('Auth.login.otp.wrong_otp')
        ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
    }

    /**
     * Google Sign-in callback function
     * @return ResponseInterface
     */
    public function loginGoogle(): ResponseInterface
    {
        $id_token         = $this->request->getPost('id_token');
        $google_client_id = getenv('GOOGLE_CLIENT_ID');
        try {
            $client       = new Google_Client(['client_id' => $google_client_id]);
            $payload      = $client->verifyIdToken($id_token);
            if ($payload) {
                log_message('debug', 'Google Sign-in: ' . json_encode($payload));
                if (isset($payload['error'])) {
                    throw new Exception($payload['error']);
                } else {
                    $session           = session();
                    $user_master_model = new UserMasterModel();
                    log_message('debug', 'Retrieving user email from user_master: ' . $payload['email']);
                    $user              = $user_master_model->where('account_status', $user_master_model::ACCOUNT_STATUS_ACTIVE)->where('email_address', $payload['email'])->first();
                    if (!empty($user)) {
                        $user_role_model   = new UserRoleModel();
                        $all_roles         = $user_role_model->getRolesByUser($user['id']);
                        if (!empty($all_roles)) {
                            $role_master_model = new RoleMasterModel();
                            $log_model         = new LogActivityModel();
                            $session->set(['logged_in' => true]);
                            $session->set(['user' => $user]);
                            $session->set(['user_id' => $user['id']]);
                            $session->set(['display_name' => $user['user_name_first'] . ' ' . substr($user['user_name_family'], 0, 1) . '.']);
                            $session->set(['avatar' => retrieve_avatars($user['email_address'], $user['user_name_first'], $user['user_name_family'])]);
                            $session->set(['locale' => $user['preferred_language']]);
                            $roles      = [];
                            $role_now   = '';
                            foreach ($all_roles as $role) {
                                $roles[] = $role['role_name'];
                                if ('Y' == $role['is_default_role']) {
                                    $role_now = $role['role_name'];
                                }
                            }
                            if (empty($role_now)) {
                                $role_now = $roles[0];
                            }
                            $session->set([
                                'roles'        => $roles,
                                'current_role' => $role_now
                            ]);
                            $permitted_features = $role_master_model->retrieveAccessRightsByRole($session->current_role);
                            $session->set(['permitted_features' => $permitted_features]);
                            $log_model->insertLogin($session->user_id, 'success', $session->current_role);
                            return $this->response->setJSON([
                                'status'  => 'success',
                                'message' => 'success',
                                'toast'   => lang('Auth.login.success'),
                                'url'     => base_url($session->locale . '/office/dashboard')
                            ]);
                        }
                        throw new Exception('Roles Not Found');
                    }
                    throw new Exception('User Not Found or Not Active');
                }
            } else {
                throw new Exception('Invalid ID Token');
            }
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'invalid-id-token',
                'toast'   => lang('Auth.login.google_error') . ' [' . $e->getMessage() . ']'
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        }
    }

    /************************************************************************
     * FORGOT PASSWORD FLOW
     * GET /forgot-password              - forgotPassword():string - Forgot password page
     * POST /forgot-password             - forgotPasswordScript():ResponseInterface - Forgot password script
     * GET /reset-password/(:any)/(:any) - resetPassword($token, $clean_email):RedirectResponse|string - Reset password page
     * POST /reset-password              - resetPasswordScript():ResponseInterface - Reset password script
     ************************************************************************/

    /**
     * Forgot password page
     * @return string
     */
    public function forgotPassword(): string
    {
        $session = session();
        if (empty($session->organization)) {
            $this->setOrganizationSession();
        }
        $data = [
            'page_title' => lang('Auth.forgot_password.page_title')
        ];
        return view('system/auth_forgot_password', $data);
    }

    /**
     * Forgot password script
     * @return ResponseInterface
     */
    public function forgotPasswordScript(): ResponseInterface
    {
        $session = session();
        $email   = $this->request->getPost('email_address');
        $model   = new UserMasterModel();
        $user    = $model->where('email_address', $email)->first();
        if (empty($user)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'invalid-email',
                'toast'   => lang('Auth.forgot_password.error')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        }
        $mailgun = new LogEmailModel();
        try {
            // remove anything that is not alphanumeric in $email
            $clean_email  = preg_replace('/[^a-zA-Z0-9]/', '', $email);
            $token        = hash('sha256', $clean_email . date('YmdHisisis'));
            $token_expiry = time() + self::TOKEN_EXPIRY_IN_SECONDS;
            $session->set(['password_reset_token' => $email . ';' . $token . ';' . $token_expiry]);
            $reset_link   = base_url('reset-password/' . $token . '/' . $clean_email);
            $mailgun->sendEmail($email, 'Password Reset', "Please click on the link to reset your password:\n{$reset_link}");
        } catch (ClientExceptionInterface|ReflectionException $e) {
            // If email failed to send, do nothing, let the user try again
        }
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'email-sent',
            'toast'   => lang('Auth.forgot_password.done')
        ]);
    }

    /**
     * Reset password page
     * @param string $token
     * @param string $clean_email
     * @return RedirectResponse|string
     */
    public function resetPassword(string $token, string $clean_email): RedirectResponse|string
    {
        $session        = session();
        if (empty($session->password_reset_token)) {
            return redirect()->to(base_url('forgot-password'));
        }
        if (empty($session->organization)) {
            $this->setOrganizationSession();
        }
        $session_data   = explode(';', $session->password_reset_token);
        $session_email  = @$session_data[0];
        $session_token  = @$session_data[1];
        $session_expiry = @$session_data[2];
        $session_cem    = preg_replace('/[^a-zA-Z0-9]/', '', $session_email);
        $error          = [];
        if ($clean_email != $session_cem) {
            $error[]    = 'invalid_email';
        }
        if ($token != $session_token) {
            $error[]    = 'invalid_token';
        }
        if (time() > $session_expiry) {
            $error[]    = 'expired_token';
        }
        $user_id = 0;
        if (empty($error)) {
            $model = new UserMasterModel();
            $user  = $model->where('email_address', $session_email)->first();
            if (!empty($user)) {
                $user_id = $user['id'];
            }
        }
        if (0 == $user_id) {
            $error[] = 'invalid_email';
        }
        $session->remove('password_reset_token');
        $session->set(['user_id' => $user_id]);
        $data = [
            'page_title' => lang('Auth.reset_password.page_title'),
            'error'      => $error,
            'user_id'    => $user_id,
            'name_regex' => '^(?:(?!' . strtolower(@$user['user_name_first']) . '|' . strtolower(@$user['user_name_family']) . ').)*$',
        ];
        return view('system/auth_reset_password', $data);
    }

    /**
     * Reset password script
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function resetPasswordScript(): ResponseInterface
    {
        $session      = session();
        $user_id      = $this->request->getPost('user_id');
        $new_password = $this->request->getPost('new_password');
        $session_id   = $session->get('user_id');
        if ($user_id != $session_id) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'invalid-user',
                'toast'   => lang('Auth.reset_password.error_token')
            ])->setStatusCode(HTTP_STATUS_UNAUTHORIZED);
        }
        $model        = new UserMasterModel();
        if (!$model->updatePassword($user_id, $new_password)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'failed',
                'toast'   => lang('Auth.reset_password.error_update')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Password reset successful',
            'toast'   => lang('Auth.forgot_password.password_updated')
        ]);
    }

    /************************************************************************
     * REGISTER FLOW - Future use
     * GET /register - register():string - Register page
     * POST /register - registerScript():ResponseInterface - Register script
     ************************************************************************/

    /**
     * Register (create new account) page
     * @return string
     */
    public function register(): string
    {
        $session = session();
        if (empty($session->organization)) {
            $this->setOrganizationSession();
        }
        // Reserved for future use, not allowed the registration yet
        $data = [
            'page_title' => lang('Auth.register.page_title')
        ];
        return view('system/auth_register', $data);
    }

    /**
     * Register (create new account) script
     * @return ResponseInterface
     */
    public function registerScript(): ResponseInterface
    {
        // Reserved for future use
        return $this->response->setJSON([
            'status'  => '',
            'message' => '',
            'toast'   => ''
        ]);
    }

    /************************************************************************
     * LOGOUT FLOW
     * GET /logout - logout():RedirectResponse - Logout script
     ************************************************************************/

    /**
     * Logout script
     * Destroy the session and redirect to the login page
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }
}