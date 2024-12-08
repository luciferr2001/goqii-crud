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
     * This function is used listing of the users.
     *
     * @return Response Array of data is returned in the response.
     */
    public function listing_user(): Response
    {
        /* Not implementing pagination as of now but pagination is a required thing to make the load on server and database less
        
        Example of pagination i used in other projects

        page=0&size=10&sort_by=phone_number&sort_order=asc&filter_value=

        Below is a sample code which i used for sorting and size

        $sort_order = empty($query_params['sort_order']) ? $default_sort_order : $query_params['sort_order'];
        $size = $query_params['size'] ?? $default_page_size;
        $page = $query_params['page'] ?? $default_page_number;
        $builder->orderBy($sort_by, $sort_order);
        $next_offset = $page * $size;
        $builder->limit($size, $next_offset);

        */
        try {
            /* As i am not updating added_by and updated_by so not showing it */
            $users = $this->common_model->getData(MAIN_USER, array('is_deleted' => NOT_DELETED), 'id,first_name,last_name,email,phone_number,dob,added_on,updated_on,status');
            return $this->response->setJSON($this->makeOutput($users, SUCCESS, DATA_FETCHED_SUCCESSFULLY));
        } catch (\Exception $e) {
            // Set log here for error logging if required
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }

    /**
     * This function is used to add user.
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
            $form_rules = $this->form_fields;
            $form_rules['uuid'] = [
                'label' => 'UUID',
                "rules" => ['required', 'max_length[50]'],
            ];
            // Generate a UUID for the user
            $data['uuid'] = $this->generate_uuidv4();
            // Set 'added_on' field to the current date and time
            $form_rules['added_on'] = [
                'label' => 'Added On',
                "rules" => ['required'],
            ];
            $data['added_on'] = $this->current_date_time;
            /* As of now not updating the added_by as login auth is not created */
            $form_rules['password'] = [
                'label' => 'Password',
                "rules" => ['required', 'max_length[50]'],
            ];
            // Generating random password the user will change the password on first login and if generating random password we can send the password by mail or sms
            $password = $this->generate_password(false);
            // md5 the password
            $data['password'] = md5($password);
            $data['dob'] = date('Y-m-d', strtotime($data['dob']));
            $data = array_intersect_key($data, $form_rules);
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

    /**
     * This function is used to edit a user.
     *
     * @param int $user_id The ID of the user to be edited.
     * @return Response Success or Failure is returned in the response.
     */
    public function edit_user($user_id): Response
    {
        try {
            /* 
            Here i am getting the user id directly from the url without encoding but i have implemented a encoding strategy in the other project to ensure that the id is not exposed to the public.
            */
            // Get the data from the request
            if (!isset($this->data_request) || empty($this->data_request)) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, DATA_REQUIRED));
            }
            $user_data = $this->data_request;
            $check_if_user_is_deleted = $this->common_model->checkRecordExists(array('id' => $user_id, 'is_deleted' => DELETED), MAIN_USER);
            if ($check_if_user_is_deleted) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, USER_DELETED));
            }
            // Start a database transaction
            $db = db_connect();
            $db->transBegin();
            // Check if email field is edited and check for duplicates
            if (isset($user_data->email) || !empty($user_data->email)) {
                $does_email_already_exists = $this->common_model->checkRecordExists(['LOWER(email)' => strtolower($user_data->email), 'is_deleted' => NOT_DELETED, 'id!=' => $user_id], MAIN_USER);
                if ($does_email_already_exists) {
                    return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, EMAIL_ALREADY_USED));
                }
            }
            // Check if phone number field is edited and check for duplicates
            if (isset($user_data->phone_number) || !empty($user_data->phone_number)) {
                $does_phone_number_already_exists = $this->common_model->checkRecordExists(['phone_number' => $user_data->phone_number,  'is_deleted' => NOT_DELETED, 'id!=' => $user_id], MAIN_USER);
                if ($does_phone_number_already_exists) {
                    return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, PHONE_NUMBER_ALREADY_USED));
                }
            }
            $data = (array)$user_data;
            if (empty($data)) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, DATA_REQUIRED));
            }
            if (isset($data['dob'])) {
                $data['dob'] = date('Y-m-d', strtotime($data['dob']));
            }
            $form_rules = $this->form_fields;
            $form_rules = array_intersect_key($this->form_fields, $data);
            if (empty($form_rules)) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, DATA_REQUIRED));
            }
            $form_rules['updated_on'] = [
                'label' => 'Updated On',
                "rules" => ['required'],
            ];
            $data['updated_on'] = $this->current_date_time;
            /* As of now not updating the updated_by as login auth is not created */
            $data = array_intersect_key($data, $form_rules);
            // Update the user data to the database using the CRUD method
            $this->crud->update(MAIN_USER, $user_id, $data, $form_rules);
            // Commit the transaction if everything is successful
            $db->transCommit();
            $db->transComplete();
            return $this->response->setJSON($this->makeOutput([], SUCCESS, USER_UPDATED_SUCCESSFULLY));
        } catch (\Exception $e) {
            // Log the exception if an error occurs
            $this->response->setStatusCode(BAD_REQUEST);
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }

    /**
     * This function is used delete user.
     *
     * @param int $user_id The ID of the user to be deleted.
     * @return Response Success or Failure is returned in the response.
     */
    public function delete_user($user_id): Response
    {
        /* 
            Here i am getting the user id directly from the url without encoding but i have implemented a encoding strategy in the other project to ensure that the id is not exposed to the public.
            */
        try {
            /* Creating a soft delete functionalty to ensure that no data is deleted */
            $check_if_user_is_deleted = $this->common_model->checkRecordExists(array('id' => $user_id, 'is_deleted' => DELETED), MAIN_USER);
            if ($check_if_user_is_deleted) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, USER_DELETED));
            }
            // Start a database transaction
            $db = db_connect();
            $db->transBegin();
            $data = [
                'status' => IN_ACTIVE,
                'is_deleted' => DELETED,
                'deleted_on' => $this->current_date_time
            ];
            $this->crud->delete(MAIN_USER, $user_id, $data);
            // Commit the transaction if everything is successful
            $db->transCommit();
            // Return success response
            return $this->response->setJSON($this->makeOutput([], SUCCESS, USER_DELETED_SUCCESSFULLY));
        } catch (\Exception $e) {
            // Log the exception if an error occurs
            $this->response->setStatusCode(BAD_REQUEST);
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }

    /**
     * This function is used to get particular user details.
     *
     * @return Response Details of user is returned in the response.
     */
    public function detail_user($user_id)
    {
        try {
            $check_if_user_is_deleted = $this->common_model->checkRecordExists(array('id' => $user_id, 'is_deleted' => DELETED), MAIN_USER);
            if ($check_if_user_is_deleted) {
                return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, USER_DELETED));
            }
            $user_details = $this->common_model->getRowData(MAIN_USER, array('id' => $user_id), 'id,first_name,last_name,email,phone_number,dob,added_on,updated_on,status');
            return $this->response->setJSON($this->makeOutput($user_details, SUCCESS, DATA_FETCHED_SUCCESSFULLY));
        } catch (\Exception $e) {
            $this->response->setStatusCode(BAD_REQUEST);
            return $this->response->setJSON($this->makeOutput(array(), FAIL_ERR_CODE, $e->getMessage()));
        }
    }
}
