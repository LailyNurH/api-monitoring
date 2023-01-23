<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pembelian extends CI_Model
{
 function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getPembelian()
    {

        $this->db->select('pembelian.id,produk.nama as nama_produk,pembelian.namasupplier,tanggal,pembelian.stok');
        $this->db->from('pembelian');
        $this->db->join('produk', 'produk.id = pembelian.id_produk');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function insertPembelian($data)
    {

        $this->db->insert('pembelian', $data);
        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('pembelian', array('id' => $insert_id));

        return $result->row_array();
    }

    public function updatePembelian($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('pembelian', $data);

        $result = $this->db->get_where('pembelian', array('id' => $id));
        return $result->row_array();
    }
    public function deletePembelian($id)
    {
        $result = $this->db->get_where('pembelian', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('pembelian');

        return $result->row_array();
    }



}

