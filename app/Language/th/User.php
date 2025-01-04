<?php
return [
    'index' => [
        'page_title' => 'บัญชีผู้ใช้',
    ],
    'edit'  => [
        'page_title_edit'         => 'แก้ไขบัญชีผู้ใช้ [{0}]',
        'page_title_new'          => 'สร้างบัญชีใหม่',
        'controlled_account_data' => 'ข้อมูลบัญชีควบคุม',
        'grant_roles'             => 'การให้สิทธิ์บทบาท',
        'granted_roles'           => 'บทบาทที่มีสิทธิ์แล้ว',
        'no_roles_granted'        => 'บัญชีนี้ไม่มีสิทธิ์ในบทบาท ทำให้บัญชีนี้จะไม่สามารถเข้าสู่ระบบได้จนกว่าจะมีบทบาทอย่างน้อยหนึ่งบทบาท',
        'default_role'            => 'บทบาทเริ่มต้น',
        'make_default_role'       => 'ทำให้เป็นบทบาทเริ่มต้น',
        'grant_more_role'         => 'ให้สิทธิ์บทบาทเพิ่มเติม',
        'successful_update'       => 'บัญชีผู้ใช้ได้รับการแก้ไขเรียบร้อยแล้ว',
        'successful_create'       => 'บัญชีผู้ใช้ได้รับการสร้าง และอีเมลต้อนรับถูกส่งไปยังผู้ใช้เรียบร้อยแล้ว',
        'role_granted'            => 'ให้สิทธิ์ในบทบาทเรียบร้อยแล้ว',
        'default_role_set'        => 'บทบาทเริ่มต้นได้รับการกำหนดเรียบร้อยแล้ว',
        'email'                   => [
            'subject' => 'ยินดีต้อนรับสู่ {0} - บัญชีผู้ใช้ของคุณถูกสร้างสำเร็จแล้ว!',
            'body'    => "เรียน {0},\n\nบัญชีผู้ใช้ของคุณถูกสร้างสำเร็จแล้ว กรุณาใช้ชื่อผู้ใช้และรหัสผ่านด้านล่างเพื่อเข้าสู่ระบบ:\n\nชื่อผู้ใช้: {1}\nรหัสผ่าน: {2}\n\nกรุณาเปลี่ยนรหัสผ่านหลังจากเข้าสู่ระบบครั้งแรก"
        ]
    ]
];