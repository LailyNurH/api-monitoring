<?php

defined('BASEPATH') or exit('No direct script access allowed');


class M_item_transaksi extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function getItemTransaksi()
    {
        $this->db->select('item_transaksi.id, item_transaksi.transaksi_id, produk.nama, item_transaksi.qty,item_transaksi.harga_saat_transaksi,item_transaksi.sub_total');
        $this->db->from('item_transaksi');
        $this->db->join('produk', 'produk.id = item_transaksi.produk_id');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getItemTransaksiByTransaksiID($transaksi_id)
    {
        $this->db->select('item_transaksi.id, item_transaksi.transaksi_id, produk.nama, item_transaksi.qty,item_transaksi.harga_saat_transaksi,item_transaksi.sub_total');
        $this->db->from('item_transaksi');
        $this->db->join('produk', 'produk.id = item_transaksi.produk_id');
        $this->db->where('item_transaksi.transaksi_id', $transaksi_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function insertItemTransaksi($data)
    {

        $this->db->insert('item_transaksi', $data);

        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('item_transaksi', array('id' => $insert_id));

        //Code Untuk mengubah Stok Barang
        $result_produk = $this->db->get_where('produk', array('id' => $data["produk_id"]));
        $result_produk = $result_produk->row_array();
        $stok_lama = $result_produk["stok"];
        $stokbaru = $stok_lama - $data["qty"];

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
        $this->db->update('item_transaksi', $data);

        $result = $this->db->get_where('item_transaksi', array('id' => $id));
        return $result->row_array();
    }
    public function deleteItemTransaksi($id)
    {
        $result = $this->db->get_where('item_transaksi', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('item_transaksi');

        return $result->row_array();
    }

    public function deleteItemTransaksiByTransaksiID($transaksi_id)
    {
        $result = $this->db->get_where('item_transaksi', array('transaksi_id' => $transaksi_id));

        $this->db->where('transaksi_id', $transaksi_id);
        $this->db->delete('item_transaksi');

        return $result->result_array();
    }
    public function cekItemTransaksiExist($id)
    {
        $data = array('id' => $id);

        $this->db->where($data);
        $result = $this->db->get('item_transaksi');

        if (empty($result->row_array())) {
            return false;
        }
        return true;
    }
}
