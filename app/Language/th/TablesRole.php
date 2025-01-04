<?php
return [
    'RoleMaster' => [
        'id'               => 'ไอดี',
        'role_name'        => 'ชื่อบทบาท',
        'role_description' => 'รายละเอียดบทบาท',
        'created_by'       => 'สร้างโดย',
        'created_at'       => 'สร้างเมื่อ',
        'updated_at'       => 'แก้ไขล่าสุดเมื่อ',
    ],
    'RoleAccess' => [
        'id'                  => 'ไอดี',
        'role_id'             => 'ไอดีบทบาท',
        'access_feature'      => 'ฟีเจอร์ที่เข้าถึงได้',
        'access_level'        => 'ระดับการเข้าถึง',
        'access_level_values' => [
            '0' => 'ไม่สามารถเข้าถึง',
            '1' => 'เข้าถึงได้',
            '2' => 'เข้าถึงและแก้ไขได้',
        ],
        'created_by'          => 'สร้างโดย',
        'created_at'          => 'สร้างเมื่อ',
        'updated_at'          => 'แก้ไขล่าสุดเมื่อ',
    ]
];