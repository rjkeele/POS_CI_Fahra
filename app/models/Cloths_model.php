<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cloths_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function addColor($data) {
        if ($this->db->insert('cloth_colors', $data)) {
            return true;
        }
        return false;
    }

    public function add_colors($data = array()) {
        if ($this->db->insert_batch('cloth_colors', $data)) {
            return true;
        }
        return false;
    }

    public function updateColor($id, $data = NULL) {
        if ($this->db->update('cloth_colors', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteColor($id) {
        if ($this->db->delete('cloth_colors', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    // upcharge
    public function addUpcharge($data) {
        if ($this->db->insert('cloth_upcharges', $data)) {
            return true;
        }
        return false;
    }
    
    public function add_Upcharges($data = array()) {
        if ($this->db->insert_batch('cloth_upcharges', $data)) {
            return true;
        }
        return false;
    }

    public function updateUpcharge($id, $data = NULL) {
        if ($this->db->update('cloth_upcharges', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteUpcharge($id) {
        if ($this->db->delete('cloth_upcharges', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    // spotlist
    public function addSpotlist($data) {
        if ($this->db->insert('cloth_spotlists', $data)) {
            return true;
        }
        return false;
    }
    
    public function add_spotlists($data = array()) {
        if ($this->db->insert_batch('cloth_spotlists', $data)) {
            return true;
        }
        return false;
    }

    public function updateSpotlist($id, $data = NULL) {
        if ($this->db->update('cloth_spotlists', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteSpotlist($id) {
        if ($this->db->delete('cloth_spotlists', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
//materials
public function addMaterial($data) {
    if ($this->db->insert('cloth_materials', $data)) {
        return true;
    }
    return false;
}

public function add_materials($data = array()) {
    if ($this->db->insert_batch('cloth_materials', $data)) {
        return true;
    }
    return false;
}

public function updateMaterial($id, $data = NULL) {
    if ($this->db->update('cloth_materials', $data, array('id' => $id))) {
        return true;
    }
    return false;
}

public function deleteMaterial($id) {
    if ($this->db->delete('cloth_materials', array('id' => $id))) {
        return true;
    }
    return FALSE;
}
}
