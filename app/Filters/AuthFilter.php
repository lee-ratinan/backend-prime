<?php

/**
 * *********************************************************************
 * THIS FILTER IS SYSTEM FILTER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Auth Filter
 * @package App\Filters
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AuthFilter implements FilterInterface
{

    /**
     * @param RequestInterface $request
     * @param $arguments
     * @return RedirectResponse|RequestInterface|ResponseInterface
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if ($session->logged_in)
        {
            return $request;
        }
        // Not logged in
        $method = strtoupper($request->getMethod());
        if ('GET' == $method)
        {
            return redirect()->to(base_url('logout'));
        }
        // AJAX Return, make it compatible with both DataTables and other AJAX requests
        return Services::response()->setJSON([
            'draw'            => @$request->getPost('draw'),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
            'error'           => lang('System.status_message.unauthorized_access'),
            'toast'           => lang('System.status_message.unauthorized_access')
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = NULL)
    {
        // Do nothing
    }
}