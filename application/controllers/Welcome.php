<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('welcome_model');
        $this->load->helper('security');
        $this->load->library('form_validation');
    }
    public function index() {
        $this->load->view('welcome_message');
    }
    public function action() {
        if ($this->input->post('data_action')) {
            $data_action = $this->input->post('data_action');

            // Insert & Update
            if ($data_action == 'Insert' || $data_action == 'Update') {
                // debug($this->input->post());exit;
                $this->form_validation->set_rules('employee_name', 'Employee Name', 'trim|required|xss_clean');
                if ($data_action == 'Insert') {
                    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[tbl_employees.email]|xss_clean');
                } else {
                    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
                }
                $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|regex_match[/^[0-9]{10}$/]|xss_clean');
                $this->form_validation->set_rules('designation', 'Designation', 'trim|required|xss_clean|xss_clean');
                $this->form_validation->set_rules('roles[]', 'Roles', 'trim|required|xss_clean');
                if (empty($_FILES['photo']['name']) && empty($this->input->post('hidden_photo'))) {
                    $this->form_validation->set_rules('photo', 'Photo', 'required|xss_clean');
                }
                if ($this->form_validation->run()) {
                    $roles = implode(',', $this->input->post('roles'));
                    $origin = $this->input->post('origin');
                    if ($origin) {
                        if (empty($_FILES['photo']['name']) && !empty($this->input->post('hidden_photo'))) {
                            $post_data['photo'] = $this->input->post('hidden_photo');
                        } else {
                            // File uploading through helper
                            $file_data = file_upload('photo');
                            $file_name = $file_data['upload_data']['file_name'];
                            $post_data['photo'] = $file_name;
                        }     
                        $data = array('employee_name' => $this->input->post('employee_name'), 'email' => $this->input->post('email'), 'mobile' => $this->input->post('mobile'), 'designation' => $this->input->post('designation'), 'roles' => $roles, 'photo' => $post_data['photo']);
                        $this->welcome_model->update_employee($data,$origin);
                    	$array = array('success' => true, 'msg' => 'Data Updated');
                    } else {
                        // Employee Code
                        $employee_code = 'Employee-' . time();
                        // File uploading through helper
                        $file_data = file_upload('photo');
                        $file_name = $file_data['upload_data']['file_name'];
                        $post_data['photo'] = $file_name;
                        // echo $post_data['photo'];exit;
                        $data = array('employee_code' => $employee_code, 'employee_name' => $this->input->post('employee_name'), 'email' => $this->input->post('email'), 'mobile' => $this->input->post('mobile'), 'designation' => $this->input->post('designation'), 'roles' => $roles, 'photo' => $post_data['photo']);
                        $this->welcome_model->insert_employee($data);
                        $array = array('success' => true, 'msg' => 'Data Inserted');
                    }
                } else {
                    $array = array('error' => true, 'employee_name_error' => form_error('employee_name'), 'email_error' => form_error('email'), 'mobile_error' => form_error('mobile'), 'designation_error' => form_error('designation'), 'roles_error' => form_error('roles[]'), 'photo_error' => form_error('photo'));
                }
                echo json_encode($array);
            }

            // Fetch Single User Data
            if ($data_action == 'fetch_single_user_data') {
                $origin = $this->input->post('origin');
                $result = $this->welcome_model->fetch_single_user_data($origin);
                $result[0]['roles'] = explode(',', $result[0]['roles']);
                echo json_encode($result[0]);
            }

            // Delete
			if ($data_action == 'Delete') {
				$origin = $this->input->post('origin');
				$result = $this->welcome_model->delete_employee($origin);
				if ($result) {
					$array=array('success'=>true,'msg'=>'Deleted');
				}
				else{
					$array=array('error'=>true);
				}
				echo json_encode($array);
					}
        }
    }

    // Fetching employees data
    public function fetch_employees(){
    	$fetch_data=$this->welcome_model->make_datatables();
    	$data=array();
    	$i=1;
    	foreach ($fetch_data as $row) {
    		$sub_array = array();
    		$sub_array[] = $i;
    		$sub_array[] = $row->employee_code;
    		$sub_array[] = $row->employee_name;
    		$sub_array[] = $row->email;
    		$sub_array[] = $row->mobile;
    		$sub_array[] = $row->designation;
    		$sub_array[] = $row->roles;
    		$sub_array[] = '<img src="' . base_url() . display_path . $row->photo . '"  height="50" width="100">';
    		$sub_array[] = '<button type="button" name="edit" class="btn btn-warning btn-xs edit" id="' . $row->origin . '">Edit</button>';
    		$sub_array[] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="' . $row->origin . '">Delete</button>';
    		$data[] = $sub_array;
    	$i++;
    	}

    	$output = array(
    			"draw" => intval($_POST['draw']),
    			"recordsTotal" => $this->welcome_model->get_all_data(),
    			"recordsFlitered" => $this->welcome_model->get_filtered_data(),
    			"data" => $data
    	);

    	echo json_encode($output);
    }
}
