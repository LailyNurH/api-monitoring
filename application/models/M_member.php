<?php

defined('BASEPATH') or exit('No direct script access allowed');


class M_member extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insertMember($data)
    {

        $this->db->insert('member', $data);
        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('member', array('id' => $insert_id));

        return $result->row_array();
    }

    public function get_membership_by_id($id_user)
    {
        // Assuming you have a table called 'membership' to store membership data
        $this->db->where('id_user', $id_user);
        $query = $this->db->get('member');

        if ($query->num_rows() > 0) {
            return $query->result(); // Return all rows representing the membership data for the provided id_user
        } else {
            return null; // Return null if no membership data found for the provided id_user
        }
    }

    public function get_detail_membership($id)
    {
        // Assuming you have a table called 'membership' to store membership data
        $this->db->where('id', $id);
        $query = $this->db->get('member');

        if ($query->num_rows() > 0) {
            return $query->row(); // Return a single row representing the membership data
        } else {
            return null; // Return null if no membership data found for the provided id_user
        }
    }

}