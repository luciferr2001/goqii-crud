<?php

namespace App\Controllers;

use App\Models\CommonModel;

class Home extends BaseController
{

    protected $common_model;
    protected $current_date_time;
    protected $response;
    protected $request;


    public function __construct()
    {
        $this->response = service('response');
        $this->request = \Config\Services::request();
        $this->common_model = new CommonModel();
        $this->current_date_time = date("Y-m-d H:i:s");
    }

    public function index(): string
    {
        return view('welcome_message');
    }

    // Write any common logic for the whole application
}
