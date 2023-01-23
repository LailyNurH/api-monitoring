<?php

defined('BASEPATH') or exit('No direct script access allowed');


class M_item_pembelian extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function getItemPembelian()
    {
        $this->db->select('item_pembelian.id, item_pembelian.pembelian_id, produk.nama, item_pembelian.qty');
        $this->db->from('item_pembelian');
        $this->db->join('produk', 'produk.id = item_pembelian.produk_id');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getItemTransaksiByTransaksiID($pembelian_id)
    {
        $this->db->select('item_pembelian.id, item_pembelian.pembelian_id, produk.nama, item_pembelian.qty');
        $this->db->from('item_pembelian');
        $this->db->join('produk', 'produk.id = item_pembelian.produk_id');
        $this->db->where('item_pembelian.pembelian_id', $pembelian_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function insertItemPembelian($data)
    {

        $this->db->insert('item_pembelian', $data);

        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('item_pembelian', array('id' => $insert_id));

        //Code Untuk mengubah Stok Barang
        $result_produk = $this->db->get_where('produk', array('id' => $data["produk_id"]));
        $result_produk = $result_produk->row_array();
        $stok_lama = $result_produk["stok"];
        $stokbaru = $stok_lama + $data["qty"];

        $data_produk_update = array(
            "stok" => $stokbaru
        );
        $this->db->where('id', $data["produk_id"]);
        $this->db->update('produk', $data_produk_update);



        return $result->row_array();
    }

    public function updateItemTransaksi($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('item_pembelian', $data);

        $result = $this->db->get_where('item_pembelian', array('id' => $id));
        return $result->row_array();
    }
    public function deleteItemTransaksi($id)
    {
        $result = $this->db->get_where('item_pembelian', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('item_pembelian');

        return $result->row_array();
    }

    public function deleteItemPembelianByPembelianID($pembelian_id)
    {
        $result = $this->db->get_where('item_pembelian', array('pembelian_id' => $transaksi_id));

        $this->db->where('pembelian_id', $pembelian_id);
        $this->db->delete('item_pembelian');

        return $result->result_array();
    }
    public function cekItemTransaksiExist($id)
    {
        $data = array('id' => $id);

        $this->db->where($data);
        $result = $this->db->get('item_pembelian');

        if (empty($result->row_array())) {
            return false;
        }
        return true;
    }
}
