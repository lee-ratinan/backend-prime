<?php
return [
    'UserMaster' => [
        'id'                             => 'ไอดี',
        'email_address'                  => 'อีเมล',
        'telephone_country_calling_code' => 'รหัสโทรศัพท์ประเทศ',
        'telephone_number'               => 'หมายเลขโทรศัพท์',
        'user_name'                      => 'ชื่อ',
        'user_name_first'                => 'ชื่อจริง',
        'user_name_family'               => 'นามสกุล',
        'user_gender'                    => 'เพศ',
        'user_gender_values'             => [
            'M'  => 'ชาย',
            'F'  => 'หญิง',
            'NB' => 'เพศอื่นๆ',
            'U'  => 'ไม่ระบบ'
        ],
        'user_date_of_birth'             => 'วันเกิด',
        'user_nationality'               => 'สัญชาติ',
        'user_profile_status'            => 'สถานะโปรไฟล์',
        'account_status'                 => 'สถานะบัญชี',
        'account_status_values'          => [
            'A' => 'ปกติ',
            'B' => 'บล็อก',
            'T' => 'ปิด',
            'P' => 'รอการอนุมัติ',
        ],
        'account_password_hash'          => 'รหัสผ่าน',
        'account_password_values'        => [
            'current_password' => 'รหัสผ่านปัจจุบัน',
            'new_password'     => 'รหัสผ่านใหม่',
            'password_confirm' => 'ยืนยันรหัสผ่าน',
        ],
        'account_password_expiry'        => 'วันหมดอายุของรหัสผ่าน',
        'account_type'                   => 'ประเภทบัญชี',
        'account_type_values'            => [
            'S' => 'พนักงาน',
            'C' => 'ลูกค้า',
        ],
        'employee_id'                    => 'ไอดีพนักงาน',
        'employee_start_date'            => 'วันเริ่มงาน',
        'employee_end_date'              => 'วันสิ้นสุดงาน',
        'employee_title'                 => 'ตำแหน่ง',
        'preferred_language'             => 'ภาษาที่ต้องการ',
        'preferred_language_values'      => [
            'en' => 'English',
            'th' => 'ภาษาไทย',
        ],
        'preferred_timezone'             => 'เขตเวลาที่ต้องการ',
        'user_created_by'                => 'สร้างโดย',
        'user_created_at'                => 'สร้างเมื่อ',
        'user_updated_at'                => 'แก้ไขล่าสุดเมื่อ',
    ],
    'UserRole'   => [
        'id'                     => 'ไอดี',
        'user_id'                => 'ไอดีผู้ใช้',
        'role_name'              => 'ชื่อบทบาท',
        'is_default_role'        => 'เป็นบทบาทเริ่มต้น',
        'is_default_role_values' => [
            'Y' => 'ใช่',
            'N' => 'ไม่ใช่',
        ],
        'role_created_by'        => 'สร้างโดย',
        'role_created_at'        => 'สร้างเมื่อ',
        'role_updated_at'        => 'แก้ไขล่าสุดเมื่อ',
    ]
];