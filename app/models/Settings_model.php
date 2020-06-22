<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function updateSetting($data = array()) {

        if ($this->db->update('settings', $data, array('setting_id' => 1))) {
            return true;
        }
        return false;
    }

    public function getStoreByID($id) {
        $q = $this->db->get_where('stores', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getSettingsByID($id) {
        $q = $this->db->get_where('settings', array('setting_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;

    }

    public function addStore($data = array()) {
        if ($this->db->insert('stores', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updateStore($id, $data = array()) {
        if ($this->db->update('stores', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteStore($id) {
        if ($this->db->delete('stores', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addPrinter($data = array()) {
        if ($this->db->insert('printers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updatePrinter($id, $data = array()) {
        if ($this->db->update('printers', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deletePrinter($id) {
        if ($this->db->delete('printers', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    function getData($table, $where = array(), $select = '*', $order_key = '', $order_type = "") {
        if ($select == '' || $select == '*') {
            $this->db->select('*');
        } else {
            $this->db->select($select);
        }
        if (isset($where) && !empty($where)) {
            $this->db->where($where);
        }
        if ($order_key != '' && $order_type != '') {
            $this->db->order_by($order_key, $order_type);
        }

        $this->db->from($table);
        $query = $this->db->get();
        return $query;
    }

    function updateData($table, $whereInfo, $updateInfo) {
        // $this->db->trans_start();
        $this->db->where($whereInfo);
        $this->db->update($table, $updateInfo);
        // $this->db->trans_complete();
        return $this->db->affected_rows();
    }

    function addData($table, $insertInfo) {
        $this->db->trans_start();
        $this->db->insert($table, $insertInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $insert_id;
    }
	
    function deleteRow($del_id, $del_tbl) {
        $this->db->where('id', $del_id);
        $this->db->delete($del_tbl);
        return $this->db->affected_rows();
    }

}
