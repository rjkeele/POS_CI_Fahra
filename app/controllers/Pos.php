<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pos extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        $this->load->helper('pos');
        $this->load->model('pos_model');
        $this->load->model('sales_model');
        $this->load->library('form_validation');
    }

    public function index($sid = null, $eid = null)
    {

        $userId = $this->session->userdata('user_id');
        $reference = '';

//        check whether stripe token is not empty
        if (!empty($_POST['stripeToken'])) {

//            var_dump($this->input->post('balance_amount'));
//            die();

            //get token, card and user info from the form
            $token = $_POST['stripeToken'];
//            $card_name = $_POST['cc_holder'];
            $card_email = $_POST['card_email'];
            $card_num = $_POST['cc_no'];
            $card_cvc = $_POST['cc_cvv2'];
            $card_exp_month = $_POST['cc_month'];
            $card_exp_year = $_POST['cc_year'];

            //include Stripe PHP library
            require_once APPPATH . "third_party/stripe-php/init.php";

            //set api key
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret'));

            //add customer to stripe
            $customer = \Stripe\Customer::create(array(
                'email' => $card_email,
                'source' => $token
            ));

            //item information
            $amount = $this->input->post('amount');
            $currency = 'usd';
            $paid_by = 'stripe';
            $balance = $this->input->post('balance_amount');

            //charge a credit or a debit card
            $charge = \Stripe\Charge::create(array(
                'customer' => $customer['id'],
                'amount' => $amount * 100,
                'currency' => $currency
            ));

            //receive charge details
            $chargeJson = $charge->jsonSerialize();
            if (count($chargeJson) > 0) {

                $reference = $chargeJson['id'];

            }
        }

        $this->load->model('settings_model');
        if (!$this->Settings->multi_store) {
            $this->session->set_userdata('store_id', 1);
        }
        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect($this->Settings->multi_store ? 'stores' : 'welcome');
        }
        if ($this->input->get('hold')) {
            $sid = $this->input->get('hold');
        }
        if ($this->input->get('edit')) {
            $eid = $this->input->get('edit');
        }
        if ($this->input->post('eid')) {
            $eid = $this->input->post('eid');
        }
        if ($this->input->post('did')) {
            $did = $this->input->post('did');
        } else {
            $did = null;
        }
        if ($eid && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
        }
        if (!$this->Settings->default_customer) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            redirect('settings');
        }
        if (!$this->session->userdata('register_id')) {
            if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
                $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
                $this->session->set_userdata($register_data);
            } else {
                $this->session->set_flashdata('error', lang('register_not_open'));
                redirect('pos/open_register');
            }
        }

        $suspend = $this->input->post('suspend') ? true : false;

        $this->form_validation->set_rules('customer', lang("customer"), 'trim|required');
        // $this->form_validation->set_rules('security_pin', lang("security_pin"), 'trim|required|password');

        if ($this->form_validation->run() == true) {

            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";

            $date = $eid ? $this->input->post('date') : date('Y-m-d H:i:s');
            $customer_id = $this->input->post('customer_id');
            $customer_details = $this->pos_model->getCustomerByID($customer_id);
            $customer = $customer_details->name;
            $note = $this->tec->clear_tags($this->input->post('spos_note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $pickup_date = $_POST['pickup'];
            $items_detail = $_POST['items_detail'];
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $real_unit_price = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_comment = $_POST['item_comment'][$r];
                $item_color_name = $_POST['color_name'][$r];
                $item_color_cost = $_POST['color_cost'][$r];
                $item_upcharge_name = $_POST['upcharge_name'][$r];
                $item_upcharge_cost = $_POST['upcharge_cost'][$r];
                $item_material_name = $_POST['material_name'][$r];
                $item_material_cost = $_POST['material_cost'][$r];
                $set_pc = $_POST['set_pc'][$r];
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

                if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                    $product_details = $this->site->getProductByID($item_id);
                    if ($product_details) {
                        $product_name = $product_details->name;
                        $product_code = $product_details->code;
                        $product_cost = $product_details->cost;
                    } else {
                        $product_name = $_POST['product_name'][$r];
                        $product_code = $_POST['product_code'][$r];
                        $product_cost = 0;
                    }
                    if (!$this->Settings->overselling) {
                        if ($product_details->type == 'standard') {
                            if ($product_details->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low") . ' (' .
                                    lang('name') . ': ' . $product_details->name . ' | ' .
                                    lang('ordered') . ': ' . $item_quantity . ' | ' .
                                    lang('available') . ': ' . $product_details->quantity .
                                    ')');
                                redirect("pos");
                            }
                        } elseif ($product_details->type == 'combo') {
                            $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                            foreach ($combo_items as $combo_item) {
                                $cpr = $this->site->getProductByID($combo_item->id);
                                if ($cpr->quantity < $item_quantity) {
                                    $this->session->set_flashdata('error', lang("quantity_low") . ' (' .
                                        lang('name') . ': ' . $cpr->name . ' | ' .
                                        lang('ordered') . ': ' . $item_quantity . ' x ' . $combo_item->qty . ' = ' . $item_quantity * $combo_item->qty . ' | ' .
                                        lang('available') . ': ' . $cpr->quantity .
                                        ') ' . $product_details->name);
                                    redirect("pos");
                                }
                            }
                        }
                    }
                    $unit_price = $real_unit_price;

                    $pr_discount = 0;
                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                        } else {
                            $pr_discount = $this->tec->formatDecimal($discount);
                        }
                    }
                    $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                    $product_discount += $pr_item_discount;

                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = "";
                    if (isset($product_details->tax) && $product_details->tax != 0) {

                        if ($product_details && $product_details->tax_method == 1) {
                            $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                            $tax = $product_details->tax . "%";
                        } else {
                            $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                            $tax = $product_details->tax . "%";
                            $item_net_price -= $item_tax;
                        }

                        $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->tec->formatDecimal((($item_net_price * $item_quantity) + $pr_item_tax), 4) + $item_upcharge_cost + $item_color_cost + $item_material_cost;

                    $products[] = array(
                        'product_id' => $item_id,
                        'quantity' => $item_quantity,
                        'unit_price' => $unit_price,
                        'net_unit_price' => $item_net_price,
                        'discount' => $item_discount,
                        'comment' => $item_comment,
                        'item_discount' => $pr_item_discount,
                        'tax' => $tax,
                        'item_tax' => $pr_item_tax,
                        'subtotal' => $subtotal,
                        'real_unit_price' => $real_unit_price,
                        'cost' => $product_cost,
                        'product_code' => $product_code,
                        'product_name' => $product_name,
                        'color_name' => $item_color_name,
                        'color_cost' => $item_color_cost,
                        'upcharge_name' => $item_upcharge_name,
                        'upcharge_cost' => $item_upcharge_cost,
                        'material_name' => $item_material_name,
                        'material_cost' => $item_material_cost,
                        'set_pc' => $set_pc
                    );

                    $total += $this->tec->formatDecimal(($item_net_price * $item_quantity), 4) + $item_upcharge_cost + $item_color_cost + $item_material_cost;
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('order_discount')) {
                $order_discount_id = $this->input->post('order_discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
                } else {
                    $order_discount = $this->tec->formatDecimal($order_discount_id);
                }
            } else {
                $order_discount_id = null;
            }
            $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

            if ($this->input->post('order_tax')) {
                $order_tax_id = $this->input->post('order_tax');
                $opos = strpos($order_tax_id, $percentage);
                if ($opos !== false) {
                    $ots = explode("%", $order_tax_id);
                    $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
                } else {
                    $order_tax = $this->tec->formatDecimal($order_tax_id);
                }
            } else {
                $order_tax_id = null;
                $order_tax = 0;
            }

            $total_tax = $this->tec->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->tec->formatDecimal(($total + $total_tax - $order_discount), 4);
            $paid = $this->input->post('amount') ? $this->input->post('amount') : 0;
            $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
            $rounding = $this->tec->formatDecimal(($round_total - $grand_total));
            // $this->tec->print_arrays($customer_details->id, $this->tec->formatDecimal($paid), $this->tec->formatDecimal($round_total));
            if ($customer_details->id == 1 && $this->tec->formatDecimal($paid) < $this->tec->formatDecimal($round_total)) {
                $this->session->set_flashdata('error', lang('select_customer_for_due'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $status = $_POST['paid_by'];
            if (!$eid) {
                $status = 'due';
                if ($this->tec->formatDecimal($round_total) <= $this->tec->formatDecimal($paid)) {
                    $status = 'paid';
                } elseif ($this->tec->formatDecimal($round_total) > $this->tec->formatDecimal($paid) && $paid > 0) {
                    $status = 'partial';
                }
            }

            $data = array('date' => $date,
                'customer_id' => $customer_id,
                'customer_name' => $customer,
                'total' => $this->tec->formatDecimal($total, 4),
                'product_discount' => $this->tec->formatDecimal($product_discount, 4),
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $this->tec->formatDecimal($product_tax, 4),
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'total_items' => $this->input->post('total_items'),
                'total_quantity' => $this->input->post('total_quantity'),
                'rounding' => $rounding,
                'paid' => $paid,
                'pickup_date' => date('Y-m-d', strtotime($pickup_date)),
                'status' => $status,
                'created_by' => $this->input->post('created_by'), // $this->session->userdata('user_id'),
                'note' => $note,
                'hold_ref' => $this->input->post('hold_ref'),
                'items_detail' => $items_detail
            );

            if (!$eid) {
                $data['store_id'] = $this->session->userdata('store_id');
            }

            if (!$eid && !$suspend && $paid) {
                $amount = $this->tec->formatDecimal(($paid > $grand_total ? ($paid - $this->input->post('balance_amount')) : $paid), 4);
                if ($this->input->post('paying_gift_card_no')) {
                    $gc = $this->pos_model->getGiftCardByNO($this->input->post('paying_gift_card_no'));
                    if (!$gc || $gc->balance < $amount) {
                        $this->session->set_flashdata('error', lang("incorrect_gift_card"));
                        redirect("pos");
                    }
                }
                $payment = array(
                    'date' => $date,
                    'amount' => $amount,
                    'customer_id' => $customer_id,
                    'paid_by' => $this->input->post('paid_by'),
                    'cheque_no' => $this->input->post('cheque_no'),
                    'cc_no' => $this->input->post('cc_no'),
                    'gc_no' => $this->input->post('paying_gift_card_no'),
                    'cc_holder' => $this->input->post('cc_holder'),
                    'cc_month' => $this->input->post('cc_month'),
                    'cc_year' => $this->input->post('cc_year'),
                    'cc_type' => $this->input->post('cc_type'),
                    'cc_cvv2' => $this->input->post('cc_cvv2'),
                    'created_by' => $this->session->userdata('user_id'),
                    'store_id' => $this->session->userdata('store_id'),
                    'note' => $this->input->post('payment_note'),
                    'pos_paid' => $this->tec->formatDecimal($this->input->post('amount'), 4),
                    'pos_balance' => $this->tec->formatDecimal($this->input->post('balance_amount'), 4),
                    'stripe_ref' => $reference
                );
                $data['paid'] = $amount;
            } else {
                $payment = array();
            }
//            var_dump($payment);
//            die();
//             $this->tec->print_arrays($data, $products, $payment);
        }

        if ($this->form_validation->run() == true && !empty($products)) {
            // $this->tec->print_arrays($data, $products, $payment, $suspend, $eid, $did, $sid);
            if ($suspend) {
                // $this->tec->print_arrays($data, $products, $did);
                unset($data['status'], $data['rounding']);
                if ($this->pos_model->suspendSale($data, $products, $did)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_saved_to_opened_bill"));
                    redirect("pos");
                } else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/" . $did);
                }
            } elseif ($eid) {

                unset($data['status'], $data['paid']);
                if (!$this->Admin) {
                    unset($data['date']);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $this->session->userdata('user_id');
                if ($this->pos_model->updateSale($eid, $data, $products)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_updated"));
                    redirect("sales");
                } else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/?edit=" . $eid);
                }
            } else {

                if ($sale = $this->pos_model->addSale($data, $products, $payment, $did)) {
                    $this->session->set_userdata('rmspos', 1);
                    $msg = lang("sale_added");
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('message', $msg);
                    $redirect_to = $this->Settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                    if ($this->Settings->auto_print) {
                        if (!$this->Settings->remote_printing) {
                            $this->print_receipt($sale['sale_id'], true);
                        } elseif ($this->Settings->remote_printing == 2) {
                            $redirect_to .= '?print=' . $sale['sale_id'];
                        }
                    }
                    redirect($redirect_to);
                } else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos");
                }
            }
        } else {

            if (isset($sid) && !empty($sid)) {
                $suspended_sale = $this->pos_model->getSuspendedSaleByID($sid);
                $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->id = 0;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->tax = 0;
                    }
                    $row->price = $item->net_unit_price + ($item->item_discount / $item->quantity);
                    $row->unit_price = $item->unit_price + ($item->item_discount / $item->quantity) + ($item->item_tax / $item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount = $item->discount;
                    $row->qty = $item->quantity;
                    $row->comment = $item->comment;
                    $row->ordered = $item->quantity;
                    $row->color_name = $item->color_name;
                    $row->color_total_cost = $item->color_cost;
                    $row->upcharge_name = $item->upcharge_name;
                    $row->upchrges_total_cost = $item->upcharge_cost;
                    $row->material_name = $item->material_name;
                    $row->material_total_cost = $item->material_cost;
                    $combo_items = false;
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['sid'] = $sid;
                $this->data['suspend_sale'] = $suspended_sale;
                $this->data['message'] = lang('suspended_sale_loaded');
            }

            if (isset($eid) && !empty($eid)) {
                $sale = $this->pos_model->getSaleByID($eid);
                $inv_items = $this->pos_model->getAllSaleItems($eid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->id = 0;//
                        $row->code = $item->product_code;//
                        $row->name = $item->product_name;//
                        $row->tax = 0;//
                    }

                    $row->price = $item->net_unit_price;
                    $row->unit_price = $item->unit_price;
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount = $item->discount;
                    $row->qty = $item->quantity;
                    $row->comment = $item->comment;
                    $combo_items = false;
                    $row->quantity += $item->quantity;
                    $row->color_name = $item->color_name;//
                    $row->color_total_cost = $item->color_cost;//
                    $row->upcharge_name = $item->upcharge_name;//
                    $row->upchrges_total_cost = $item->upcharge_cost;//
                    $row->material_name = $item->material_name;//
                    $row->material_total_cost = $item->material_cost;//
                    if ($row->type == 'combo') {
                        $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                        foreach ($combo_items as $combo_item) {
                            $combo_item->quantity += ($combo_item->qty * $item->quantity);
                        }
                    }
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['eid'] = $eid;
                $this->data['sale'] = $sale;
                $this->data['message'] = lang('sale_loaded');
            }
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['reference_note'] = isset($sid) && !empty($sid) ? $suspended_sale->hold_ref : (isset($eid) && !empty($eid) ? $sale->hold_ref : null);
            $this->data['sid'] = isset($sid) && !empty($sid) ? $sid : 0;
            $this->data['eid'] = isset($eid) && !empty($eid) ? $eid : 0;
            // $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['customers'] = $this->site->getLimitCustomers();
            // $this->tec->print_arrays($this->data['customers']);
            $this->data["tcp"] = $this->pos_model->products_count($this->Settings->default_category);
            $this->data['products'] = $this->ajaxproducts($this->Settings->default_category, 1);
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['message'] = $this->session->flashdata('message');
            $this->data['suspended_sales'] = $this->site->getUserSuspenedSales();

            $this->data['printer'] = $this->site->getPrinterByID($this->Settings->printer);
            $printers = array();
            if (!empty($order_printers = json_decode($this->Settings->order_printers))) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->site->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;

            if ($saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getSaleByID($saleid)) {
                    if ($this->session->userdata('store_id') != $inv->store_id) {
                        $this->session->set_flashdata('error', lang('access_denied'));
                        redirect('pos');
                    }
                    $this->tec->view_rights($inv->created_by, false, 'pos');
                    $this->load->helper('text');
                    $this->data['rows'] = $this->pos_model->getAllSaleItems($saleid);
                    $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
                    $this->data['store'] = $this->site->getStoreByID($inv->store_id);
                    $this->data['inv'] = $inv;
                    $this->data['print'] = $saleid;
                    $this->data['payments'] = $this->pos_model->getAllSalePayments($saleid);
                    $this->data['created_by'] = $this->site->getUser($inv->created_by);
                }
            }

            $this->data['page_title'] = lang('pos');
            $bc = array(array('link' => '#', 'page' => lang('pos')));
            $meta = array('page_title' => lang('pos'), 'bc' => $bc);

            $this->data['tbl_cloth_types'] = $this->tbl_cloth_types;

            $this->data['tbl_cloth_sub_types'] = $this->tbl_cloth_sub_types;
            $this->data['tbl_cloth_patterns'] = $this->tbl_cloth_patterns;
            $this->data['tbl_cloth_materials'] = $this->tbl_cloth_materials;

            $this->data['cloth_types'] = $res = $this->settings_model->getData($this->tbl_cloth_types);
            $this->data['cloth_sub_types'] = $this->settings_model->getData($this->tbl_cloth_sub_types);
            $this->data['cloth_patterns'] = $this->settings_model->getData($this->tbl_cloth_patterns);
            $this->data['cloth_materials'] = $this->settings_model->getData($this->tbl_cloth_materials);
            $this->data['cloth_colors'] = $this->settings_model->getData($this->tbl_cloth_colors);
            $this->data['cloth_upcharges'] = $this->settings_model->getData($this->tbl_cloth_upcharges);
            $this->data['spot_lists'] = $this->settings_model->getData($this->tbl_cloth_spotlists);

            $pos = $this->pos_model->getPaymentInfoByUser($userId);
            $this->data['pos_balance'] = $pos[0]['pos_balance'];

            $this->load->view($this->theme . 'pos/index', $this->data, $meta);
        }
    }

    public function editCustomer()
    {

        $id = $_GET['id'];
        $data = $this->pos_model->getCustomerByID($id);
        echo json_encode($data);
    }

    public function updateCustomer()
    {
        $id = $_GET['id'];
        $data = array(
            'name' => $this->input->get('name'),
            'phone' => $this->input->get('phone'),
            'address' => $this->input->get('address'),
            'email' => $this->input->get('email'),
            'city' => $this->input->get('city'),
            'shirt_starch' => $this->input->get('shirt_starch'),
            'packing' => $this->input->get('packing'),
            'cf1' => $this->input->get('cf1'),
            'cf2' => $this->input->get('cf2'),
            'kind_phone' => $this->input->get('kind_phone'),

        );
        echo json_encode($data);

        $this->pos_model->updateCustomer($id, $data);

    }

    public function searchCustomer($search = null)
    {
        if ($this->input->get('search')) {
            $search = $this->input->get('search');
        }
        $customers = $this->site->getLimitCustomers($search);
        echo json_encode($customers);
    }

    public function get_product($code = null)
    {
        $upchrges_total_cost = 0;
        $color_total_cost = 0;
        $material_total_cost = 0;
        if ($this->input->get('code')) {
            $code = $this->input->get('code');
            $upchrges_total_cost = $this->input->get('upchrges_total_cost') ? $this->input->get('upchrges_total_cost') : 0;
            $material_total_cost = $this->input->get('material_total_cost') ? $this->input->get('material_total_cost') : 0;
            $color_total_cost = $this->input->get('color_total_cost') ? $this->input->get('color_total_cost') : 0;
            $color_name = $this->input->get('color_name');
            $upcharge_name = $this->input->get('upcharge_name');
            $material_name = $this->input->get('material_name');
        }
        $combo_items = false;
        if ($product = $this->pos_model->getProductByCode($code)) {
            unset($product->cost, $product->details);
            $product->qty = 1;
            $product->comment = '';
            $product->discount = '0';
            $product->price = $product->store_price > 0 ? $product->store_price : $product->price;
            $product->real_unit_price = $product->price;
            $product->unit_price = $product->tax ? ($product->price + (($product->price * $product->tax) / 100)) : $product->price;
            $product->unit_price = $product->unit_price + $upchrges_total_cost + $color_total_cost + $material_total_cost;
            $product->upchrges_total_cost = $upchrges_total_cost;
            $product->material_total_cost = $material_total_cost;
            $product->color_total_cost = $color_total_cost;
            $product->color_name = $color_name;
            $product->upcharge_name = $upcharge_name;
            $product->material_name = $material_name;


            if ($product->type == 'combo') {
                $combo_items = $this->pos_model->getComboItemsByPID($product->id);
            }
            echo json_encode(array('id' => str_replace(".", "", microtime(true)), 'item_id' => $product->id, 'label' => $product->name . " (" . $product->code . ")", 'row' => $product, 'combo_items' => $combo_items));
        } else {
            echo null;
        }
    }

    //added by itsea
    public function addClothtoList()
    {
        $selectedClothType = $this->input->post('selectedClothType');
        $clothPrice = $this->input->post('clothPrice');
        $clothName = $this->input->post('clothName');
        $clothId = $this->input->post('clothId');
        $patternName = $this->input->post('patternName');
        $patternPrice = $this->input->post('patternPrice');
        $materialName = $this->input->post('materialName');
        $materialPrice = $this->input->post('materialPrice');
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);

        $rows = $this->pos_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                unset($row->cost, $row->details);
                $row->qty = 1;
                $row->comment = '';
                $row->discount = '0';
                $row->price = $row->store_price > 0 ? $row->store_price : $row->price;
                $row->real_unit_price = $row->price;
                $row->unit_price = $row->tax ? ($row->price + (($row->price * $row->tax) / 100)) : $row->price;
                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                }
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function registers()
    {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $this->data, $meta);
    }

    public function open_register()
    {
        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id' => $this->session->userdata('user_id'),
                'store_id' => $this->session->userdata('store_id'),
                'status' => 'open',
            );
        }
        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            redirect("pos");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $this->data, $meta);
        }
    }

    public function close_register($user_id = null)
    {
        if (!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $rid = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $cash_in_hand = $user_register ? $user_register->cash_in_hand : $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            } else {
                $rid = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
                $register_open_time = $this->session->userdata('register_open_time');
                $cash_in_hand = $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            }

            $data = array('closed_at' => date('Y-m-d H:i:s'),
                'total_cash' => $total_cash,
                'total_cheques' => $chsales->total_cheques,
                'total_cc_slips' => $ccsales->total_cc_slips,
                'total_cash_submitted' => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted' => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note' => $this->input->post('note'),
                'status' => 'close',
                'transfer_opened_bills' => $this->input->post('transfer_opened_bills'),
                'closed_by' => $this->session->userdata('user_id'),
            );

            // $this->tec->print_arrays($data);
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            redirect("pos");
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->unset_userdata('register_id');
            $this->session->unset_userdata('cash_in_hand');
            $this->session->unset_userdata('register_open_time');
            $this->session->set_flashdata('message', lang("register_closed"));
            redirect("welcome");
        } else {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : null;
                $this->data['register_open_time'] = $user_register ? $register_open_time : null;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = null;
                $this->data['register_open_time'] = null;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['other_sales'] = $this->pos_model->getRegisterOtherSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
            $this->data['users'] = $this->tec->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    public function ajaxproducts($category_id = null, $return = null)
    {

        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } elseif (!$category_id) {
            $category_id = $this->Settings->default_category;
        }
        if ($this->input->get('per_page') == 'n') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('tcp') == 1) {
            $tcp = true;
        } else {
            $tcp = false;
        }

        $products = $this->pos_model->fetch_products($category_id, $this->Settings->pro_limit, $page);
        $pro = 1;
        $prods = "<div>";
        if ($products) {
            if ($this->Settings->bsty == 1) {
                foreach ($products as $product) {
                    $count = $product->id;
                    if ($count < 10) {
                        $count = "0" . ($count / 100) * 100;
                    }
                    if ($category_id < 10) {
                        $category_id = "0" . ($category_id / 100) * 100;
                    }
                    $prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-name btn-default btn-flat product\">" . $product->name . "</button>";
                    $pro++;
                }
            } elseif ($this->Settings->bsty == 2) {
                foreach ($products as $product) {
                    $count = $product->id;
                    if ($count < 10) {
                        $count = "0" . ($count / 100) * 100;
                    }
                    if ($category_id < 10) {
                        $category_id = "0" . ($category_id / 100) * 100;
                    }
                    $prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-img btn-flat product\"><img src=\"" . base_url() . "uploads/thumbs/" . $product->image . "\" alt=\"" . $product->name . "\" style=\"width: 110px; height: 110px;\"></button>";
                    $pro++;
                }
            } elseif ($this->Settings->bsty == 3) {
                foreach ($products as $product) {
                    $count = $product->id;
                    if ($count < 10) {
                        $count = "0" . ($count / 100) * 100;
                    }
                    if ($category_id < 10) {
                        $category_id = "0" . ($category_id / 100) * 100;
                    }
                    $prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-both btn-flat product\"><span class=\"bg-img\"><img src=\"" . base_url() . "uploads/thumbs/" . $product->image . "\" alt=\"" . $product->name . "\" style=\"width: 100px; height: 100px;\"></span><span><span>" . $product->name . "</span></span></button>";
                    $pro++;
                }
            }
        } else {
            $prods .= '<h4 class="text-center text-info" style="margin-top:50px;">' . lang('category_is_empty') . '</h4>';
        }

        $prods .= "</div>";

        if (!$return) {
            if (!$tcp) {
                echo $prods;
            } else {
                $category_products = $this->pos_model->products_count($category_id);
                header('Content-Type: application/json');
                echo json_encode(array('products' => $prods, 'tcp' => $category_products));
            }
        } else {
            return $prods;
        }
    }

    function invoice_barcode($product_code = NULL, $bcs = 'code128', $height = 60)
    {
        return $this->tec->barcode($product_code, $bcs, $height);
    }

    public function view($sale_id = null, $noprint = null)
    {
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getSaleByID($sale_id);
        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        } elseif ($this->session->userdata('store_id') != $inv->store_id) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('welcome');
        }
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
//        $this->data['store'] = $this->site->getStoreByID($inv->store_id);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['noprint'] = $noprint;
        $this->data['modal'] = $noprint ? true : false;
        $this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->site->getPrinterByID($this->Settings->printer);
        $this->data['store'] = $this->site->getStoreByID($inv->store_id);
        $this->data['page_title'] = lang("invoice");
        $this->data['sale'] = $this->sales_model->getSaleByID($sale_id);

        $this->data['invoice_id'] = '#' . $sale_id;
        $this->data['barcode'] = $this->invoice_barcode($this->data['invoice_id'], 'code128', 20);

        // $this->tec->print_arrays($this->data);
