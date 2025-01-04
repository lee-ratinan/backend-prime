<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * User Role Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

class UserRoleModel extends Model
{
    protected $table = 'user_role';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'user_id',
        'role_name',
        'is_default_role',
        'role_created_by',
        'role_created_at',
        'role_updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'role_created_at';
    protected $updatedField = 'role_updated_at';
    private $configurations = [
        'id'              => [
            'type'      => 'hidden',
            'label_key' => 'TablesUser.UserRole.id'
        ],
        'user_id'         => [
            'type'      => 'number',
            'label_key' => 'TablesUser.UserRole.user_id'
        ],
        'role_name'       => [
            'type'        => 'text',
            'label_key'   => 'TablesUser.UserRole.role_name',
            'maxlength'   => 32,
            'required'    => true,
            'placeholder' => 'system-admin'
        ],
        'is_default_role' => [
            'type'      => 'select',
            'label_key' => 'TablesUser.UserRole.is_default_role',
            'required'  => true,
            'options'   => [
                'Y' => 'TablesUser.UserRole.is_default_role_values.Y',
                'N' => 'TablesUser.UserRole.is_default_role_values.N',
            ],
            'default'   => 'N'
        ],
        'role_created_by' => [
            'type'      => 'number',
            'label_key' => 'TablesUser.UserRole.role_created_by',
            'required'  => false
        ],
        'role_created_at' => [
            'type'      => 'datetime',
            'label_key' => 'TablesUser.UserRole.role_created_at',
            'required'  => false
        ],
        'role_updated_at' => [
            'type'      => 'datetime',
            'label_key' => 'TablesUser.UserRole.role_updated_at',
            'required'  => false
        ],
    ];
    const IS_DEFAULT_ROLE_YES = 'Y';
    const IS_DEFAULT_ROLE_NO = 'N';

    /**
     * Get the roles by user ID
     * @param int $user_id
     * @return array
     */
    public function getRolesByUser(int $user_id): array
    {
        return $this->where('user_id', $user_id)->findAll();
    }

    /**
     * Assign default role to user
     * @param int $user_id
     * @param int $user_role_id
     * @return bool
     * @throws ReflectionException
     */
    public function makeDefaultRole(int $user_id, int $user_role_id): bool
    {
        if ($this->where('user_id', $user_id)->set(['is_default_role' => 'N'])->update()) {
            return $this->where('id', $user_role_id)->set(['is_default_role' => 'Y'])->update();
        }
        return false;
    }

}