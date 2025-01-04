<?php
return [
    'RoleMaster' => [
        'id'               => 'ID',
        'role_name'        => 'Role Name',
        'role_description' => 'Role Description',
        'created_by'       => 'Created By',
        'created_at'       => 'Created At',
        'updated_at'       => 'Updated At'
    ],
    'RoleAccess' => [
        'id'                  => 'ID',
        'role_id'             => 'Role Master ID',
        'access_feature'      => 'Accessible Feature',
        'access_level'        => 'Access Level',
        'access_level_values' => [
            '0' => 'No Access',
            '1' => 'Read-only',
            '2' => 'Editable'
        ],
        'created_by'          => 'Created By',
        'created_at'          => 'Created At',
        'updated_at'          => 'Updated At'
    ]
];