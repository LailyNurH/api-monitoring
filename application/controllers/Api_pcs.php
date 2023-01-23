<?php

defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');
require APPPATH . '/libraries/Firebase/JWT/JWT.php';

use \Firebase\JWT\JWT;

use Restserver\Libraries\REST_Controller;

class Api_pcs extends REST_Controller
{

    private $secret_key = "dsagdfg4353rtregmfdgo";

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_admin');
        $this->load->model('M_produk');
        $this->load->model('M_transaksi');
        $this->load->model('M_item_transaksi');
        
        $this->load->model('M_pembelian');
    $this->load->model('M_item_pembelian');
        
    }

    public function admin_get()
    {
        $result = $this->M_admin->getAdmin();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function admin_post()
    {
        $this->cekToken();
        //Validasi
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

        //Jika Lolos Validasi
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password")),
            "nama" => $this->input->post("nama")
        );

        $result = $this->M_admin->insertAdmin($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function admin_put()
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

        $result = $this->M_admin->updateAdmin($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function admin_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_admin->deleteAdmin($id);

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
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function login_post()
    {
        $data = array(
            "email" => $this->input->post("email"),
            "password" =>  md5($this->input->post("password"))
        );

        $result = $this->M_admin->cekLoginAdmin($data);

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
                    "admin" => $result,
                    "token" => JWT::encode($payload, $this->secret_key)
                )

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        }
    }
    // // API Admin End

    // // API Product Start
    public function produk_get()
    {
        $this->cekToken();

        $result = $this->M_produk->getProduk();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function produk_post()
    {
        $this->cekToken();
        //Validasi
        // $validation_message = [];

        // if ($this->input->post("admin_id") == "") {
        //     array_push($validation_message, "Admin ID  tidak boleh kosong");
        // }

        // if ($this->input->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
        //     array_push($validation_message, "Admin ID  tidak ditemukan");
        // }

        // if ($this->input->post("nama") == "") {
        //     array_push($validation_message, "Nama tidak boleh kosong");
        // }

        // if ($this->input->post("harga") == "") {
        //     array_push($validation_message, "Harga tidak boleh kosong");
        // }
        // if ($this->input->post("harga") != "" && !is_numeric($this->input->post("harga"))) {
        //     array_push($validation_message, "Harga Harus berisi Angka ");
        // }

        // if ($this->input->post("stok") == "") {
        //     array_push($validation_message, "Stok tidak boleh kosong");
        // }
        // if ($this->input->post("stok") != "" && !is_numeric($this->input->post("stok"))) {
        //     array_push($validation_message, "Stok harus Angka");
        // }

        // if (count($validation_message) > 0) {
        //     $data_json = array(
        //         "success" => false,
        //         "message" => "Data tidak valid",
        //         "data" => $validation_message
        //     );

        //     $this->response($data_json, REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }

        //Jika Lolos Validasi
        $data = array(
            "admin_id" => $this->input->post("admin_id"),
            "nama" => $this->input->post("nama"),
            "harga" => $this->input->post("harga"),
            "stok" => $this->input->post("stok")
        );

        $result = $this->M_produk->insertProduk($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function produk_put()
    {
        $this->cekToken();
        //Validasi
        // if ($this->put("admin_id") == "") {
        //     array_push($validation_message, "Admin ID  tidak boleh kosong");
        // }

        // if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
        //     array_push($validation_message, "Admin ID  tidak ditemukan");
        // }

        // if ($this->put("nama") == "") {
        //     array_push($validation_message, "Nama tidak boleh kosong");
        // }

        // if ($this->put("harga") == "") {
        //     array_push($validation_message, "Harga tidak boleh kosong");
        // }
        // if ($this->input->post("harga") != "" && !is_numeric($this->put("harga"))) {
        //     array_push($validation_message, "Harga Harus berisi Angka ");
        // }

        // if ($this->put("stok") == "") {
        //     array_push($validation_message, "Stok tidak boleh kosong");
        // }
        // if ($this->put("stok") != "" && !is_numeric($this->put("stok"))) {
        //     array_push($validation_message, "Stok harus Angka");
        // }

        // if (count($validation_message) > 0) {
        //     $data_json = array(
        //         "success" => false,
        //         "message" => "Data tidak valid",
        //         "data" => $validation_message
        //     );

        //     $this->response($data_json, REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }

        //Jika Lolos Validasi
        $data = array(
            "admin_id" => $this->put("admin_id"),
            "nama" => $this->put("nama"),
            "harga" => $this->put("harga"),
            "stok" => $this->put("stok")
        );

        $id = $this->put("id");

        $result = $this->M_produk->updateProduk($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function produk_delete()
    {
        // $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_produk->deleteProduk($id);

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
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // // API Product End


    // // API Transaksi Start


    public function transaksi_get()
    {
        $this->cekToken();

        $result = $this->M_transaksi->getTransaksi();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function transaksi_post()
    {
        $this->cekToken();
        //Validasi
        $validation_message = [];

        if ($this->input->post("admin_id") == "") {
            array_push($validation_message, "Admin ID  tidak boleh kosong");
        }

        if ($this->input->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
            array_push($validation_message, "Admin ID  tidak ditemukan");
        }

        if ($this->input->post("total") == "") {
            array_push($validation_message, "Total tidak boleh kosong");
        }
        if ($this->input->post("total") != "" && !is_numeric($this->input->post("total"))) {
            array_push($validation_message, "Total Harus berisi Angka ");
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
            "admin_id" => $this->input->post("admin_id"),
            "total" => $this->input->post("total"),
            "tanggal" => date("y-m-d H:i:s")
        );

        $result = $this->M_transaksi->insertTransaksi($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function transaksi_put()
    {
        $this->cekToken();
        //Validasi
        $validation_message = [];



        if ($this->put("admin_id") == "") {
            array_push($validation_message, "Admin ID  tidak boleh kosong");
        }

        if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "Admin ID  tidak ditemukan");
        }

        if ($this->put("total") == "") {
            array_push($validation_message, "Total tidak boleh kosong");
        }
        if ($this->put("total") != "" && !is_numeric($this->put("total"))) {
            array_push($validation_message, "Total Harus berisi Angka ");
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
            "admin_id" => $this->put("admin_id"),
            "total" => $this->put("total"),
            "tanggal" => date("y-m-d H:i:s")
        );

        $id = $this->put("id");

        $result = $this->M_transaksi->updateTransaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function transaksi_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_transaksi->deleteTransaksi($id);

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
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //API Transaksi End

    // GET TRANSAKSI BULAN SAAT INI

    public function transaksi_bulan_ini_get()
    {
        $this->cekToken();

        $result = $this->M_transaksi->getTransaksiBulanIni();

        $total = 0;
        foreach($result as $row){
            $total = $total + $row['total'];

        }

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "total" => $total,
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    // // API Item Transaksi Start


    public function item_transaksi_by_transaksi_id_get()
    {
        $this->cekToken();

        $result = $this->M_item_transaksi->getItemTransaksiByTransaksiID($this->input->get('transaksi_id'));

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_get()
    {
        $this->cekToken();

        $result = $this->M_item_transaksi->getItemTransaksi();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item transasi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function item_transaksi_post()
    {
        $this->cekToken();
        //Validasi
        $validation_message = [];

        if ($this->input->post("transaksi_id") == "") {
            array_push($validation_message, "Transaksi ID  tidak boleh kosong");
        }

        if ($this->input->post("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->input->post("transaksi_id"))) {
            array_push($validation_message, "Transaksi ID  tidak ditemukan");
        }
        if ($this->input->post("produk_id") == "") {
            array_push($validation_message, "Produk ID  tidak boleh kosong");
        }

        if ($this->input->post("produk_id") != "" && !$this->M_produk->cekProdukExist($this->input->post("produk_id"))) {
            array_push($validation_message, "Produk ID  tidak ditemukan");
        }

        if ($this->input->post("qty") == "") {
            array_push($validation_message, "Total tidak boleh kosong");
        }
        if ($this->input->post("qty") != "" && !is_numeric($this->input->post("qty"))) {
            array_push($validation_message, "Total Harus berisi Angka ");
        }

        if ($this->input->post("harga_saat_transaksi") == "") {
            array_push($validation_message, "Harga Saat Transaksi tidak boleh kosong");
        }
        if ($this->input->post("harga_saat_transaksi") != "" && !is_numeric($this->input->post("harga_saat_transaksi"))) {
            array_push($validation_message, "Harga Saat Transaksi Harus berisi Angka ");
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
            "transaksi_id" => $this->input->post("transaksi_id"),
            "produk_id" => $this->input->post("produk_id"),
            "qty" => $this->input->post("qty"),
            "harga_saat_transaksi" => $this->input->post("harga_saat_transaksi"),
            "sub_total" => $this->input->post("qty") * $this->input->post("harga_saat_transaksi")
        );

        $result = $this->M_item_transaksi->insertItemTransaksi($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function item_transaksi_put()
    {
        $this->cekToken();
        //Validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, " ID  tidak boleh kosong");
        }
        if ($this->put("transaksi_id") == "") {
            array_push($validation_message, "Transaksi ID  tidak boleh kosong");
        }

        if ($this->put("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->put("transaksi_id"))) {
            array_push($validation_message, "Transaksi ID  tidak ditemukan");
        }
        if ($this->put("produk_id") == "") {
            array_push($validation_message, "Produk ID  tidak boleh kosong");
        }

        if ($this->put("produk_id") != "" && !$this->M_produk->cekProdukExist($this->put("produk_id"))) {
            array_push($validation_message, "Produk ID  tidak ditemukan");
        }

        if ($this->put("qty") == "") {
            array_push($validation_message, "Total tidak boleh kosong");
        }
        if ($this->put("qty") != "" && !is_numeric($this->put("qty"))) {
            array_push($validation_message, "Total Harus berisi Angka ");
        }

        if ($this->put("harga_saat_transaksi") == "") {
            array_push($validation_message, "Harga Saat Transaksi tidak boleh kosong");
        }
        if ($this->put("harga_saat_transaksi") != "" && !is_numeric($this->put("harga_saat_transaksi"))) {
            array_push($validation_message, "Harga Saat Transaksi Harus berisi Angka ");
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
            "transaksi_id" => $this->put("transaksi_id"),
            "produk_id" => $this->put("produk_id"),
            "qty" => $this->put("qty"),
            "harga_saat_transaksi" => $this->put("harga_saat_transaksi"),
            "sub_total" => $this->put("qty") * $this->put("harga_saat_transaksi")
        );

        $id = $this->put("id");

        $result = $this->M_item_transaksi->updateitemTransaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function item_transaksi_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_item_transaksi->deleteItemTransaksi($id);

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
                "item transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_delete()
    {
        $this->cekToken();

        $transaksi_id = $this->delete("transaksi_id");

        $result = $this->M_item_transaksi->deleteItemTransaksiByTransaksiID($transaksi_id);

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
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //API Transaksi End

    /*
    // API PEMBELIAN START
    */

    public function pembelian_get()
    {
        $result = $this->M_pembelian->getPembelian();

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "pembelian"=> $result
            )
        );
        $this->response($data_json,REST_Controller::HTTP_OK);
    }

    public function pembelian_post()
    {
        $this->cekToken();

        $data= array(
            "id_produk" => $this->input->post("id_produk"),
            "namasupplier" => $this->input->post("namasupplier"),
            "tanggal" => date("y-m-d H:i:s")
        );
        $result = $this->M_pembelian->insertPembelian($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Data Berhasil",
            "data" => array(
                "pembelian"=> $result
            )
        );
        $this->response($data_json,REST_Controller::HTTP_OK);
    }

public function pembelian_put()
    {
        $this->cekToken();
        //Validasi
        // $validation_message = [];



        // if ($this->put("admin_id") == "") {
        //     array_push($validation_message, "Admin ID  tidak boleh kosong");
        // }

        // if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
        //     array_push($validation_message, "Admin ID  tidak ditemukan");
        // }

        // if ($this->put("total") == "") {
        //     array_push($validation_message, "Total tidak boleh kosong");
        // }
        // if ($this->put("total") != "" && !is_numeric($this->put("total"))) {
        //     array_push($validation_message, "Total Harus berisi Angka ");
        // }

        // if (count($validation_message) > 0) {
        //     $data_json = array(
        //         "success" => false,
        //         "message" => "Data tidak valid",
        //         "data" => $validation_message
        //     );

        //     $this->response($data_json, REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }

        //Jika Lolos Validasi
        $data = array(
            "id_produk" => $this->put("id_produk"),
            "namasupplier" => $this->put("namasupplier"),
            "tanggal" => date("y-m-d H:i:s"),
            "id_admin" => $this->put("id_admin")
        );

        $id = $this->put("id");

        $result = $this->M_pembelian->updatePembelian($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function pembelian_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_pembelian->deletePembelian($id);

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
                "pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    /*
    Pembelian End
    */

    
    /*
   ITEM Pembelian START
    */
        public function delete_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_produk->deletePembelian($id);

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
                "pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    /*
    ITEM PEMBELIAN START
    */

     public function item_pembelian_by_pembelian_id_get()
    {
        $this->cekToken();

        $result = $this->M_item_transaksi->getItemPembelianByPembelianID($this->input->get('transaksi_id'));

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_pembelian_get()
    {
        $this->cekToken();

        $result = $this->M_item_pembelian->getItemPembelian();

        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }


    public function item_pembelian_post()
    {
        $this->cekToken();
        //Validasi
        // $validation_message = [];

        // if ($this->input->post("transaksi_id") == "") {
        //     array_push($validation_message, "Transaksi ID  tidak boleh kosong");
        // }

        // if ($this->input->post("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->input->post("transaksi_id"))) {
        //     array_push($validation_message, "Transaksi ID  tidak ditemukan");
        // }
        // if ($this->input->post("produk_id") == "") {
        //     array_push($validation_message, "Produk ID  tidak boleh kosong");
        // }

        // if ($this->input->post("produk_id") != "" && !$this->M_produk->cekProdukExist($this->input->post("produk_id"))) {
        //     array_push($validation_message, "Produk ID  tidak ditemukan");
        // }

        // if ($this->input->post("qty") == "") {
        //     array_push($validation_message, "Total tidak boleh kosong");
        // }
        // if ($this->input->post("qty") != "" && !is_numeric($this->input->post("qty"))) {
        //     array_push($validation_message, "Total Harus berisi Angka ");
        // }

        // if ($this->input->post("harga_saat_transaksi") == "") {
        //     array_push($validation_message, "Harga Saat Transaksi tidak boleh kosong");
        // }
        // if ($this->input->post("harga_saat_transaksi") != "" && !is_numeric($this->input->post("harga_saat_transaksi"))) {
        //     array_push($validation_message, "Harga Saat Transaksi Harus berisi Angka ");
        // }



        // if (count($validation_message) > 0) {
        //     $data_json = array(
        //         "success" => false,
        //         "message" => "Data tidak valid",
        //         "data" => $validation_message
        //     );

        //     $this->response($data_json, REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }

        //Jika Lolos Validasi
        $data = array(
            "pembelian_id" => $this->input->post("pembelian_id"),
            "produk_id" => $this->input->post("produk_id"),
            "qty" => $this->input->post("qty") );

        $result = $this->M_item_pembelian->insertItemPembelian($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
public function item_pembelian_put()
    {
        $this->cekToken();
        //Validasi
        // $validation_message = [];

        // if ($this->put("id") == "") {
        //     array_push($validation_message, " ID  tidak boleh kosong");
        // }
        // if ($this->put("transaksi_id") == "") {
        //     array_push($validation_message, "Transaksi ID  tidak boleh kosong");
        // }

        // if ($this->put("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->put("transaksi_id"))) {
        //     array_push($validation_message, "Transaksi ID  tidak ditemukan");
        // }
        // if ($this->put("produk_id") == "") {
        //     array_push($validation_message, "Produk ID  tidak boleh kosong");
        // }

        // if ($this->put("produk_id") != "" && !$this->M_produk->cekProdukExist($this->put("produk_id"))) {
        //     array_push($validation_message, "Produk ID  tidak ditemukan");
        // }

        // if ($this->put("qty") == "") {
        //     array_push($validation_message, "Total tidak boleh kosong");
        // }
        // if ($this->put("qty") != "" && !is_numeric($this->put("qty"))) {
        //     array_push($validation_message, "Total Harus berisi Angka ");
        // }

        // if ($this->put("harga_saat_transaksi") == "") {
        //     array_push($validation_message, "Harga Saat Transaksi tidak boleh kosong");
        // }
        // if ($this->put("harga_saat_transaksi") != "" && !is_numeric($this->put("harga_saat_transaksi"))) {
        //     array_push($validation_message, "Harga Saat Transaksi Harus berisi Angka ");
        // }


        // if (count($validation_message) > 0) {
        //     $data_json = array(
        //         "success" => false,
        //         "message" => "Data tidak valid",
        //         "data" => $validation_message
        //     );

        //     $this->response($data_json, REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }

        //Jika Lolos Validasi
        $data = array(
             "pembelian_id" => $this->put("pembelian_id"),
            "produk_id" => $this->put("produk_id"),
            "qty" => $this->put("qty") 
        );

        $id = $this->put("id");

        $result = $this->M_item_pembelian->updateitemTransaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function item_pembelian_delete()
    {
        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_item_pembelian->deleteItemPembelian($id);

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
                "item transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_pembelian_by_pembelian_id_delete()
    {
        $this->cekToken();

        $pembelian_id = $this->delete("pembelian_id");

        $result = $this->M_item_pembelian->deleteItemPembelianByPembelianID($pembelian_id);

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
                "item pembelian" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
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
}
