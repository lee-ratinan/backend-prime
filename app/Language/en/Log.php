<?php
return [
    'index'     => [
        'page_title'   => 'Activity Log',
        'table'        => [
            'activity'     => 'Activity',
            'table'        => 'Table Name',
            'id'           => 'Row ID',
            'details'      => 'Details',
            'performed_by' => 'Performed By',
            'logged_at'    => 'Logged At'
        ],
        'activity_key' => [
            'login'           => 'Login',
            'update-table'    => 'Table: Insert/Update',
            'delete-table'    => 'Table: Delete',
            'update-password' => 'Password: Update',
            'update-avatar'   => 'Avatar: Upload/Update',
            'remove-avatar'   => 'Avatar: Remove',
            'switch-role'     => 'Switch Role',
            'update-favicon'  => 'Favicon: Upload/Update',
            'update-logo'     => 'Logo: Upload/Update',
        ]
    ],
    'email'     => [
        'page_title' => 'Email Log',
        'table'      => [
            'created_at'    => 'Sent At',
            'email_to'      => 'Recipient Email',
            'email_subject' => 'Subject',
            'email_status'  => 'Status'
        ]
    ],
    'file_list' => [
        'page_title' => 'Log Files'
    ],
    'file_view' => [
        'page_title' => 'View Log File [{0}]',
        'file_name'  => 'File Name'
    ]
];