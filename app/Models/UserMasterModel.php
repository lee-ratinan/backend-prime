<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * User Master Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use ReflectionException;

class UserMasterModel extends Model
{
    protected $table = 'user_master';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'email_address',
        'telephone_country_calling_code',
        'telephone_number',
        'user_name_first',
        'user_name_family',
        'user_gender',
        'user_date_of_birth',
        'user_nationality',
        'user_profile_status',
        'account_status',
        'account_password_hash',
        'account_password_expiry',
        'account_type',
        'employee_id',
        'employee_start_date',
        'employee_end_date',
        'employee_title',
        'preferred_language',
        'user_created_by',
        'user_created_at',
        'user_updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'user_created_at';
    protected $updatedField = 'user_updated_at';
    const HASH_ALGORITHM = 'sha256';
    const PASSWORD_EXPIRY = '+180 days';
    CONST USER_GENDER_MALE = 'M';
    CONST USER_GENDER_FEMALE = 'F';
    CONST USER_GENDER_NON_BINARY = 'NB';
    CONST USER_GENDER_UNKNOWN = 'U';
    const ACCOUNT_TYPE_STAFF = 'S';
    const ACCOUNT_TYPE_CUSTOMER = 'C';
    const ACCOUNT_STATUS_ACTIVE = 'A';
    const ACCOUNT_STATUS_BLOCKED = 'B';
    const ACCOUNT_STATUS_TERMINATED = 'T';
    const ACCOUNT_STATUS_PENDING = 'P';

