<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Role Master Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;

class RoleMasterModel extends Model
{
    protected $table = 'role_master';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'role_name',
        'role_description',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    private array $configurations = [
        'id'               => [
            'type'      => 'hidden',
            'label_key' => 'TablesRole.RoleMaster.id'
        ],
        'role_name'        => [
            'type'        => 'text',
            'label_key'   => 'TablesRole.RoleMaster.role_name',
            'required'    => true,
            'maxlength'   => 32,
            'placeholder' => 'admin'
        ],
        'role_description' => [
            'type'        => 'text',
            'label_key'   => 'TablesRole.RoleMaster.role_description',
            'required'    => true,
            'maxlength'   => 128,
            'placeholder' => 'Administrator'
        ],
        'created_by'       => [
            'type'      => 'number',
            'label_key' => 'TablesRole.RoleMaster.created_by',
            'required'  => false
        ],
        'created_at'       => [
            'type'      => 'datetime',
            'label_key' => 'TablesRole.RoleMaster.created_at',
            'required'  => false
        ],
        'updated_at'       => [
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
     * Retrieve the list of features for the role name
     * @return string[]
     */
    public function retrieveAccessRightsByRole(string $role_name): array
    {
        $result = $this->select('a.access_feature, a.access_level')
            ->where('role_name', $role_name)
            ->join('role_access as a', 'role_master.id = a.role_id')->findAll();
        if (empty($result)) {
            return [];
        }
        $access_rights = [];
        foreach ($result as $row) {
            $access_rights[$row['access_feature']] = $row['access_level'];
        }
        return $access_rights;
    }

    /**
     * Apply search value for DataTables
     * @param string $search_value
     * @return void
     */
    private function applyFilter(string $search_value): void
    {
        $this->groupStart()
            ->like('role_name', $search_value)
            ->orLike('role_description', $search_value)
            ->groupEnd();
    }

    /**
     * Retrieve the data for DataTables
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value)) {
            $this->applyFilter($search_value);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('role_master.*, user_master.user_name_first, user_master.user_name_family')
            ->join('user_master', 'role_master.created_by = user_master.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/role/edit/' . $row['role_name']) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                $row['role_name'],
                $row['role_description'],
                $row['user_name_first'] . ' ' . $row['user_name_family'],
                (empty($row['created_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['created_at']) . 'Z</span>'),
                (empty($row['updated_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['updated_at']) . 'Z</span>'),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * Get role by name
     * @param string $role_name
     * @return array|null
     */
    public function getRoleByName(string $role_name): array|null
    {
        return $this->select('role_master.*, user_master.user_name_first, user_master.user_name_family')
            ->join('user_master', 'role_master.created_by = user_master.id', 'left outer')
            ->where('role_name', $role_name)->first();
    }

    /**
     * Get unassigned role for user
     * @param int $user_id
     * @return array|null
     */
    public function getUnassignedRoleForUser(int $user_id): array|null
    {
        return $this->where("role_name NOT IN (SELECT role_name FROM user_role WHERE user_id = {$user_id})")->orderBy('role_name', 'asc')->findAll();
    }
}