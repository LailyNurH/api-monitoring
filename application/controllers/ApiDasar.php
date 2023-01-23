<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiDasar extends CI_Controller {


	// public function index()
	// {
    // $this->load->view('welcome_message');
       
    // }
    
    public function getAdmin()
    {
        $this->load->model('ModelAdmin');
        $data_admin=$this->ModelAdmin->getAdminData();
        $result = array(
            "success" => true,
            "message" => "Data Found",
            "data"=> $data_admin
        );
        echo json_encode($result);
    }
}
