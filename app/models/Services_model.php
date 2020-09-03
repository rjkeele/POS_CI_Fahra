<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Services_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

//    public function updateSetting($data = array()) {
//
//        if ($this->db->update('settings', $data, array('setting_id' => 1))) {
//            return true;
//        }
//        return false;
//    }

    public function insertData($ticket_id, $data = array()) {

        $this->db->where('TicketId', $ticket_id);
        $this->db->from('prtservices');
        $query = $this->db->get();
        $count = count($query->result_array());

//        return $count;

        if ($count == 0) {
            if ($this->db->insert('prtservices', $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
