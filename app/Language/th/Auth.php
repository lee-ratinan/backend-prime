<?php
return [
    'login'           => [
        'page_title'         => 'เข้าสู่ระบบ',
        'heading'            => 'เข้าสู่ระบบ',
        'subheading'         => 'กรุณากรอกอีเมลและรหัสผ่านของคุณ',
        'login_button'       => 'เข้าสู่ระบบ',
        'or'                 => 'หรือ',
        'invalid_password'   => 'รหัสผ่านไม่ถูกต้อง กรุณากรอกใหม่อีกครั้ง',
        'blocked_account'    => 'บัญชีของคุณถูกบล็อก',
        'terminated_account' => 'บัญชีของคุณถูกปิด',
        'pending_account'    => 'บัญชีของคุณอยู่ระหว่างการอนุมัติ',
        'no_roles'           => 'บัญชีของคุณไม่มีบทบาทระบุไว้ กรุณาติดต่อผู้ดูแลระบบ',
        'unknown_error'      => 'เกิดข้อผิดพลาดบางอย่างขณะเข้าสู่ระบบ กรุณาลองใหม่อีกครั้ง',
        'google_error'       => 'เกิดข้อผิดพลาดขณะเข้าสู่ระบบด้วยบัญชี Google กรุณาลองใหม่อีกครั้ง',
        'empty_fields'       => 'กรุณากรอกอีเมลและรหัสผ่านของคุณ',
        'wrong_pw_3_times'   => 'บัญชีของคุณถูกบล็อกเนื่องจากกรอกรหัสผ่านผิด 3 ครั้ง',
        'success'            => 'เข้าสู่ระบบสำเร็จ',
        'expired_password'   => [
            'heading'                => 'รหัสผ่านหมดอายุ',
            'subheading'             => 'รหัสผ่านหมดอายุ กรุณาเปลี่ยนรหัสผ่านของคุณ',
            'new_password'           => 'รหัสผ่านใหม่',
            'confirm_password'       => 'ยืนยันรหัสผ่าน',
            'update_password_button' => 'เปลี่ยนรหัสผ่าน',
            'empty_fields'           => 'กรุณากรอกรหัสผ่านใหม่และยืนยันรหัสผ่าน',
            'password_not_match'     => 'รหัสผ่านไม่ตรงกัน',
            'password_updated'       => 'รหัสผ่านของคุณถูกเปลี่ยนแล้ว',
        ],
        'otp'                => [
            'heading'           => 'ยืนยัน OTP',
            'subheading'        => 'กรุณากรอกรหัส OTP ที่ส่งไปยังอีเมลของคุณ',
            'otp'               => 'OTP',
            'verify_otp_button' => 'ยืนยัน OTP',
            'empty_otp'         => 'กรุณากรอกรหัส OTP ที่ส่งไปยังอีเมลของคุณ',
            'wrong_otp'         => 'รหัส OTP ไม่ถูกต้อง หรือหมดอายุแล้ว กรุณากรอกใหม่อีกครั้ง',
            'resend_otp'        => 'ส่ง OTP อีกครั้ง',
            'resend_success'    => 'ส่ง OTP ไปยังอีเมลของคุณสำเร็จ',
            'verified'          => 'ยืนยันรหัส OTP สำเร็จ',
            'email'             => [
                'subject' => 'ยืนยัน OTP',
                'body'    => "เรียน {0},\nรหัส OTP ของคุณคือ {1} รหัสนี้จะหมดอายุภายใน 5 นาที\n\nกรุณาอย่าให้คนอื่นรับรู้รหัส OTP ของคุณ หากคุณไม่ได้กำลังเข้าสู่ระบบ กรุณาเพิกเฉยและลบอีเมลฉบับนี้ทิ้ง"
            ]
        ]
    ],
    'forgot_password' => [
        'page_title'   => 'ลืมรหัสผ่าน',
        'heading'      => 'ลืมรหัสผ่าน',
        'subheading'   => 'กรุณากรอกอีเมลของคุณ เราจะส่งอีเมลเพื่อเปลี่ยนรหัสผ่านของคุณใหม่',
        'reset_button' => 'เปลี่ยนรหัสผ่าน',
        'back_button'  => 'กลับไปที่หน้าเข้าสู่ระบบ',
        'done'         => 'เราได้ส่งอีเมลเพื่อเปลี่ยนรหัสผ่านของคุณแล้ว กรุณาตรวจสอบอีเมลของคุณและคำตามคำแนะนำเพื่อเปลี่ยนรหัสผ่าน',
        'error'        => 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้ง',
    ],
    'reset_password'  => [
        'page_title'       => 'เปลี่ยนรหัสผ่าน',
        'heading'          => 'เปลี่ยนรหัสผ่าน',
        'subheading'       => 'คุณได้ยื่นคำขอเปลี่ยนรหัสผ่าน กรุณาเพิกเฉยหากคุณไม่ได้ยื่นคำขอนี้',
        'error_token'      => 'รหัส Token ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
        'error_update'     => 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้ง',
        'password_updated' => 'รหัสผ่านของคุณถูกเปลี่ยนแล้ว',
    ],
    'register'        => [
        'page_title' => 'ลงทะเบียน',
        'heading'    => 'ลงทะเบียน',
        'subheading' => 'กรุณากรอกข้อมูลของคุณเพื่อทำการสร้างบัญชีใหม่',
    ],
];