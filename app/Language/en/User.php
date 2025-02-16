<?php
return [
    'index' => [
        'page_title' => 'User'
    ],
    'edit'  => [
        'page_title_edit'         => 'Edit User [{0}]',
        'page_title_new'          => 'Create New User',
        'controlled_account_data' => 'Controlled Account Data',
        'grant_roles'             => 'Grant Roles',
        'granted_roles'           => 'Granted Roles',
        'no_roles_granted'        => 'No roles have been granted to this user. This user will not be able to log in until at least one role is granted.',
        'default_role'            => 'Default Role',
        'make_default_role'       => 'Make Default Role',
        'grant_more_role'         => 'Grant More Role',
        'successful_update'       => 'Changes are saved.',
        'successful_create'       => 'User is created; welcome email is sent.',
        'role_granted'            => 'Role granted!',
        'default_role_set'        => 'Default role is set!',
        'email'                   => [
            'subject' => 'Welcome to {0} - Your Account has been created!',
            'body'    => "Dear {0},\n\nYour account has been created successfully. Please use the following credentials to log in:\n\nEmail Address: {1}\nPassword: {2}\n\nPlease change your password after logging in for the first time."
        ]
    ]
];