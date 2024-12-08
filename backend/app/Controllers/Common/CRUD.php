<?php

namespace App\Controllers;

namespace App\Controllers\Common;

use App\Controllers\BaseController;
use App\Traits\CommonTraits;
use App\Models\CommonModel;


class CRUD extends BaseController
{
    use CommonTraits;
    protected $datatable_model;
    protected $client_ip;
    protected $client_agent;
    protected $current_date_time;
    protected $response;
    protected $request;
    protected $common_model;
    protected $set_log_data;
    protected $domain;


    /**
     * This is the constructor used to define global class variables.
     *
     */
    public function __construct()
    {
        $this->response = service('response');
        $this->request = \Config\Services::request();
        $this->common_model = new CommonModel();
    }

    public function add($table_name, $data, $form_fields, $return_id = false)
    {
        try {
            helper(['form']);
            $db = db_connect();
            $db->transBegin();
            $validation =  \Config\Services::validation();
            if (empty($form_fields)) {
                throw new \Exception(MASTER_FORM_EMPTY);
            }
            $validation->reset();
            foreach ($form_fields as $key => $value) {
                $validation->setRule($key, $value['label'], $value['rules']);
            }
            if (!$validation->run($data)) {
                $error = "";
                $errors = $validation->getErrors();
                foreach ($errors as $key => $value) {
                    $error .= " " . $value;
                }
                // Set Log here
                throw new \Exception(trim($error));
            }
            $add_data = $this->common_model->setData($table_name, $data, $return_id);
            if (!$add_data) {
                $db->transRollback();
                $db->transComplete();
                throw new \Exception(DATABASE_ERROR);
            }
            if ($return_id) {
                $db->transCommit();
                $db->transComplete();
                return $add_data;
            }
            $db->transCommit();
            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update($table_name, $id, $data, $form_fields)
    {
        try {
            helper(['form']);
            $db = db_connect();
            $db->transBegin();
            $validation =  \Config\Services::validation();
            if (empty($form_fields)) {
                throw new \Exception(MASTER_FORM_EMPTY);
            }
            $validation->reset();
            foreach ($form_fields as $key => $value) {
                $validation->setRule($key, $value['label'], $value['rules']);
            }
            if (!$validation->run($data)) {
                $error = "";
                $errors = $validation->getErrors();
                foreach ($errors as $key => $value) {
                    $error .= " " . $value;
                }
                // Set Log here
                throw new \Exception(trim($error));
            }
            $update_data = $this->common_model->updateData($table_name, array('id' => $id), $data,);
            if (!$update_data) {
                $db->transRollback();
                $db->transComplete();
                throw new \Exception(DATABASE_ERROR);
            }
            $db->transCommit();
            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($table_name, $id, $data)
    {
        try {
            $db = db_connect();
            $db->transBegin();
            $delete_data = $this->common_model->updateData($table_name, array('id' => $id), $data);
            if (!$delete_data) {
                $db->transRollback();
                $db->transComplete();
                throw new \Exception(DATABASE_ERROR);
            }
            $db->transCommit();
            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
