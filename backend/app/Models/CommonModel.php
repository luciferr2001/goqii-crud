<?php

namespace App\Models;

use CodeIgniter\Model;
use PDO;

class CommonModel extends Model
{

    public function getData($table, $where, $select, $orWhere = null, $like = null, $like_field_name = null, $distinct = false, $where_array = null, $like_direction = 'both', $order_on = null, $order_type = null, $like_array = null, $group_by = null, $limit = null, $where_in = null, $where_in_column = null)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $builder->select($select);
        if ($where) {
            $builder->where($where);
        }
        if ($where_array) {
            foreach ($where_array as $key => $value) {
                $builder->where($value);
            }
        }
        if ($where_in && $where_in_column) {
            $builder->whereIn($where_in_column, $where_in);
        }
        if ($orWhere) {
            $builder->groupStart();
            $builder->orWhere($orWhere);
            $builder->groupEnd();
        }
        if ($like != null) {
            $builder->groupStart();
            if ($like_array) {
                $concatFields = "CONCAT($like_field_name, ' ', " . implode(", ' ', ", $like_array) . ")";
                $builder->where("$concatFields LIKE", "%$like%");
            } else {
                $builder->like($like_field_name, $like, $like_direction);
            }
            $builder->groupEnd();
        }
        if ($distinct) {
            $builder->distinct();
        }
        if ($order_on and $order_type) {
            $builder->orderBy($order_on, $order_type);
        }

        if ($group_by) {
            $builder->groupBy($group_by);
        }
        //a
        if ($limit) {
            $builder->limit($limit);
        }

        $employee = $builder->get()->getResultArray();
        return $employee;
    }

    public function setData($table, $data, $getReturnId = false)
    {
        $db = \Config\Database::connect('default');
        $reutrnDBInstance = $db;
        $builder = $db->table($table);
        $insertResult = $builder->insert($data);
        if ($insertResult) {
            if ($getReturnId != false) {
                $getIdForReturn = $reutrnDBInstance->insertID();
                return $getIdForReturn;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    public function getLastId($table)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $getIdForReturn = $builder->selectMax('id')->get()->getResultArray();
        return $getIdForReturn;
    }

    public function checkRecordExists($conditions, $table)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $t = $builder->where($conditions)->countAllResults('table_name');
        if ($t > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkFlightRecordExists($conditions, $table, $isLike = false)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);

        if ($isLike) {
            // Assuming 'flight_id' is the key to be searched with LIKE
            $builder->like('flight_id', $conditions['flight_id'], 'after');
            unset($conditions['flight_id']);
        }

        $builder->where($conditions);
        $t = $builder->countAllResults();

        return $t > 0 ? 1 : 0;
    }


    public function updateData($table, $conditions, $data)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $queryStatus = $builder->set($data)
            ->where($conditions)
            ->update();
        if ($queryStatus) {
            return 1;
        } else {
            return 0;
        }
    }

    public function deleteData($table, $conditions)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $builder->where($conditions);
        $queryStatus = $builder->delete();
        if ($queryStatus) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getDataWithJoin($table, $mainTableAlias, $where, $select, $isJoin, $joins, $orderByOn = null, $orderByType = 'DESC', $distinct = null, $like = null, $like_field_name = null, $like_side = 'both', $limit = null, $orWhere = null)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table . ' ' . $mainTableAlias);
        if ($isJoin) {
            foreach ($joins as $value) {
                $builder->join($value['tableName'] . ' as ' . $value['alias'], $value['condition'], $value['type']);
            }
        }
        if ($orderByOn) {
            $builder->orderBy($orderByOn, $orderByType);
        }
        if ($distinct) {
            $builder->distinct()->select($select);
        } else {
            $builder->select($select);
        }
        if ($where) {
            $builder->where($where);
        }
        if ($like) {
            $builder->like($like_field_name, $like, $like_side);
        }
        if ($limit) {
            $builder->limit($limit);
        }
        if ($orWhere) {
            $builder->groupStart();
            $builder->orWhere($orWhere);
            $builder->groupEnd();
        }
        $employee = $builder->get()->getResultArray();
        return $employee;
    }

    public function getRowCount($table, $where)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $builder->select('id')->where($where);
        $count = $builder->countAllResults();
        return $count;
    }

    public function setDataBatch($table, $data)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $insertResult = $builder->insertBatch($data);
        return $insertResult;
    }

    public function addCountWithColumn($table, $where, $column_name)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        $builder->set($column_name, $column_name . '+1', false);
        $builder->where($where);
        $queryStatus = $builder->update();
        if ($queryStatus) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getRowData($table, $where, $select, $orWhere = null, $order_by = null, $order_type = null)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $builder->select($select)
            ->where($where);
        if ($orWhere) {
            $builder->groupStart();
            $builder->orWhere($orWhere);
            $builder->groupEnd();
        }
        if ($order_by) {
            $builder->orderBy($order_by, $order_type);
        }
        $builder->limit(1);
        $builder->orderBy($table . '.id', 'DESC');
        $employee = $builder->get()->getRowArray();
        return $employee;
    }



    public function get_aggregate_data($table, $where, $select, $aggregate_function = 'COUNT')
    {
        // Connect to the database
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        // Apply the where clause
        $builder->where($where);
        // Use a switch-case to handle different aggregate functions
        switch (strtoupper($aggregate_function)) {
            case 'COUNT':
                $builder->selectCount($select);
                break;
            case 'SUM':
                $builder->selectSum($select);
                break;
            case 'AVG':
                $builder->selectAvg($select);
                break;
            case 'MAX':
                $builder->selectMax($select);
                break;
            case 'MIN':
                $builder->selectMin($select);
                break;
        }
        // Set limit and order by clause
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
        // Return the result
        return $result;
    }

    public function updateDataBatch($table, $data, $constraints)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $updateResult = $builder->updateBatch($data, $constraints);
        return $updateResult;
    }

    public function updateDataWhereIn($table, $where_in, $where_in_column, $data)
    {
        $db = \Config\Database::connect('default');
        $builder = $db->table($table);
        $queryStatus = $builder->set($data)
            ->whereIn($where_in_column, $where_in)
            ->update();
        return $queryStatus;
    }
}
