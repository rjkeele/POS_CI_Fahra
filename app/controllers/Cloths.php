<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cloths extends MY_Controller {

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('cloths_model');
    }
    // colors
    function colors() {

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['colors'] = $this->site->getAllColors();
        $this->data['page_title'] = 'Colors';
        $bc = array(array('link' => '#', 'page' => 'Colors'));
        $meta = array('page_title' => 'Colors', 'bc' => $bc);
        $this->page_construct('cloths/color', $this->data, $meta);

    }

    function get_colors() {

        $this->load->library('datatables');
        $this->datatables->select("id, name, price, hash_code, created_by, created_on,");
        $this->datatables->where('name !=', 'Skip');
        $this->datatables->from('cloth_colors');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('cloths/edit_color/$1') . "' title='Edit Color' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('cloths/delete_color/$1') . "' onClick=\"return confirm('You are going to delete color, please click ok to delete.')\" title='Delete Color' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id, price, hash_code, name");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_color() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('name', 'Color Name', 'required');
        $this->form_validation->set_rules('price', 'Color Price', 'required|numeric');
        $this->form_validation->set_rules('hash_code', 'Color Code', 'required');

        if ($this->form_validation->run() == true) {
            $data = array('hash_code' => $this->input->post('hash_code'), 'name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->addColor($data)) {

            $this->session->set_flashdata('message', 'Added Color');
            redirect('cloths/colors');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Add color';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Cloths'), array('link' => '#', 'page' => 'Add Color'));
            $meta = array('page_title' => 'Add Color', 'bc' => $bc);
            $this->page_construct('cloths/add_color', $this->data, $meta);
        }
    }

    function edit_color($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('name', 'Color Name', 'required');
        $this->form_validation->set_rules('price', 'Color Price', 'required|numeric');
        $this->form_validation->set_rules('hash_code', 'Color Code', 'required');

        if ($this->form_validation->run() == true) {
            $data = array('hash_code' => $this->input->post('hash_code'), 'name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->updateColor($id, $data)) {

            $this->session->set_flashdata('message', 'Updated Color');
            redirect('cloths/colors');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['color'] = $this->site->getColorByID($id);
            $this->data['page_title'] = 'New color';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Colors'), array('link' => '#', 'page' => 'Edit Color'));
            $meta = array('page_title' => 'Edit Color', 'bc' => $bc);
            $this->page_construct('cloths/edit_color', $this->data, $meta);

        }
    }

    function delete_color($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->cloths_model->deleteColor($id)) {
            $this->session->set_flashdata('message', 'Deleted Color');
            redirect('cloths/colors');
        }
    }

    function import_color() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("cloths/import_color");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('hash_code', 'name', 'price');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("cloths/import_color");
                }

                foreach ($final as $csv_pr) {
                    if($this->site->getColorByCode($csv_pr['hash_code'])) {
                        $this->session->set_flashdata('error', 'Check Color' . " (" . $csv_pr['hash_code'] . "). " . 'Color already exist');
                        redirect("cloths/import_color");
                    }
                    $data[] = array('hash_code' => $csv_pr['hash_code'], 'name' => $csv_pr['name'], 'price' => $csv_pr['price']);
                }
            }

        }

        if ($this->form_validation->run() == true && $this->cloths_model->add_colors($data)) {

            $this->session->set_flashdata('message', 'Added Colors');
            redirect('cloths/colors');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Import Colors';
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => site_url('cloths/colors'), 'page' => 'Colors'), array('link' => '#', 'page' => 'Import Colors'));
            $meta = array('page_title' => 'Import Colors', 'bc' => $bc);
            $this->page_construct('cloths/import_color', $this->data, $meta);

        }
    }
    // upcharges
    function upcharges() {

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['upcharges'] = $this->site->getAllUpcharges();
        $this->data['page_title'] = 'Upcharges';
        $bc = array(array('link' => '#', 'page' => 'Upcharges'));
        $meta = array('page_title' => 'Upcharges', 'bc' => $bc);
        $this->page_construct('cloths/upcharge', $this->data, $meta);

    }

    function get_upcharges() {

        $this->load->library('datatables');
        $this->datatables->select("id, name, price, created_by, created_on,");
        $this->datatables->from('cloth_upcharges');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('cloths/edit_upcharge/$1') . "' title='Edit Upcharge' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('cloths/delete_upcharge/$1') . "' onClick=\"return confirm('You are going to delete upcharge, please click ok to delete.')\" title='Delete Upcharge' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id, price, name");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_upcharge() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('name', 'Upcharge Name', 'required');
        $this->form_validation->set_rules('price', 'Upcharge Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->addUpcharge($data)) {

            $this->session->set_flashdata('message', 'Added Upcharge');
            redirect('cloths/upcharges');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Add Upcharge';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Cloths'), array('link' => '#', 'page' => 'Add Upcharge'));
            $meta = array('page_title' => 'Add Upcharge', 'bc' => $bc);
            $this->page_construct('cloths/add_upcharge', $this->data, $meta);
        }
    }

    function edit_upcharge($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('name', 'Upcharge Name', 'required');
        $this->form_validation->set_rules('price', 'Upcharge Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->updateUpcharge($id, $data)) {

            $this->session->set_flashdata('message', 'Updated Upcharge');
            redirect('cloths/upcharges');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['upcharge'] = $this->site->getUpchargeByID($id);
            $this->data['page_title'] = 'New Upcharge';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Upcharges'), array('link' => '#', 'page' => 'Edit Upcharge'));
            $meta = array('page_title' => 'Edit Upcharge', 'bc' => $bc);
            $this->page_construct('cloths/edit_upcharge', $this->data, $meta);

        }
    }

    function delete_upcharge($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->cloths_model->deleteUpcharge($id)) {
            $this->session->set_flashdata('message', 'Deleted Upcharge');
            redirect('cloths/upcharges');
        }
    }

    function import_upcharge() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("cloths/import_upcharge");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('name', 'price');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("cloths/import_upcharge");
                }

                foreach ($final as $csv_pr) {
                    if($this->site->getUpchargeByName($csv_pr['name'])) {
                        $this->session->set_flashdata('error', 'Check Upcharge' . " (" . $csv_pr['name'] . "). " . 'Upcharge already exist');
                        redirect("cloths/import_upcharge");
                    }
                    $data[] = array('name' => $csv_pr['name'], 'price' => $csv_pr['price']);
                }
            }

        }

        if ($this->form_validation->run() == true && $this->cloths_model->add_upcharges($data)) {

            $this->session->set_flashdata('message', 'Added Upcharges');
            redirect('cloths/upcharges');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Import Upcharges';
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => site_url('cloths/upcharges'), 'page' => 'Upcharges'), array('link' => '#', 'page' => 'Import Upcharges'));
            $meta = array('page_title' => 'Import Upcharges', 'bc' => $bc);
            $this->page_construct('cloths/import_upcharge', $this->data, $meta);

        }
    }
    // spot lists
    function spotlists() {

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['spotlists'] = $this->site->getAllSpotlists();
        $this->data['page_title'] = 'Spotlists';
        $bc = array(array('link' => '#', 'page' => 'Spotlists'));
        $meta = array('page_title' => 'Spotlists', 'bc' => $bc);
        $this->page_construct('cloths/spotlist', $this->data, $meta);

    }

    function get_spotlists() {

        $this->load->library('datatables');
        $this->datatables->select("id, name, price, created_by, created_on,");
        $this->datatables->from('cloth_spotlists');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('cloths/edit_spotlist/$1') . "' title='Edit Spotlist' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('cloths/delete_spotlist/$1') . "' onClick=\"return confirm('You are going to delete spotlist, please click ok to delete.')\" title='Delete Spotlist' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id, price, name");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_spotlist() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('name', 'Spotlist Name', 'required');
        $this->form_validation->set_rules('price', 'Spotlist Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->addSpotlist($data)) {

            $this->session->set_flashdata('message', 'Added Spotlist');
            redirect('cloths/Spotlists');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Add Spotlist';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Cloths'), array('link' => '#', 'page' => 'Add Spotlist'));
            $meta = array('page_title' => 'Add Spotlist', 'bc' => $bc);
            $this->page_construct('cloths/add_spotlist', $this->data, $meta);
        }
    }

    function edit_spotlist($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('name', 'Spotlist Name', 'required');
        $this->form_validation->set_rules('price', 'Spotlist Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->updateSpotlist($id, $data)) {

            $this->session->set_flashdata('message', 'Updated Spotlist');
            redirect('cloths/spotlists');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['spotlist'] = $this->site->getSpotlistByID($id);
            $this->data['page_title'] = 'New Spotlist';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'spotlists'), array('link' => '#', 'page' => 'Edit Spotlist'));
            $meta = array('page_title' => 'Edit Spotlist', 'bc' => $bc);
            $this->page_construct('cloths/edit_spotlist', $this->data, $meta);

        }
    }

    function delete_spotlist($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->cloths_model->deleteSpotlist($id)) {
            $this->session->set_flashdata('message', 'Deleted Spotlist');
            redirect('cloths/spotlists');
        }
    }

    function import_spotlist() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("cloths/import_spotlist");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('name', 'price');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("cloths/import_spotlist");
                }

                foreach ($final as $csv_pr) {
                    if($this->site->getSpotlistByName($csv_pr['name'])) {
                        $this->session->set_flashdata('error', 'Check Spotlist' . " (" . $csv_pr['name'] . "). " . 'Spotlist already exist');
                        redirect("cloths/import_spotlist");
                    }
                    $data[] = array('name' => $csv_pr['name'], 'price' => $csv_pr['price']);
                }
            }

        }

        if ($this->form_validation->run() == true && $this->cloths_model->add_spotlists($data)) {

            $this->session->set_flashdata('message', 'Added Spotlists');
            redirect('cloths/spotlists');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Import Spotlists';
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => site_url('cloths/spotlists'), 'page' => 'Spotlists'), array('link' => '#', 'page' => 'Import Spotlists'));
            $meta = array('page_title' => 'Import Spotlists', 'bc' => $bc);
            $this->page_construct('cloths/import_spotlist', $this->data, $meta);

        }
    }
    // materials
    function materials() {

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['materials'] = $this->site->getAllMaterials();
        $this->data['page_title'] = 'Materials';
        $bc = array(array('link' => '#', 'page' => 'Materials'));
        $meta = array('page_title' => 'Materials', 'bc' => $bc);
        $this->page_construct('cloths/material', $this->data, $meta);

    }

    function get_materials() {

        $this->load->library('datatables');
        $this->datatables->select("id, name, price, created_by, created_on,");
        $this->datatables->from('cloth_materials');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('cloths/edit_material/$1') . "' title='Edit Material' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('cloths/delete_material/$1') . "' onClick=\"return confirm('You are going to delete material, please click ok to delete.')\" title='Delete Material' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id, price, name");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_material() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('name', 'Material Name', 'required');
        $this->form_validation->set_rules('price', 'Material Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->addMaterial($data)) {

            $this->session->set_flashdata('message', 'Added Material');
            redirect('cloths/materials');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Add Material';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Cloths'), array('link' => '#', 'page' => 'Add Material'));
            $meta = array('page_title' => 'Add Material', 'bc' => $bc);
            $this->page_construct('cloths/add_material', $this->data, $meta);
        }
    }

    function edit_material($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('name', 'Material Name', 'required');
        $this->form_validation->set_rules('price', 'Material Price', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'), 'price' => $this->input->post('price'));
        }

        if ($this->form_validation->run() == true && $this->cloths_model->updateMaterial($id, $data)) {

            $this->session->set_flashdata('message', 'Updated Material');
            redirect('cloths/materials');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['material'] = $this->site->getMaterialByID($id);
            $this->data['page_title'] = 'New Material';
            $bc = array(array('link' => site_url('cloths'), 'page' => 'Materials'), array('link' => '#', 'page' => 'Edit Material'));
            $meta = array('page_title' => 'Edit Material', 'bc' => $bc);
            $this->page_construct('cloths/edit_material', $this->data, $meta);

        }
    }

    function delete_material($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->cloths_model->deleteMaterial($id)) {
            $this->session->set_flashdata('message', 'Deleted Material');
            redirect('cloths/materials');
        }
    }

    function import_material() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("cloths/import_material");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('name', 'price');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("cloths/import_material");
                }

                foreach ($final as $csv_pr) {
                    if($this->site->getMaterialByName($csv_pr['name'])) {
                        $this->session->set_flashdata('error', 'Check Material' . " (" . $csv_pr['name'] . "). " . 'Material already exist');
                        redirect("cloths/import_material");
                    }
                    $data[] = array('name' => $csv_pr['name'], 'price' => $csv_pr['price']);
                }
            }

        }

        if ($this->form_validation->run() == true && $this->cloths_model->add_materials($data)) {

            $this->session->set_flashdata('message', 'Added Materials');
            redirect('cloths/materials');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Import Materials';
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => site_url('cloths/materials'), 'page' => 'Materials'), array('link' => '#', 'page' => 'Import Materials'));
            $meta = array('page_title' => 'Import Materials', 'bc' => $bc);
            $this->page_construct('cloths/import_material', $this->data, $meta);

        }
    }








    function cloths() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = 'Dryclean Dashboard Settings';
        $bc = array(array('link' => '#', 'page' => 'Cloth Types'));
        $meta = array('page_title' => 'Cloth Types', 'bc' => $bc);
        $this->data['tbl_cloth_types'] = $this->tbl_cloth_types;
        $this->data['tbl_cloth_sub_types'] = $this->tbl_cloth_sub_types;
        $this->data['tbl_cloth_patterns'] = $this->tbl_cloth_patterns;
        $this->data['tbl_cloth_materials'] = $this->tbl_cloth_materials;
        $this->data['tbl_cloth_upcharges'] = $this->tbl_cloth_upcharges;
        

        $this->data['cloth_types'] = $res = $this->settings_model->getData($this->tbl_cloth_types);
        $this->data['cloth_sub_types'] = $this->settings_model->getData($this->tbl_cloth_sub_types);
        $this->data['cloth_patterns'] = $this->settings_model->getData($this->tbl_cloth_patterns);
        $this->data['cloth_materials'] = $this->settings_model->getData($this->tbl_cloth_materials);
        $this->data['cloth_colors'] = $this->settings_model->getData($this->tbl_cloth_colors);
        $this->data['cloth_upcharges'] = $this->settings_model->getData($this->tbl_cloth_upcharges);
        $this->page_construct('cloths/cloths', $this->data, $meta);
    }

    function doAddClothTypes() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('name'),
            'cloth_type' => $this->input->post('cloth_type'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_types, $data);

        if ($_FILES['userfile']['size'] > 0) {
            $this->load->library('upload');

            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|svg';
            $config['max_size'] = '3500';
            $config['max_width'] = '3300';
            $config['max_height'] = '3100';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                //  redirect("settings/cloths");
            }

            $updata['image'] = $photo = $this->upload->file_name;
            $data['logo'] = $photo;
            $where['id'] = $result;
            $this->settings_model->updateData($this->tbl_cloth_types, $where, $updata);
        }

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Type added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Type creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothTypes() {
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'cloth_type' => $this->input->post('cloth_type'),
        );
        $where['id'] = $this->input->post('cloth_type_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_types, $where, $data);
        $result2 = 0;
        if ($_FILES['userfile']['size'] > 0) {
            $this->load->library('upload');

            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|svg';
            $config['max_size'] = '3500';
            $config['max_width'] = '3300';
            $config['max_height'] = '3100';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                //  redirect("settings/cloths");
            }

            $updata['image'] = $photo = $this->upload->file_name;
            $data['logo'] = $photo;
            $where['id'] = $this->input->post('cloth_type_id');
            $result2 = $this->settings_model->updateData($this->tbl_cloth_types, $where, $updata);
        }

        if ($result > 0 || $result2 > 0) {
            $this->session->set_flashdata('success', 'Cloth Type updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Type updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doAddClothPattern() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_patterns, $data);

        if ($_FILES['userfile']['size'] > 0) {
            $this->load->library('upload');

            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|svg';
            $config['max_size'] = '3500';
            $config['max_width'] = '3300';
            $config['max_height'] = '3100';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                //  redirect("settings/cloths");
            }

            $updata['image'] = $photo = $this->upload->file_name;
            $data['logo'] = $photo;
            $where['id'] = $result;
            $this->settings_model->updateData($this->tbl_cloth_patterns, $where, $updata);
        }

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Pattern added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Pattern creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothPattern() {
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
        );
        $where['id'] = $this->input->post('cloth_pattern_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_patterns, $where, $data);
        $result2 = 0;
        if ($_FILES['userfile']['size'] > 0) {
            $this->load->library('upload');

            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|svg';
            $config['max_size'] = '3500';
            $config['max_width'] = '3300';
            $config['max_height'] = '3100';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                //  redirect("settings/cloths");
            }

            $updata['image'] = $photo = $this->upload->file_name;
            $data['logo'] = $photo;
            $where['id'] = $this->input->post('cloth_pattern_id');
            $result2 = $this->settings_model->updateData($this->tbl_cloth_patterns, $where, $updata);
        }

        if ($result > 0 || $result2 > 0) {
            $this->session->set_flashdata('success', 'Cloth Pattern updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Pattern updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doAddClothSubType() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('cloth_sub_type'),
            'price' => $this->input->post('price'),
            'cloth_type_id' => $this->input->post('cloth_type_id'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_sub_types, $data);
        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Sub Type added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Sub Type creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothSubType() {
        $data = array(
            'name' => $this->input->post('cloth_sub_type'),
            'price' => $this->input->post('price'),
            'cloth_type_id' => $this->input->post('cloth_type_id'),
        );
        $where['id'] = $this->input->post('cloth_sub_type_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_sub_types, $where, $data);

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Type updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Type updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doAddClothMaterial() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_materials, $data);
        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Material added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Material creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothMaterial() {
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
        );
        $where['id'] = $this->input->post('cloth_material_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_materials, $where, $data);

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Material updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Material updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doAddClothColor() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'hash_code' => $this->input->post('hash_code'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_colors, $data);
        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Color added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Color creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothColor() {
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'hash_code' => $this->input->post('hash_code'),
        );
        $where['id'] = $this->input->post('cloth_color_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_colors, $where, $data);

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Color updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Color updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

        function doAddClothUpcharge() {
        $data = array(
            'created_by' => $this->session->userdata('user_id'),
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'created_on' => date('Y-m-d H:i:s'),
        );
        $result = $this->settings_model->addData($this->tbl_cloth_upcharges, $data);
        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Upcharge added successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Upcharge creation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }

    function doUpdateClothUpcharge() {
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
        );
        $where['id'] = $this->input->post('cloth_upcharge_id');
        $result = $this->settings_model->updateData($this->tbl_cloth_upcharges, $where, $data);

        if ($result > 0) {
            $this->session->set_flashdata('success', 'Cloth Upcharge updated successfully ');
        } else {
            $this->session->set_flashdata('error', 'Cloth Upcharge updation failed ' . $this->db->last_query());
        }
        redirect('settings/cloths');
    }
    function getClothNames() {
        $cloth_type = $this->input->get('cloth_type');
        $where = array('cloth_type' => $cloth_type);
        $result = $this->settings_model->getData($this->tbl_cloth_types, $where, '*', 'name', 'ASC')->result();
        $html = '';
        foreach ($result as $ro) {
            $html .= '<option value="' . $ro->id . '">' . $ro->name . '</option>';
        }
        echo json_encode(array('html' => $html));
        exit();
    }

    function getClothNameImage() {
        $cloth_type_id = $this->input->get('cloth_type_id');
        // $cloth_type_name = $this->input->get('cloth_type_name');
        $where = array('id' => $cloth_type_id);
        $result = $this->settings_model->getData($this->tbl_cloth_types, $where)->row();
        $image = base_url() . 'uploads/' . $result->image;
        echo json_encode(array('src' => $image));
        exit();
    }
    function deleteRow() {
        $del_id = $this->input->get('del_id');
        $del_tbl = $this->input->get('del_tbl');
        $where = array('id' => $cloth_type_id);
        $result = $this->settings_model->deleteRow($del_id, $del_tbl);
		        echo true;
        exit();
    }
    function insertColors() {
        $colors = array(
            "800000" => "Maroon",
            "8B0000" => "DarkRed",
            "B22222" => "FireBrick",
            "FF0000" => "Red",
            "FA8072" => "Salmon",
            "FF6347" => "Tomato",
            "FF7F50" => "Coral",
            "FF4500" => "OrangeRed",
            "D2691E" => "Chocolate",
            "F4A460" => "SandyBrown",
            "FF8C00" => "DarkOrange",
            "FFA500" => "Orange",
            "B8860B" => "DarkGoldenrod",
            "DAA520" => "Goldenrod",
            "FFD700" => "Gold",
            "808000" => "Olive",
            "FFFF00" => "Yellow",
            "9ACD32" => "YellowGreen",
            "ADFF2F" => "GreenYellow",
            "7FFF00" => "Chartreuse",
            "7CFC00" => "LawnGreen",
            "008000" => "Green",
            "00FF00" => "Lime",
            "32CD32" => "LimeGreen",
            "00FF7F" => "SpringGreen",
            "00FA9A" => "MediumSpringGreen",
            "40E0D0" => "Turquoise",
            "20B2AA" => "LightSeaGreen",
            "48D1CC" => "MediumTurquoise",
            "008080" => "Teal",
            "008B8B" => "DarkCyan",
            "00FFFF" => "Aqua",
            "00FFFF" => "Cyan",
            "00CED1" => "DarkTurquoise",
            "00BFFF" => "DeepSkyBlue",
            "1E90FF" => "DodgerBlue",
            "4169E1" => "RoyalBlue",
            "000080" => "Navy",
            "00008B" => "DarkBlue",
            "0000CD" => "MediumBlue",
            "0000FF" => "Blue",
            "8A2BE2" => "BlueViolet",
            "9932CC" => "DarkOrchid",
            "9400D3" => "DarkViolet",
            "800080" => "Purple",
            "8B008B" => "DarkMagenta",
            "FF00FF" => "Fuchsia",
            "FF00FF" => "Magenta",
            "C71585" => "MediumVioletRed",
            "FF1493" => "DeepPink",
            "FF69B4" => "HotPink",
            "DC143C" => "Crimson",
            "A52A2A" => "Brown",
            "CD5C5C" => "IndianRed",
            "BC8F8F" => "RosyBrown",
            "F08080" => "LightCoral",
            "FFFAFA" => "Snow",
            "FFE4E1" => "MistyRose",
            "E9967A" => "DarkSalmon",
            "FFA07A" => "LightSalmon",
            "A0522D" => "Sienna",
            "FFF5EE" => "SeaShell",
            "8B4513" => "SaddleBrown",
            "FFDAB9" => "Peachpuff",
            "CD853F" => "Peru",
            "FAF0E6" => "Linen",
            "FFE4C4" => "Bisque",
            "DEB887" => "Burlywood",
            "D2B48C" => "Tan",
            "FAEBD7" => "AntiqueWhite",
            "FFDEAD" => "NavajoWhite",
            "FFEBCD" => "BlanchedAlmond",
            "FFEFD5" => "PapayaWhip",
            "FFE4B5" => "Moccasin",
            "F5DEB3" => "Wheat",
            "FDF5E6" => "Oldlace",
            "FFFAF0" => "FloralWhite",
            "FFF8DC" => "Cornsilk",
            "F0E68C" => "Khaki",
            "FFFACD" => "LemonChiffon",
            "EEE8AA" => "PaleGoldenrod",
            "BDB76B" => "DarkKhaki",
            "F5F5DC" => "Beige",
            "FAFAD2" => "LightGoldenrodYellow",
            "FFFFE0" => "LightYellow",
            "FFFFF0" => "Ivory",
            "6B8E23" => "OliveDrab",
            "556B2F" => "DarkOliveGreen",
            "8FBC8F" => "DarkSeaGreen",
            "006400" => "DarkGreen",
            "228B22" => "ForestGreen",
            "90EE90" => "LightGreen",
            "98FB98" => "PaleGreen",
            "F0FFF0" => "Honeydew",
            "2E8B57" => "SeaGreen",
            "3CB371" => "MediumSeaGreen",
            "F5FFFA" => "Mintcream",
            "66CDAA" => "MediumAquamarine",
            "7FFFD4" => "Aquamarine",
            "2F4F4F" => "DarkSlateGray",
            "AFEEEE" => "PaleTurquoise",
            "E0FFFF" => "LightCyan",
            "F0FFFF" => "Azure",
            "5F9EA0" => "CadetBlue",
            "B0E0E6" => "PowderBlue",
            "ADD8E6" => "LightBlue",
            "87CEEB" => "SkyBlue",
            "87CEFA" => "LightskyBlue",
            "4682B4" => "SteelBlue",
            "F0F8FF" => "AliceBlue",
            "708090" => "SlateGray",
            "778899" => "LightSlateGray",
            "B0C4DE" => "LightsteelBlue",
            "6495ED" => "CornflowerBlue",
            "E6E6FA" => "Lavender",
            "F8F8FF" => "GhostWhite",
            "191970" => "MidnightBlue",
            "6A5ACD" => "SlateBlue",
            "483D8B" => "DarkSlateBlue",
            "7B68EE" => "MediumSlateBlue",
            "9370DB" => "MediumPurple",
            "4B0082" => "Indigo",
            "BA55D3" => "MediumOrchid",
            "DDA0DD" => "Plum",
            "EE82EE" => "Violet",
            "D8BFD8" => "Thistle",
            "DA70D6" => "Orchid",
            "FFF0F5" => "LavenderBlush",
            "DB7093" => "PaleVioletRed",
            "FFC0CB" => "Pink",
            "FFB6C1" => "LightPink",
            "000000" => "Black",
            "696969" => "DimGray",
            "808080" => "Gray",
            "A9A9A9" => "DarkGray",
            "C0C0C0" => "Silver",
            "D3D3D3" => "LightGrey",
            "DCDCDC" => "Gainsboro",
            "F5F5F5" => "WhiteSmoke",
            "FFFFFF" => "White"
        );
        foreach ($colors as $k => $val) {
            $data = array(
                'name' => $val,
                'hash_code' => $k,
                'created_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
            );
            $this->settings_model->addData($this->tbl_cloth_colors, $data);
        }
    }

}
