<?php

defined('BASEPATH') or exit('No direct script access allowed');


class M_user extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getUser()
    {
        $data = $this->db->get('user');
        return $data->result_array();
    }

    public function insertUser($data)
    {

        $this->db->insert('user', $data);
        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('user', array('id' => $insert_id));

        return $result->row_array();
    }

    public function updateUser($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('user', $data);

        $result = $this->db->get_where('user', array('id' => $id));
        return $result->row_array();
    }
    public function deleteUser($id)
    {
        $result = $this->db->get_where('user', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('user');

        return $result->row_array();
    }

    public function cekLoginUser($data)
    {
        $this->db->where($data);
        $result = $this->db->get('user');

        return $result->row_array();
    }

    public function cekUserExist($id)
    {
        $data = array('id' => $id);

        $this->db->where($data);
        $result = $this->db->get('admin');

        if (empty($result->row_array())) {
            return false;
        }
        return true;
    }
}