    private array $configurations = [
        'id'                      => [
            'type'      => 'hidden',
            'label_key' => 'TablesUser.UserMaster.id'
        ],
        'email_address'           => [
            'type'        => 'email',
            'label_key'   => 'TablesUser.UserMaster.email_address',
            'required'    => true,
            'placeholder' => 'john.doe@example.com'
        ],
        'telephone_number'        => [
            'type'               => 'tel',
            'country_code_label' => 'TablesUser.UserMaster.telephone_country_calling_code',
            'phone_number_label' => 'TablesUser.UserMaster.telephone_number',
            'country_code_field' => 'telephone_country_calling_code',
            'phone_number_field' => 'telephone_number',
            'placeholder'        => '1234567890',
            'required'           => false
        ],
        'user_name_first'         => [
            'type'        => 'text',
            'label_key'   => 'TablesUser.UserMaster.user_name_first',
            'required'    => true,
            'maxlength'   => 50,
            'placeholder' => 'John'
        ],
        'user_name_family'        => [
            'type'        => 'text',
            'label_key'   => 'TablesUser.UserMaster.user_name_family',
            'required'    => true,
            'maxlength'   => 50,
            'placeholder' => 'Doe'
        ],
        'user_gender'             => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserMaster.user_gender',
            'required'  => true,
            'options'   => [
                'M'  => 'TablesUser.UserMaster.user_gender_values.M',
                'F'  => 'TablesUser.UserMaster.user_gender_values.F',
                'NB' => 'TablesUser.UserMaster.user_gender_values.NB',
                'U'  => 'TablesUser.UserMaster.user_gender_values.U',
            ],
            'default'   => 'U',
        ],
        'user_date_of_birth'      => [
            'type'      => 'date',
            'label_key' => 'TablesUser.UserMaster.user_date_of_birth',
            'required'  => false,
            'min'       => '1900-01-01'
        ],
        'user_nationality'        => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserMaster.user_nationality',
            'required'  => false,
            'default'   => 'TH',
        ],
        'user_profile_status'     => [
            'type'      => 'text',
            'label_key' => 'TablesUser.UserMaster.user_profile_status',
            'required'  => false,
            'default'   => 'Hello World!'
        ],
        'account_status'          => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserMaster.account_status',
            'required'  => true,
            'options'   => [
                'A' => 'TablesUser.UserMaster.account_status_values.A',
                'B' => 'TablesUser.UserMaster.account_status_values.B',
                'T' => 'TablesUser.UserMaster.account_status_values.T',
                'P' => 'TablesUser.UserMaster.account_status_values.P',
            ],
            'default'   => 'P',
        ],
        'account_password_hash'   => [
            'type'              => 'password',
            'label_key'         => 'TablesUser.UserMaster.account_password_hash',
            'required'          => true,
            'placeholder'       => 'Password',
            'confirm_label_key' => 'TablesUser.UserMaster.account_password_values.password_confirm',
            'current_label_key' => 'TablesUser.UserMaster.account_password_values.current_password',
        ],
        'account_password_expiry' => [
            'type'      => 'date',
            'label_key' => 'TablesUser.UserMaster.account_password_expiry',
            'required'  => false
        ],
        'account_type'            => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserMaster.account_type',
            'required'  => true,
            'options'   => [
                'S' => 'TablesUser.UserMaster.account_type_values.S',
                'C' => 'TablesUser.UserMaster.account_type_values.C',
            ],
            'default'   => 'S',
        ],
        'employee_id'             => [
            'type'        => 'text',
            'label_key'   => 'TablesUser.UserMaster.employee_id',
            'required'    => false,
            'maxlength'   => 20,
            'placeholder' => 'EMP12345'
        ],
        'employee_start_date'     => [
            'type'      => 'date',
            'label_key' => 'TablesUser.UserMaster.employee_start_date',
            'required'  => false
        ],
        'employee_end_date'       => [
            'type'      => 'date',
            'label_key' => 'TablesUser.UserMaster.employee_end_date',
            'required'  => false
        ],
        'employee_title'          => [
            'type'        => 'text',
            'label_key'   => 'TablesUser.UserMaster.employee_title',
            'required'    => false,
            'maxlength'   => 50,
            'placeholder' => 'Manager'
        ],
        'preferred_language'      => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserMaster.preferred_language',
            'required'  => false,
            'options'   => [
                'en' => 'TablesUser.UserMaster.preferred_language_values.en',
                'th' => 'TablesUser.UserMaster.preferred_language_values.th',
            ],
            'default'   => 'en',
        ],
        'preferred_timezone'      => [
            'type'      => 'text',
            'label_key' => 'TablesUser.UserMaster.preferred_timezone',
            'required'  => false,
            'default'   => 'Asia/Bangkok'
        ],
        'user_created_by'         => [
            'type'      => 'number',
            'label_key' => 'TablesUser.UserMaster.user_created_by',
            'required'  => false
        ],
        'user_created_at'         => [
            'type'      => 'datetime',
            'label_key' => 'TablesUser.UserMaster.user_created_at',
            'required'  => false
        ],
        'user_updated_at'         => [
            'type'      => 'datetime',
            'label_key' => 'TablesUser.UserMaster.user_updated_at',
            'required'  => false
        ],
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @return array
     */
    public function getConfigurations(array $columns = []): array
    {
        $configurations  = $this->configurations;
        $countries       = lang('ListCountries.countries');
        $final_countries = array_map(function ($value) {
            return $value['common_name'];
        }, $countries);
        $configurations['user_nationality']['options'] = $final_countries;
        $configurations['user_date_of_birth']['max'] = date('Y-m-d');
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * Authenticate user: retrieve user by email and password
     * @param $username
     * @param $password
     * @return array
     */
    public function authenticateUser($username, $password): array
    {
        $hashed_password = hash(self::HASH_ALGORITHM, $password);
        $user            = $this->where('email_address', $username)
            ->where('account_password_hash', $hashed_password)
            ->first();
        return $user ?: [];
    }

    /**
     * Block user by email
     * @throws ReflectionException
     */
    public function blockUser($email): bool
    {
        $user = $this->where('email_address', $email)->first();
        if (!$user) {
            log_message('warning', 'A person with ' . $email . ' has attempted to login for 3 times but the email is not found in the database.');
            return false;
        }
        $model = new LogActivityModel();
        $model->insertTableUpdate($this->table, $user['id'], ['account_status' => 'B'], $user['id'], 'block-account');
        return $this->update($user['id'], [
            'account_status' => 'B'
        ]);
    }

    /**
     * Update password and extend the expiry date
     * @param int $user_id
     * @param string $new_password
     * @return bool
     * @throws ReflectionException
     */
    public function updatePassword(int $user_id, string $new_password): bool
    {
        $hashed_password = hash(self::HASH_ALGORITHM, $new_password);
        // check if the new password the same as the previous one
        $same_password   = $this->where('id', $user_id)->where('account_password_hash', $hashed_password)->first();
        if (!empty($same_password)) {
            return false;
        }
        $new_expiry_date = date('Y-m-d', strtotime(self::PASSWORD_EXPIRY));
        $data            = [
            'account_password_hash'   => $hashed_password,
            'account_password_expiry' => $new_expiry_date
        ];
        $data_for_log    = [
            'account_password_hash'   => '********',
            'account_password_expiry' => $new_expiry_date
        ];
        $model = new LogActivityModel();
        $model->insertTableUpdate($this->table, $user_id, $data_for_log, $user_id, 'update-password');
        return $this->update($user_id, $data);
    }

    /**
     * Generate random password and its hash
     * @param int $length
     * @return array
     */
    public function generateRandomPassword(int $length = 12): array
    {
        $uppercase    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase    = 'abcdefghijklmnopqrstuvwxyz';
        $numbers      = '0123456789';
        $specialChars = '@$!%*?&';
        $allChars     = $uppercase . $lowercase . $numbers . $specialChars;
        $password     = $uppercase[rand(0, strlen($uppercase) - 1)] .
            $lowercase[rand(0, strlen($lowercase) - 1)] .
            $numbers[rand(0, strlen($numbers) - 1)] .
            $specialChars[rand(0, strlen($specialChars) - 1)];
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        $password = str_shuffle($password);
        $hashed   = hash(self::HASH_ALGORITHM, $password);
        return [
            'plain'  => $password,
            'hashed' => $hashed
        ];
    }

    /**
     * Apply filter for DataTables
     * @param string $search_value
     * @param string $account_type
     * @param string $account_status
     * @return void
     */
    private function applyFilter(string $search_value, string $account_type, string $account_status): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('user_master.role_name', $search_value)
                ->orLike('user_master.role_description', $search_value)
                ->groupEnd();
        }
        if (!empty($account_type)) {
            $this->where('user_master.account_type', $account_type);
        }
        if (!empty($account_status)) {
            $this->where('user_master.account_status', $account_status);
        }
    }

    /**
     * Retrieve the data for DataTables
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $account_type
     * @param string $account_status
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $account_type, string $account_status): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($account_type) || !empty($account_status)) {
            $this->applyFilter($search_value, $account_type, $account_status);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $account_type, $account_status);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('user_master.*, creator.user_name_first AS created_first, creator.user_name_family AS created_last')
            ->join('user_master AS creator', 'user_master.user_created_by = creator.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $phone_util = PhoneNumberUtil::getInstance();
        foreach ($raw_result as $row) {
            $encoded_email = encode_caesar_cipher($row['email_address']);
            $phone         = '-';
            if (!empty($row['telephone_country_calling_code']) && !empty($row['telephone_number'])) {
                try {
                    $phone_obj = $phone_util->parse($row['telephone_country_calling_code'] . $row['telephone_number'], null);
                    $phone     = $phone_util->format($phone_obj, PhoneNumberFormat::INTERNATIONAL);
                } catch (NumberParseException $e) {
                    $phone     = $row['telephone_country_calling_code'] . ' ' . $row['telephone_number'];
                    log_message('error', 'Error parsing phone number: ' . $e->getMessage());
                }
            }
            $result[]      = [
                '<div class="btn-group" role="group"><a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/user/edit/' . $encoded_email) . '"><i class="fa-solid fa-edit"></i></a><a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/public-profile/' . $encoded_email) . '"><i class="fa-regular fa-eye"></i></a></div>',
                $row['id'],
                $row['email_address'],
                $phone,
                $row['user_name_first'] . ' ' . $row['user_name_family'],
                lang('TablesUser.UserMaster.account_type_values.' . $row['account_type']),
                lang('TablesUser.UserMaster.account_status_values.' . $row['account_status']),
                $row['created_first'] . ' ' . $row['created_last'],
                (empty($row['user_created_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['user_created_at'] ?? '') . 'Z</span>'),
                (empty($row['user_updated_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['user_updated_at'] ?? '') . 'Z</span>'),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}