//        var_dump($this->data['payments'][0]->pos_balance);
//        die();

        $this->load->model('services_model');
        $service = array();
        $service['TicketId'] = $sale_id;
        $service['Location'] = 'USA';
        $service['PrinterId'] = $this->data['printer']->id;
        $service['CashDrawer'] = '';
        $service['PrinterType'] = $this->data['printer']->type;

        $DataToPrint = array();
        $DataToPrint['Barcode'] = $this->data['barcode'];
        $DataToPrint['PcsCount'] = '';
        $DataToPrint['PickUp'] = $this->data['sale']->pickup_date;
        $DataToPrint['Customer'] = $this->data['customer']->name;
        $DataToPrint['Phone'] = $this->data['customer']->phone;
        $items_array = $this->data['rows'];
        $product_id_array = array();
        $product_array = array();
        $i = 0;
        foreach ($items_array as $item) {
            $product_id = $item->product_id;
            $product_id_array[$i] = $product_id;
            $product_array[$product_id] = array();
            $product_array[$product_id]['Qty'] = 1;
            $product_array[$product_id]['Item_Name'] = $item->product_name;
            $product_array[$product_id]['Item_Price'] = $item->real_unit_price;
            $i++;
        }

        $final_id_array = array_count_values($product_id_array);
        foreach ($final_id_array as $key=>$val) {
            $product_array[$key]['Qty'] = $val;
        }
        $DataToPrint['Items'] = $product_array;

        $DataToPrint['Subtotal'] = $this->data['sale']->total;
        $DataToPrint['SalesTax'] = $this->data['sale']->order_tax;
        $DataToPrint['Eco'] = $this->data['sale']->product_tax;
        $DataToPrint['Net'] = $this->data['sale']->grand_total;
        $DataToPrint['Paid'] = $this->data['sale']->paid;
        $DataToPrint['Balance'] = $this->data['payments'][0]->pos_balance;

        $service['DataToPrint'] = json_encode($DataToPrint);

        $this->data['service'] = $service;

        $test = $this->services_model->insertData($sale_id, $service);
