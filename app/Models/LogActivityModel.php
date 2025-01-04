<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Log Activity Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

class LogActivityModel extends Model
{
    protected $table = 'log_activity';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'activity_key',
        'table_involved',
        'table_id_updated',
        'activity_detail',
        'done_by',
        'done_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'done_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Insert log activity when user login
     * @param int $user_id
     * @param string $result
     * @param string $role (optional)
     * @return bool|int|string
     * @throws ReflectionException
     */
    public function insertLogin(int $user_id, string $result, string $role = '-'): bool|int|string
    {
        $result = [
            'result' => $result,
            'role'   => $role
        ];
        $data   = [
            'activity_key'     => 'login',
            'table_involved'   => 'session',
            'table_id_updated' => $user_id,
            'activity_detail'  => json_encode($result),
            'done_by'          => $user_id
        ];
        return $this->insert($data);
    }

    /**
     * Insert log activity
     * @param string $table_involved
     * @param int $id
     * @param array $new_data
     * @param int $user_id
     * @param string $activity_key (optional)
     * @return bool|int|string
     * @throws ReflectionException
     */
    public function insertTableUpdate(string $table_involved, int $id, array $new_data, int $user_id, string $activity_key = 'update-table'): bool|int|string
    {
        $data = [
            'activity_key'     => $activity_key,
            'table_involved'   => $table_involved,
            'table_id_updated' => $id,
            'activity_detail'  => json_encode($new_data),
            'done_by'          => $user_id
        ];
        return $this->insert($data);
    }

    /**
     * Apply filter to the query
     * @param string $activity_key
     * @param string $table_involved
     * @param int $table_id_updated
     * @param string $date_start
     * @param string $date_end
     * @return void
     */
    private function applyFilter(string $activity_key, string $table_involved, int $table_id_updated, string $date_start, string $date_end): void
    {
        if (!empty($activity_key)) {
            $this->where('log_activity.activity_key', $activity_key);
        }
        if (!empty($table_involved)) {
            $this->where('log_activity.table_involved', $table_involved);
        }
        if (!empty($table_id_updated)) {
            $this->where('log_activity.table_id_updated', $table_id_updated);
        }
        if (!empty($date_start)) {
            $this->where('log_activity.done_at >=', $date_start);
        }
        if (!empty($date_end)) {
            $this->where('log_activity.done_at <=', $date_end);
        }
    }

    /**
     * Get the activity keys
     * @param string $key (optional)
     * @return array|string
     */
    public function getActivityKeys(string $key = ''): array|string
    {
        $keys          = lang('Log.index.activity_key');
        $activity_keys = array_map(function ($value) {
            return $value;
        }, $keys);
        if (isset($activity_keys[$key])) {
            return $activity_keys[$key];
        }
        return $activity_keys;
    }

    /**
     * Get the data for DataTables
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $activity_key
     * @param string $table_involved
     * @param int $table_id_updated
     * @param string $date_start
     * @param string $date_end
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $activity_key, string $table_involved, int $table_id_updated, string $date_start, string $date_end): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($activity_key) || !empty($table_involved) || !empty($table_id_updated) || !empty($date_start) || !empty($date_end)) {
            $this->applyFilter($activity_key, $table_involved, $table_id_updated, $date_start, $date_end);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($activity_key, $table_involved, $table_id_updated, $date_start, $date_end);
        }
        $this->select('log_activity.*, user_master.user_name_first, user_master.user_name_family')
            ->join('user_master', 'user_master.id = log_activity.done_by')
            ->orderBy($order_column, $order_direction)
            ->limit($length, $start);
        $raw_result = $this->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $detail_array = json_decode($row['activity_detail'], true);
            $detail_str   = [];
            foreach ($detail_array as $key => $value) {
                $detail_str[] = '<li>' . $key . ': ' . $value . '</li>';
            }
            $result[]     = [
                (empty($row['done_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['done_at']) . 'Z</span>'),
                $row['user_name_first'] . ' ' . $row['user_name_family'],
                $this->getActivityKeys($row['activity_key']),
                $row['table_involved'],
                $row['table_id_updated'],
                '<ul>' . implode('', $detail_str) . '</ul>'
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * Delete old log activities older than the given date
     * @param string $date
     * @return array
     */
    public function deleteOldLog(string $date): array
    {
        $cnt_before  = $this->countAllResults();
        $date_string = date('Y-m-d 00:00:00', strtotime($date));
        $result      = $this->where('done_at <=', $date_string)->delete();
        $cnt_after   = $this->countAllResults();
        return [
            'deleted' => $result,
            'before'  => $cnt_before,
            'after'   => $cnt_after,
            'delta'   => $cnt_before - $cnt_after
        ];
    }
}