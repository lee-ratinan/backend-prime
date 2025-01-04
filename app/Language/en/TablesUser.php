<?php
return [
    'UserMaster' => [
        'id'                             => 'ID',
        'email_address'                  => 'Email Address',
        'telephone_country_calling_code' => 'Country Code',
        'telephone_number'               => 'Telephone Number',
        'user_name'                      => 'Name',
        'user_name_first'                => 'First Name',
        'user_name_family'               => 'Family Name',
        'user_gender'                    => 'Gender',
        'user_gender_values'             => [
            'M'  => 'Male',
            'F'  => 'Female',
            'NB' => 'Non-binary',
            'U'  => 'Unspecified'
        ],
        'user_date_of_birth'             => 'Date of Birth',
        'user_nationality'               => 'Nationality',
        'user_profile_status'            => 'Profile Status',
        'account_status'                 => 'Account Status',
        'account_status_values'          => [
            'A' => 'Active',
            'B' => 'Blocked',
            'T' => 'Terminated',
            'P' => 'Pending Activation',
        ],
        'account_password_hash'          => 'Password',
        'account_password_values'        => [
            'current_password' => 'Current Password',
            'new_password'     => 'New Password',
            'password_confirm' => 'Confirm Password'
        ],
        'account_password_expiry'        => 'Password Expiry',
        'account_type'                   => 'Account Type',
        'account_type_values'            => [
            'S' => 'Staff',
            'C' => 'Client'
        ],
        'employee_id'                    => 'Employee ID',
        'employee_start_date'            => 'Start Date',
        'employee_end_date'              => 'End Date',
        'employee_title'                 => 'Job Title',
        'preferred_language'             => 'Preferred Language',
        'preferred_language_values'      => [
            'en' => 'English',
            'th' => 'ภาษาไทย',
        ],
        'preferred_timezone'             => 'Preferred Timezone',
        'user_created_by'                => 'Created By',
        'user_created_at'                => 'Created At',
        'user_updated_at'                => 'Last Updated At',
    ],
    'UserRole'   => [
        'id'                     => 'ID',
        'user_id'                => 'User ID',
        'role_name'              => 'Role Name',
        'is_default_role'        => 'Default Role',
        'is_default_role_values' => [
            'Y' => 'Default',
            'N' => 'No'
        ],
        'role_created_by'        => 'Created By',
        'role_created_at'        => 'Created At',
        'role_updated_at'        => 'Last Updated At',
    ]
];