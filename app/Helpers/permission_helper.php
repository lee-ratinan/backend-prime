<?php

/**
 * *********************************************************************
 * THIS FILE IS SYSTEM HELPER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 */

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Retrieve the list of features to be used in the role management
 * @return string[]
 */
function retrieve_feature_master(): array
{
    return [
        'log',
        'organization',
        'role_master',
        'user_master',
    ];
}

/**
 * Retrieve the permission level of the feature for the user
 * @param string $feature
 * @return string
 */
function retrieve_permission_for_user(string $feature): string
{
    $session            = session();
    $permitted_features = $session->get('permitted_features');
    if (empty($permitted_features[$feature])) {
        return PERMISSION_NOT_PERMITTED;
    }
    return (string) $permitted_features[$feature];
}

/**
 * Show the permission denied page with status code 403
 * @param string $type
 * @return string|ResponseInterface
 */
function permission_denied(string $type = 'page'): string|ResponseInterface
{
    $response = service('response');
    if ('json' == $type) {
        $response->setStatusCode(403);
        return $response->setJSON([
            'status'  => 403,
            'message' => lang('System.permission_denied.message'),
            'toast'   => lang('System.permission_denied.message'),
        ]);
    } else if ('datatables' == $type) {
        $request = service('request');
        return $response->setJSON([
            'draw'            => $request->getPost('draw'),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
            'error'           => lang('System.permission_denied.message')
        ]);
    }
    $response->setStatusCode(403);
    return view('system/permission_denied', [
        'page_title' => lang('System.permission_denied.page_title'),
        'slug'       => 'permission-denied'
    ]);
}