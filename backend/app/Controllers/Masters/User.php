<?php

namespace App\Controllers\Masters;

use App\Controllers\Common\CRUD;
use App\Controllers\Home;
use App\Traits\CommonTraits;
use CodeIgniter\HTTP\Response;

class User extends Home
{
    use CommonTraits;

    protected $data_request;
    protected $crud;

    public function __construct()
    {
        parent::__construct();
        $this->data_request = $this->request->getJSON();
        $this->crud = new CRUD();
    }

    public function index(): string
    {
        return view('welcome_message');
    }

    protected $form_fields = [
        "first_name" => [
            "label" => "First Name",
            "rules" => ["required", "max_length[50]", "regex_match[^[^\s][\w',.\-\s][^0-9_!¡?÷?¿\/\\+=@#$%&*(){}|~<>;:[\]]{0,}[^\s]$]"],
            "type" => "input",
        ],
        "last_name" => [
            "label" => "Last Name",
            "rules" => ["required", "max_length[50]", "regex_match[^[^\s][\w',.\-\s][^0-9_!¡?÷?¿\/\\+=@#$%&*(){}|~<>;:[\]]{0,}[^\s]$]"],
            "type" => "input",
        ],
        "email" => [
            "label" => "Email",
            "rules" => ["required", "regex_match[^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$]"],
            "type" => "input",
        ],
        "phone_number" => [
            "label" => "Phone Number",
            "rules" => ["required", "max_length[10]", "min_length[10]", "regex_match[^[0-9]+$]"],
            "type" => "input",
        ],
        "dob" => [
            "label" => "Date Of Birth",
            "rules" => ["required"],
            "type" => "date",
        ],
    ];

    /**
     * This function is used get form of the user master.
     *
     * @return Response Array of data is returned in the response.
     */
    public function master_form_user(): Response
    {
        try {
            if (empty($this->form_fields)) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, MASTER_FORM_EMPTY));
            }
            return $this->response->setJSON($this->makeOutput($this->form_fields, SUCCESS, DATA_FETCHED_SUCCESSFULLY));
        } catch (\Exception $e) {
            // Set log here for error logging if required
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }

    /**
     * This function is used add user.
     *
     * @return Response Success or Failure is returned in the response.
     */
    public function add_user(): Response
    {
        try {
        // Get the data from the request
        if (!isset($this->data_request) || empty($this->data_request)) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, DATA_REQUIRED));
        }
        $user_data = $this->data_request;
        // Start a database transaction
        $db = db_connect();
        $db->transBegin();
        // Check if email is present and not empty
        if (!isset($user_data->email) || empty($user_data->email)) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, EMAIL_IS_REQUIRED));
        }
        $does_email_already_exists = $this->common_model->checkRecordExists(['LOWER(email)' => strtolower($user_data->email), 'is_deleted' => NOT_DELETED], MAIN_USER);
        if ($does_email_already_exists) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, EMAIL_ALREADY_USED));
        }
        // Check if phone_number is present and not empty
        if (!isset($user_data->phone_number) || empty($user_data->phone_number)) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, PHONE_NUMBER_IS_REQUIRED));
        }
        $does_phone_number_already_exists = $this->common_model->checkRecordExists(['phone_number' => $user_data->phone_number,  'is_deleted' => NOT_DELETED], MAIN_USER);
        if ($does_phone_number_already_exists) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, PHONE_NUMBER_ALREADY_USED));
        }
        $data = (array)$user_data;

        if (empty($data)) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, DATA_REQUIRED));
        }
        // Prepare form rules excluding role_id
        $form_rules = $this->form_fields;
        // Check if both first_name and last_name are missing
        if ((!isset($user_data->first_name) || empty($user_data->first_name)) || (!isset($user_data->last_name) || empty($user_data->last_name))) {
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, FIRST_LAST_NAME_REQUIRED));
        }
        // Create a 'full_name' field combining first_name and last_name
        $form_rules['full_name'] = [
            'label' => 'Full Name',
            "rules" => ['required', 'max_length[50]'],
        ];
        $data['full_name'] = "{$user_data->first_name} {$user_data->last_name}";
        // Generate a UUID for the user
        $form_rules['uuid'] = [
            'label' => 'UUID',
            "rules" => ['required', 'max_length[50]'],
        ];
        $data['uuid'] = $this->generate_uuidv4();
        // Set 'added_on' field to the current date and time
        $form_rules['added_on'] = [
            'label' => 'Added On',
            "rules" => ['required'],
        ];
        $data['added_on'] = $this->current_date_time;
        $form_rules['password'] = [
            'label' => 'Password',
            "rules" => ['required', 'max_length[50]'],
        ];
        // Generating random password the user will change the password on first login and if generating random password we can send the password by mail or sms
        $password = $this->generate_password(false);
        // md5 the password
        $data['password'] = md5($password);
        $data['dob']= date('Y-m-d', strtotime($data['dob']));
        // Add the user data to the database using the CRUD method
        $user_id = $this->crud->add(MAIN_USER, $data, $form_rules, true);
        /* With Returned user id we can map any role to the user */
        // Commit the transaction if everything is successful
        $db->transCommit();
        $db->transComplete();
        // Write a mailer logic if required
        return $this->response->setJSON($this->makeOutput([], SUCCESS, USER_CREATED_SUCCESSFULLY));
        } catch (\Exception $e) {
            // Log the exception if an error occurs
            $this->response->setStatusCode(BAD_REQUEST);
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }
}