//        var_dump($test);
//        die();

//        $xml_dom = new DOMDocument();
//        $xml_dom->encoding = 'utf-8';
//        $xml_dom->xmlVersion = '1.0';
//        $xml_dom->xmlStandalone = 'yes';
//        $xml_dom->formatOutput = true;
//
//        $root = $xml_dom->createElement('DOCUMENT');
//        $barcode = $xml_dom -> createElement('BARCODE');
//        $root -> appendChild($barcode);
//        $xml_dom->appendChild($root);

//        var_dump($xml_dom);
//        die();

        $this->load->view($this->theme . 'pos/' . ($this->Settings->print_img ? 'eview' : 'view'), $this->data);
    }

    public function email_receipt($sale_id = null, $to = null)
    {
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        if (!$sale_id || !$to) {
            die();
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getSaleByID($sale_id);
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['noprint'] = null;
        $this->data['page_title'] = lang('invoice');
        $this->data['modal'] = false;
        $this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);

        $receipt = $this->load->view($this->theme . 'pos/view', $this->data, true);
        $message = preg_replace('#\<!-- start -->(.+)\<!-- end -->#Usi', '', $receipt);
        $subject = lang('email_subject') . ' - ' . $this->Settings->site_name;

        try {
            if ($this->tec->send_email($to, $subject, $message)) {
                echo json_encode(array('msg' => lang("email_success")));
            } else {
                echo json_encode(array('msg' => lang("email_failed")));
            }
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function register_details()
    {

        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['other_sales'] = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    public function today_sale()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getTodayCCSales();
        $this->data['cashsales'] = $this->pos_model->getTodayCashSales();
        $this->data['chsales'] = $this->pos_model->getTodayChSales();
        $this->data['other_sales'] = $this->pos_model->getTodayOtherSales();
        $this->data['gcsales'] = $this->pos_model->getTodayGCSales();
        $this->data['stripesales'] = $this->pos_model->getTodayStripeSales();
        $this->data['totalsales'] = $this->pos_model->getTodaySales();
        // $this->data['expenses'] = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    public function shortcuts()
    {
        $this->load->view($this->theme . 'pos/shortcuts', $this->data);
    }

    public function view_bill()
    {
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    public function promotions()
    {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    public function stripe_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->model('stripe_payments');
        return $this->stripe_payments->get_balance();
    }

    public function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }
        //$this->load->helper('cookie');
        $folder = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            $cookie = array(
                'name' => 'language',
                'value' => $lang,
                'expire' => '31536000',
                'prefix' => 'spos_',
                'secure' => false,
            );

            $this->input->set_cookie($cookie);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function validate_gift_card($no)
    {
        if ($gc = $this->pos_model->getGiftCardByNO(urldecode($no))) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    echo json_encode($gc);
                } else {
                    echo json_encode(false);
                }
            } else {
                echo json_encode($gc);
            }
        } else {
            echo json_encode(false);
        }
    }

    public function print_register($re = null)
    {

        if ($this->session->userdata('register_id')) {

            $register = $this->pos_model->registerData();
            $ccsales = $this->pos_model->getRegisterCCSales();
            $cashsales = $this->pos_model->getRegisterCashSales();
            $chsales = $this->pos_model->getRegisterChSales();
            $other_sales = $this->pos_model->getRegisterOtherSales();
            $gcsales = $this->pos_model->getRegisterGCSales();
            $stripesales = $this->pos_model->getRegisterStripeSales();
            $totalsales = $this->pos_model->getRegisterSales();
            $expenses = $this->pos_model->getRegisterExpenses();
            $user = $this->site->getUser();

            $total_cash = $cashsales->paid ? ($cashsales->paid + $register->cash_in_hand) : $register->cash_in_hand;
            $total_cash -= ($expenses->total ? $expenses->total : 0);
            $info = array(
                (object)array('label' => lang('opened_at'), 'value' => $this->tec->hrld($register->date)),
                (object)array('label' => lang('cash_in_hand'), 'value' => $register->cash_in_hand),
                (object)array('label' => lang('user'), 'value' => $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'),
                (object)array('label' => lang('printed_at'), 'value' => $this->tec->hrld(date('Y-m-d H:i:s'))),
            );

            $reg_totals = array(
                (object)array('label' => lang('cash_sale'), 'value' => $this->tec->formatMoney($cashsales->paid ? $cashsales->paid : '0.00') . ' (' . $this->tec->formatMoney($cashsales->total ? $cashsales->total : '0.00') . ')'),
                (object)array('label' => lang('ch_sale'), 'value' => $this->tec->formatMoney($chsales->paid ? $chsales->paid : '0.00') . ' (' . $this->tec->formatMoney($chsales->total ? $chsales->total : '0.00') . ')'),
                (object)array('label' => lang('gc_sale'), 'value' => $this->tec->formatMoney($gcsales->paid ? $gcsales->paid : '0.00') . ' (' . $this->tec->formatMoney($gcsales->total ? $gcsales->total : '0.00') . ')'),
                (object)array('label' => lang('cc_sale'), 'value' => $this->tec->formatMoney($ccsales->paid ? $ccsales->paid : '0.00') . ' (' . $this->tec->formatMoney($ccsales->total ? $ccsales->total : '0.00') . ')'),
                (object)array('label' => lang('stripe'), 'value' => $this->tec->formatMoney($stripesales->paid ? $stripesales->paid : '0.00') . ' (' . $this->tec->formatMoney($stripesales->total ? $stripesales->total : '0.00') . ')'),
                (object)array('label' => lang('other_sale'), 'value' => $this->tec->formatMoney($other_sales->paid ? $other_sales->paid : '0.00') . ' (' . $this->tec->formatMoney($other_sales->total ? $other_sales->total : '0.00') . ')'),
                (object)array('label' => 'line', 'value' => ''),
                (object)array('label' => lang('total_sales'), 'value' => $this->tec->formatMoney($totalsales->paid ? $totalsales->paid : '0.00') . ' (' . $this->tec->formatMoney($totalsales->total ? $totalsales->total : '0.00') . ')'),
                (object)array('label' => lang('cash_in_hand'), 'value' => $this->tec->formatMoney($register->cash_in_hand)),
                (object)array('label' => lang('expenses'), 'value' => $this->tec->formatMoney($expenses->total ? $expenses->total : '0.00')),
                (object)array('label' => 'line', 'value' => ''),
                (object)array('label' => lang('total_cash'), 'value' => $this->tec->formatMoney($total_cash)),
            );

            $data = (object)array(
                'printer' => $this->Settings->local_printers ? '' : json_encode($printer),
                'logo' => !empty($store->logo) ? base_url('uploads/' . $store->logo) : '',
                'heading' => lang('register_details'),
                'info' => $info,
                'totals' => $reg_totals,
            );

            // $this->tec->print_arrays($data);
            if ($re == 1) {
                return $data;
            } elseif ($re == 2) {
                echo json_encode($data);
            } else {
                $printer = $this->site->getPrinterByID($this->Settings->printer);
                $this->load->library('escpos');
                $this->escpos->load($printer);
                $this->escpos->print_data($data);
                echo json_encode(true);
            }
        } else {
            echo json_encode(false);
        }
    }

    public function print_receipt($id, $open_drawer = false)
    {

        $sale = $this->pos_model->getSaleByID($id);
        $items = $this->pos_model->getAllSaleItems($id);
        $payments = $this->pos_model->getAllSalePayments($id);
        $store = $this->site->getStoreByID($sale->store_id);
        $created_by = $this->site->getUser($sale->created_by);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_receipt($store, $sale, $items, $payments, $created_by, $open_drawer);
    }

    public function receipt_img()
    {

        $data = $this->input->post('img', true);
        $filename = date('Y-m-d-H-i-s-') . uniqid() . '.png';
        $cd = !empty($this->input->post('cd')) ? true : false;
        $imgData = str_replace(' ', '+', $data);
        $imgData = base64_decode($imgData);
        file_put_contents('files/receipts/' . $filename, $imgData);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_img($filename, $cd);
    }

    public function open_drawer()
    {

        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->open_drawer();
    }

    public function p($bo = 'order')
    {

        $date = date('Y-m-d H:i:s');
        $customer_id = $this->input->post('customer_id');
        $pickup_date = $this->input->post('pickup_date');
        $customer_details = $this->pos_model->getCustomerByID($customer_id);
        $customer = $customer_details->name;
        $note = $this->tec->clear_tags($this->input->post('spos_note'));

        $total = 0;
        $product_tax = 0;
        $order_tax = 0;
        $product_discount = 0;
        $order_discount = 0;
        $percentage = '%';
        $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
        for ($r = 0; $r < $i; $r++) {
            $item_id = $_POST['product_id'][$r];
            $real_unit_price = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
            $item_quantity = $_POST['quantity'][$r];
            $item_comment = $_POST['item_comment'][$r];
            $item_ordered = $_POST['item_was_ordered'][$r];
            $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

            if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                $product_details = $this->site->getProductByID($item_id);
                if ($product_details) {
                    $product_name = $product_details->name;
                    $product_code = $product_details->code;
                    $product_cost = $product_details->cost;
                } else {
                    $product_name = $_POST['product_name'][$r];
                    $product_code = $_POST['product_code'][$r];
                    $product_cost = 0;
                }
                if (!$this->Settings->overselling) {
                    if ($product_details->type == 'standard') {
                        if ($product_details->quantity < $item_quantity) {
                            $this->session->set_flashdata('error', lang("quantity_low") . ' (' .
                                lang('name') . ': ' . $product_details->name . ' | ' .
                                lang('ordered') . ': ' . $item_quantity . ' | ' .
                                lang('available') . ': ' . $product_details->quantity .
                                ')');
                            redirect("pos");
                        }
                    } elseif ($product_details->type == 'combo') {
                        $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id);
                            if ($cpr->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low") . ' (' .
                                    lang('name') . ': ' . $cpr->name . ' | ' .
                                    lang('ordered') . ': ' . $item_quantity . ' x ' . $combo_item->qty . ' = ' . $item_quantity * $combo_item->qty . ' | ' .
                                    lang('available') . ': ' . $cpr->quantity .
                                    ') ' . $product_details->name);
                                redirect("pos");
                            }
                        }
                    }
                }
                $unit_price = $real_unit_price;

                $pr_discount = 0;
                if (isset($item_discount)) {
                    $discount = $item_discount;
                    $dpos = strpos($discount, $percentage);
                    if ($dpos !== false) {
                        $pds = explode("%", $discount);
                        $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                    } else {
                        $pr_discount = $this->tec->formatDecimal($discount);
                    }
                }
                $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                $item_net_price = $unit_price;
                $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                $product_discount += $pr_item_discount;

                $pr_item_tax = 0;
                $item_tax = 0;
                $tax = "";
                if (isset($product_details->tax) && $product_details->tax != 0) {

                    if ($product_details && $product_details->tax_method == 1) {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                        $tax = $product_details->tax . "%";
                    } else {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                        $tax = $product_details->tax . "%";
                        $item_net_price -= $item_tax;
                    }

                    $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);
                }

                $product_tax += $pr_item_tax;
                $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                $products[] = (object)array(
                    'product_id' => $item_id,
                    'quantity' => $item_quantity,
                    'unit_price' => $unit_price,
                    'net_unit_price' => $item_net_price,
                    'discount' => $item_discount,
                    'comment' => $item_comment,
                    'item_discount' => $pr_item_discount,
                    'tax' => $tax,
                    'item_tax' => $pr_item_tax,
                    'subtotal' => $subtotal,
                    'real_unit_price' => $real_unit_price,
                    'cost' => $product_cost,
                    'product_code' => $product_code,
                    'product_name' => $product_name,
                    'ordered' => $item_ordered,
                );

                $total += $item_net_price * $item_quantity;
            }
        }
        if (empty($products)) {
            $this->form_validation->set_rules('product', lang("order_items"), 'required');
        } else {
            krsort($products);
        }

        if ($this->input->post('order_discount')) {
            $order_discount_id = $this->input->post('order_discount');
            $opos = strpos($order_discount_id, $percentage);
            if ($opos !== false) {
                $ods = explode("%", $order_discount_id);
                $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
            } else {
                $order_discount = $this->tec->formatDecimal($order_discount_id);
            }
        } else {
            $order_discount_id = null;
        }
        $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

        if ($this->input->post('order_tax')) {
            $order_tax_id = $this->input->post('order_tax');
            $opos = strpos($order_tax_id, $percentage);
            if ($opos !== false) {
                $ots = explode("%", $order_tax_id);
                $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
            } else {
                $order_tax = $this->tec->formatDecimal($order_tax_id);
            }
        } else {
            $order_tax_id = null;
            $order_tax = 0;
        }

        $total_tax = $this->tec->formatDecimal(($product_tax + $order_tax), 4);
        $grand_total = $this->tec->formatDecimal(($this->tec->formatDecimal($total) + $total_tax - $order_discount), 4);
        $paid = 0;
        $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
        $rounding = $this->tec->formatDecimal(($round_total - $grand_total));

        $data = (object)array('date' => $date,
            'customer_id' => $customer_id,
            'customer_name' => $customer,
            'total' => $this->tec->formatDecimal($total),
            'product_discount' => $this->tec->formatDecimal($product_discount, 4),
            'order_discount_id' => $order_discount_id,
            'order_discount' => $order_discount,
            'total_discount' => $total_discount,
            'product_tax' => $this->tec->formatDecimal($product_tax, 4),
            'order_tax_id' => $order_tax_id,
            'order_tax' => $order_tax,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
            'total_items' => $this->input->post('total_items'),
            'total_quantity' => $this->input->post('total_quantity'),
            'rounding' => $rounding,
            'paid' => $paid,
            'created_by' => $this->session->userdata('user_id'),
            'note' => $note,
            'hold_ref' => $this->input->post('hold_ref'),
        );

        // $this->tec->print_arrays($data, $products);
        $store = $this->site->getStoreByID($this->session->userdata('store_id'));
        $created_by = $this->site->getUser($this->session->userdata('user_id'));

        if ($bo == 'bill') {
            $printer = $this->site->getPrinterByID($this->Settings->printer);
            $this->load->library('escpos');
            $this->escpos->load($printer);
            $this->escpos->print_receipt($store, $data, $products, false, $created_by, false, true);
        } else {
            $order_printers = json_decode($this->Settings->order_printers);
            $this->load->library('escpos');
            foreach ($order_printers as $printer_id) {
                $printer = $this->site->getPrinterByID($printer_id);
                $this->escpos->load($printer);
                $this->escpos->print_order($store, $data, $products, $created_by);
            }
        }
    }

    /* myone from */

    public function validate_sec_pin($secPin = null)
    {

        if ($this->input->get('secPin')) {
            $secPin = $this->input->get('secPin');

            if ($this->pos_model->getUserPin($this->session->userdata('user_id'), $secPin)) {
                echo json_encode(array('secPin' => $secPin, 'success' => true, 'staff' => $this->pos_model->getUserPin($this->session->userdata('user_id'), $secPin)));
            } else {
                echo json_encode(array('secPin' => 0, 'success' => false));
            }
        } else {
            echo json_encode(array('secPin' => 0, 'success' => false));
        }
    }

    public function checkCustomer()
    {

        if ($this->input->get('cname')) {
            $cname = $this->input->get('cname');
            $op = $this->pos_model->findCustomer($cname);
            if ($op) {
                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('success' => false));
            }
        } else {
            echo json_encode(array('success' => false));
        }
    }

    public function printDate()
    {
        $this->load->model('sales_model');
        $date_print = $_GET['print_date'];
        $print_id = $_GET['print_id'];

        $this->sales_model->updatePrintDateTick($print_id, $date_print);
        // echo $date_print;
    }
    /* myone to */
}
