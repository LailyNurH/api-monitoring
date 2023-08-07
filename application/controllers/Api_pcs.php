<?php

defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');
require APPPATH . '/libraries/Firebase/JWT/JWT.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use \Firebase\JWT\JWT;

use Restserver\Libraries\REST_Controller;

class Api_pcs extends REST_Controller
{

    private $secret_key = "dsagdfg4353rtregmfdgo";

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_user');
        $this->load->model('M_produk');
        $this->load->model('M_member');


    }

    public function user_get()
    {
        $result = $this->M_user->getuser();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "user" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function user_post()
    {

        $validation_message = [];

        if ($this->input->post("email") == "") {
            array_push($validation_message, "Email tidak boleh kosong");
        }

        if ($this->input->post("email") != "" && !filter_var($this->input->post("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email tidak valid");
        }

        if ($this->input->post("password") == "") {
            array_push($validation_message, "Password tidak boleh kosong");
        }

        if ($this->input->post("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        // Jika Lolos Validasi
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password")),
            "nama" => $this->input->post("nama"),
            "role" => $this->input->post("role")

        );

        $result = $this->M_user->insertUser($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "user" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function user_put()
    {
        $this->cekToken();
        //Validasi
        $validation_message = [];

        if ($this->put("email") == "") {
            array_push($validation_message, "Email tidak boleh kosong");
        }

        if ($this->put("email") != "" && !filter_var($this->put("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email tidak valid");
        }

        if ($this->put("password") == "") {
            array_push($validation_message, "Password tidak boleh kosong");
        }

        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Jika Lolos Validasi
        $data = array(
            "email" => $this->put("email"),
            "password" => md5($this->put("password")),
            "nama" => $this->put("nama")
        );

        $id = $this->put("id");

        $result = $this->M_user->updateUser($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "user" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function user_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_user->deleteUser($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "user" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function login_post()
    {
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password"))
        );

        $result = $this->M_user->cekLoginUser($data);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Email dan Password tidak valid",
                "error_code" => 1308,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        } else {
            $date = new Datetime();

            $payload["id"] = $result["id"];
            $payload["email"] = $result["email"];
            $payload["iat"] = $date->getTimestamp();
            $payload["exp"] = $date->getTimestamp() + 3600;

            $data_json = array(
                "success" => true,
                "message" => "Otentikasi Berhasil",
                "data" => array(
                    "user" => $result,
                    "token" => JWT::encode($payload, $this->secret_key)
                )

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        }
    }
    // // API Admin End


    public function cekToken()
    {
        try {
            $token = $this->input->get_request_header('Authorization');

            if (!empty($token)) {
                $token = explode(' ', $token)[1];
            }

            $token_decode = JWT::decode($token, $this->secret_key, array('HS256'));
        } catch (Exception $e) {
            $data_json = array(
                "success" => false,
                "message" => "Token tidak valid",
                "error_code" => 1204,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }
    public function member_post()
    {

        //Jika Lolos Validasi
        $data = array(
            "id_user" => $this->input->post("id_user"),
            "nama" => $this->input->post("nama"),
            "alamat" => $this->input->post("alamat"),
            "no_hp" => $this->input->post("no_hp"),
            "no_ktp" => $this->input->post("no_ktp"),
            "pekerjaan" => $this->input->post("pekerjaan"),
            "jenis_berlangganan" => $this->input->post("jenis_berlangganan"),
            "guna_berlangganan" => $this->input->post("guna_berlangganan"),
            "pembayaran_muka" => $this->input->post("pembayaran_muka"),
            "tgl_berlangganan" => $this->input->post("tgl_berlangganan"),
            "jumlah_unit" => $this->input->post("jumlah_unit"),
            "status_verifikasi" => $this->input->post("status_verifikasi"),
            "status_bayar" => $this->input->post("status_bayar")
        );

        $result = $this->M_member->insertMember($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "member" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function member_get($id_user)
    {
        $result = $this->M_member->get_membership_by_id($id_user);

        if ($result !== null) {
            $data_json = array(
                "success" => true,
                "message" => "Data found",
                "data" => array(
                    "membership" => $result
                )
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        } else {
            $data_json = array(
                "success" => false,
                "message" => "Data not found for the provided id_user",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function detailmember_get($id)
    {
        $result = $this->M_member->get_detail_membership($id);

        if ($result !== null) {
            $data_json = array(
                "success" => true,
                "message" => "Data found",
                "data" => array(
                    "membership" => $result
                )
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        } else {
            $data_json = array(
                "success" => false,
                "message" => "Data not found for the provided id_user",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_NOT_FOUND);
        }
    }
}