<?php

/**
 * *********************************************************************
 * THIS CONTROLLER IS SYSTEM CONTROLLER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Organization Controller
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Models\LogActivityModel;
use App\Models\OrganizationMasterModel;
use Exception;
use ReflectionException;

class Organization extends BaseController
{

    const ORG_ID = 1;
    const PERMISSION_REQUIRED = 'organization';

    /************************************************************************
     * Organization Page
     * GET office/organization    index():string
     * POST office/organization   update():ResponseInterface
     ************************************************************************/

    /**
     * Organization page
     * @return string
     */
    public function index(): string
    {
        if (PERMISSION_NOT_PERMITTED == retrieve_permission_for_user(self::PERMISSION_REQUIRED)) {
            return permission_denied();
        }
        // There are only ONE role in this table, always get the first record
        $model   = new OrganizationMasterModel();
        $data    = [
            'page_title'     => lang('Organization.page_title'),
            'slug'           => 'organization',
            'organization'   => $model->first(),
            'configurations' => $model->getConfigurations()
        ];
        return view('system/organization_index', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function update()
    {
        $session   = session();
        $org_model = new OrganizationMasterModel();
        $action    = $this->request->getPost('script_action');
        $log_model = new LogActivityModel();
        if ('save-data' == $action) {
            $phone_number = $this->request->getPost('organization_phone_number');
            $social_links = $this->request->getPost('organization_social_links');
            $data         = [
                'organization_name'                       => strtoupper($this->request->getPost('organization_name')),
                'organization_address_1'                  => strtoupper($this->request->getPost('organization_address_1')),
                'organization_address_2'                  => strtoupper($this->request->getPost('organization_address_2')),
                'organization_address_3'                  => strtoupper($this->request->getPost('organization_address_3')) ?? null,
                'organization_address_country_code'       => $this->request->getPost('organization_address_country_code'),
                'organization_address_postal_code'        => strtoupper($this->request->getPost('organization_address_postal_code')) ?? null,
                'organization_phone_country_calling_code' => $this->request->getPost('organization_phone_country_calling_code') ?? null,
                'organization_phone_number'               => preg_replace('/[^0-9]/', '', $phone_number) ?? null,
                'organization_email_address'              => strtolower($this->request->getPost('organization_email_address')) ?? null,
                'organization_website_url'                => strtolower($this->request->getPost('organization_website_url')) ?? null,
                'organization_social_links'               => json_encode($social_links),
                'app_name'                                => strtoupper($this->request->getPost('app_name')),
                'trade_name'                              => strtoupper($this->request->getPost('trade_name')) ?? null,
                'registration_number'                     => strtoupper($this->request->getPost('registration_number')) ?? null,
                'incorporation_date'                      => $this->request->getPost('incorporation_date'),
            ];
            if ($org_model->update(self::ORG_ID, $data)) {
                $log_model->insertTableUpdate('organization_master', self::ORG_ID, $data, $session->user_id);
                if ($data['app_name'] !== $session->app_name) {
                    $files = WRITEPATH . 'uploads/logo_' . preg_replace('/[^a-z0-9]/i', '', strtolower($session->app_name)) . '.png';
                    $session->set('app_name', $data['app_name']);
                    // remove the old logo
                    if (file_exists($files)) {
                        unlink($files);
                    }
                    $app_logo = retrieve_app_logo($session->app_name);
                    $session->set('app_logo', $app_logo);
                }
                $organization = $org_model->find(self::ORG_ID);
                $session->set('organization', $organization);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'updated',
                    'toast'   => lang('Organization.data_updated')
                ]);
            }
        } else if ('upload-logo' == $action) {
            helper(['form']);
            $validationRule = [
                'logo' => [
                    'label' => 'Logo File',
                    'rules' => [
                        'uploaded[logo]',
                        'is_image[logo]',
                        'mime_in[logo,image/jpg,image/jpeg,image/png]',
                        'max_size[logo,300]',
                        'max_dims[logo,800,200]',
                    ],
                ],
            ];
            if (! $this->validateData([], $validationRule)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'failed-validation',
                    'toast'   => lang('Organization.logo_upload_error'),
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
            // Prepare file
            try {
                $img                  = $this->request->getFile('logo');
                list($width, $height) = getimagesize($img->getPathname());
                $new_width            = $width;
                $new_height           = $height;
                if ($height > 100) {
                    $ratio      = $height / 100;
                    $new_width  = intval($width / $ratio);
                    $new_height = 100;
                }
                if ($new_width > 800) {
                    $ratio      = $new_width / 800;
                    $new_width  = 800;
                    $new_height = intval($new_height / $ratio);
                }
                $file_type            = $img->getClientMimeType();
                $file_name            = 'logo_' . preg_replace('/[^a-z0-9]/i', '', strtolower($session->organization['app_name'])) . '.png';
                if ('image/png' == $file_type) {
                    $source = imagecreatefrompng($img->getPathname());
                    $destination = imagecreatetruecolor($new_width, $new_height);
                    imagealphablending($destination, false);
                    imagesavealpha($destination, true);
                    $transparentColor = imagecolorallocatealpha($destination, 0, 0, 0, 127);
                    imagefill($destination, 0, 0, $transparentColor);
                } else {
                    $source = imagecreatefromjpeg($img->getPathname());
                    $destination = imagecreatetruecolor($new_width, $new_height);
                }
                imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($destination, WRITEPATH . 'uploads/' . $file_name, 0);
                imagedestroy($source);
                imagedestroy($destination);
                $app_logo    = retrieve_app_logo($session->organization['app_name']);
                $session->set('app_logo', $app_logo);
                $log_model->insertTableUpdate('organization_master', self::ORG_ID, ['app_logo' => $file_name], $session->user_id, 'update-logo');
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => 'logo-uploaded',
                    'toast'    => lang('Organization.logo_uploaded')
                ]);
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'generic-error',
                    'toast'   => lang('System.status_message.generic_error')
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
        } else if ('upload-favicon' == $action) {
            helper(['form']);
            $validationRule = [
                'favicon' => [
                    'label' => 'Favicon File',
                    'rules' => [
                        'uploaded[favicon]',
                        'is_image[favicon]',
                        'mime_in[favicon,image/jpg,image/jpeg,image/png]',
                        'max_size[favicon,200]',
                        'max_dims[favicon,200,200]',
                    ],
                ],
            ];
            if (! $this->validateData([], $validationRule)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'failed-validation',
                    'toast'   => lang('Organization.favicon_upload_error'),
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
            // Prepare file
            try {
                $img                  = $this->request->getFile('favicon');
                list($width, $height) = getimagesize($img->getPathname());
                $side                 = round(min($width, $height));
                $x                    = round(($width - $side) / 2);
                $y                    = round(($height - $side) / 2);
                $file_type            = $img->getClientMimeType();
                if ('image/png' == $file_type) {
                    $source = imagecreatefrompng($img->getPathname());
                } else {
                    $source = imagecreatefromjpeg($img->getPathname());
                }
                $destination = imagecreatetruecolor($side, $side);
                imagecopyresampled($destination, $source, 0, 0, $x, $y, $side, $side, $side, $side);
                imagejpeg($destination, WRITEPATH . 'uploads/favicon.jpg', 90);
                imagedestroy($source);
                imagedestroy($destination);
                $log_model->insertTableUpdate('organization_master', self::ORG_ID, ['favicon' => 'updated'], $session->user_id, 'update-favicon');
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => 'logo-uploaded',
                    'toast'    => lang('Organization.favicon_uploaded')
                ]);
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'generic-error',
                    'toast'   => lang('System.status_message.generic_error')
                ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
            }
        }
        log_message('error', 'Invalid action: ' . $action);
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'invalid-action',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }
}