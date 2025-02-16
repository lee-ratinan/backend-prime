<?php
return [
    'login'           => [
        'page_title'         => 'User Login',
        'heading'            => 'Login to your account',
        'subheading'         => 'Please enter your email address and password to login.',
        'login_button'       => 'Login',
        'or'                 => 'or',
        'invalid_password'   => 'Invalid password, please try again.',
        'blocked_account'    => 'Account blocked.',
        'terminated_account' => 'Account terminated.',
        'pending_account'    => 'Your account is pending approval.',
        'no_roles'           => 'You do not have any roles assigned to your account. Please contact the administrator.',
        'unknown_error'      => 'An unknown error has occurred, please try again.',
        'google_error'       => 'An error occurred while signing in with Google.',
        'empty_fields'       => 'Please enter your email address and password.',
        'wrong_pw_3_times'   => 'Your account has been blocked due to 3 failed login attempts.',
        'success'            => 'Login successful.',
        'expired_password'   => [
            'heading'                => 'Expired Password',
            'subheading'             => 'Your password has expired, please update.',
            'new_password'           => 'New Password',
            'confirm_password'       => 'Confirm Password',
            'update_password_button' => 'Update Password',
            'empty_fields'           => 'Please enter your new and confirm password.',
            'password_not_match'     => 'New password and confirm password do not match.',
            'password_updated'       => 'Password updated.',
        ],
        'otp'                => [
            'heading'           => 'OTP Verification',
            'subheading'        => 'Please enter the OTP sent to your email address.',
            'otp'               => 'OTP',
            'verify_otp_button' => 'Verify OTP',
            'empty_otp'         => 'Please enter the OTP sent to your email address.',
            'wrong_otp'         => 'Invalid or expired OTP. Please log in again.',
            'resend_otp'        => 'Resend OTP',
            'resend_success'    => 'OTP has been sent to your email address.',
            'verified'          => 'OTP verified.',
            'email'             => [
                'subject' => 'Login OTP',
                'body'    => "Dear {0},\nYour one-time password (OTP) is {1}. It will expire in 5 minutes.\n\nPlease do not share this OTP with anyone. If you did not request this OTP, please ignore this email."
            ]
        ]
    ],
    'forgot_password' => [
        'page_title'   => 'Forgot Password',
        'heading'      => 'Forgot Password',
        'subheading'   => 'Please enter your email address to reset your password.',
        'reset_button' => 'Reset Password',
        'back_button'  => 'Back to Login',
        'done'         => 'Password reset email is sent, please check your email and follow the instruction.',
        'error'        => 'An error occurred, please try again.',
    ],
    'reset_password'  => [
        'page_title'       => 'Reset Password',
        'heading'          => 'Reset Password',
        'subheading'       => 'You have requested a password reset. Please ignore if you did not request for it.',
        'error_token'      => 'Invalid token, please try again.',
        'error_update'     => 'An error occurred, please try again.',
        'password_updated' => 'Password updated.',
    ],
    'register'        => [
        'page_title' => 'Register',
        'heading'    => 'Register',
        'subheading' => 'Please enter your details to create an account.',
    ],
];