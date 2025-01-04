<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Role Access Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;

class RoleAccessModel extends Model
{
    protected $table = 'role_access';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'role_id',
        'access_feature',
        'access_level',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    private array $configurations = [
        'id'             => [
            'type'      => 'hidden',
            'label_key' => 'TablesRole.RoleMaster.id'
        ],
        'access_feature' => [
            'type'        => 'text',
            'label_key'   => 'TablesRole.RoleAccess.access_feature',
            'required'    => true,
            'maxlength'   => 32,
            'placeholder' => 'role_master'
        ],
        'access_level'   => [
            'type'      => 'select',
            'label_key' => 'TablesRole.RoleAccess.access_level',
            'required'  => true
        ],
        'role_id'        => [
            'type'      => 'number',
            'label_key' => 'TablesRole.RoleAccess.role_id',
            'required'  => true
        ],
        'created_by'     => [
            'type'      => 'number',
            'label_key' => 'TablesRole.RoleMaster.created_by',
            'required'  => false
        ],
        'created_at'     => [
            'type'      => 'datetime',
            'label_key' => 'TablesRole.RoleMaster.created_at',
            'required'  => false
        ],
        'updated_at'     => [
            'type'      => 'datetime',
            'label_key' => 'TablesRole.RoleMaster.updated_at',
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
        $configurations = $this->configurations;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * Get the access by role name
     * @param string $role_name
     * @return array
     */
    public function getAccessByRoleName(string $role_name): array
    {
        $role_master_model = new RoleMasterModel();
        $role_master       = $role_master_model->getRoleByName($role_name);
        if (empty($role_master)) {
            return [];
        }
        $role_id       = $role_master['id'];
        $role_accesses = $this->select('role_access.*, user_master.user_name_first, user_master.user_name_family')
            ->join('user_master', 'user_master.id = role_access.created_by', 'left outer')
            ->where('role_id', $role_id)->findAll();
        return [
            'role_master'   => $role_master,
            'role_accesses' => $role_accesses
        ];
    }

    /**
     * Retrieve all role accesses with their role_names
     * @return array
     */
    public function getAllRoles(): array
    {
        $roles = $this->select('role_access.*, role_master.role_name')
            ->join('role_master', 'role_master.id = role_access.role_id')
            ->findAll();
        $result = [];
        foreach ($roles as $role) {
            $result[$role['role_name']][$role['access_feature']] = $role['access_level'];
        }
        return $result;
    }
}