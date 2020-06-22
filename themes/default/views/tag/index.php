<?php (defined('BASEPATH')) or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title><?= $page_title . ' | ' . $Settings->site_name; ?></title>
        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png" />
        <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>dist/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css" />
        <?= $Settings->rtl ? '<link href="' . $assets . 'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
        <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <style type="text/css" media="all">
            .date {display:none;}
            #print-section .customer-name {
                font-size: 20px;
            }
            @media print {
                .no-print, .modal { display: none; }
                .date {display:block;}
                .tag, .wrapper, .content-wrapper, .tag-print, .tag-print .row, #print-section { 
                    width: 288px!important; margin: 0!important; padding: 0!important;
                }
                #print-section img {
                    width: 164px;
                    height: 55px;
                }
                #print-section h5, #print-section h3 {
                    font-weight: 700;
                }
                .tag-item {
                    width: 288px!important; 
                    margin-top: 0;
                    margin-bottom: 0;
                }
                .bottom_line{
                    padding-bottom: 5px;
                    margin-bottom: 5px;
                    border-style:  hidden hidden  dotted hidden ;
                }
              
            }
            #my_camera{
                width: 340px;
                height: 240px;
                border: 1px solid black;
            }
            #tags_div{
                padding-left:0px;
                padding-right:0px;
            }
        </style>
    </head>

    <body class="skin-<?= $Settings->theme_style; ?> sidebar-collapse sidebar-mini tag">
        <div class="wrapper rtl rtl-inv">
            <header class="main-header no-print">
                <a href="<?= site_url(); ?>/pos" class="logo">
                    <?php if ($store) { ?>
                        <span class="logo-mini">TAG</span>
                        <span class="logo-lg hide"><?= $store->name == 'SimplePOS' ? 'Simple<b>POS</b>' : $store->name; ?></span>
                    <?php } else { ?>
                        <span class="logo-mini">TAG</span>
                        <span
                            class="logo-lg hide"><?= $Settings->site_name == 'SimplePOS' ? 'Simple<b>PICK</b>' : $Settings->site_name; ?></span>
                        <?php } ?>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <ul class="nav navbar-nav pull-left">
                        <li class="dropdown hide">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img
                                    src="<?= $assets; ?>images/<?= $Settings->selected_language; ?>.png"
                                    alt="<?= $Settings->selected_language; ?>"></a>
                            <ul class="dropdown-menu">
                                <?php
                                $scanned_lang_dir = array_map(function ($path) {
                                    return basename($path);
                                }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                                foreach ($scanned_lang_dir as $entry) {
                                    ?>
                                    <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img
                                                src="<?= $assets; ?>images/<?= $entry; ?>.png" class="language-img">
                                            &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li><a href="/pos" class="btn btn-primary">POS</a></li>
                            <li><a href="#" class="clock"></a></li>
                            <li class="hide"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li>
                            <?php if ($Admin) { ?>
                                <li class="hide"><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
                            <?php } ?>
                            <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                                <li class="hide"><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-desktop"></i></a>
                                </li>
                            <?php } ?>
                            <li class="hidden-xs hidden-sm hide"><a href="<?= site_url('pos/shortcuts'); ?>" data-toggle="ajax"><i
                                        class="fa fa-key"></i></a></li>
                            <li class="hide"><a href="<?= site_url('pos/register_details'); ?>"
                                   data-toggle="ajax"><?= lang('register_details'); ?></a></li>
                                <?php if ($Admin) { ?>
                                <li class="hide"><a href="<?= site_url('pos/today_sale'); ?>" data-toggle="ajax"><?= lang('today_sale'); ?></a>
                                </li>
                            <?php } ?>
                            <li class="hide"><a href="<?= site_url('pos/close_register'); ?>"
                                   data-toggle="ajax"><?= lang('close_register'); ?></a></li>
                                <?php if ($suspended_sales) { ?>
                                <li class="dropdown notifications-menu" id="suspended_sales">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bell-o"></i>
                                        <span class="label label-warning"><?= sizeof($suspended_sales); ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header">
                                            <input type="text" autocomplete="off" data-list=".list-suspended-sales"
                                                   name="filter-suspended-sales" id="filter-suspended-sales"
                                                   class="form-control input-sm kb-text clearfix"
                                                   placeholder="<?= lang('filter_by_reference'); ?>">
                                        </li>
                                        <li>
                                            <ul class="menu">
                                                <li class="list-suspended-sales">
                                                    <?php
                                                    foreach ($suspended_sales as $ss) {
                                                        echo '<a href="' . site_url('pos/?hold=' . $ss->id) . '" class="load_suspended">' . $this->tec->hrld($ss->date) . ' (' . $ss->customer_name . ')<br><div class="bold">' . $ss->hold_ref . '</div></a>';
                                                    }
                                                    ?>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="dropdown user user-menu hide">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?= base_url('uploads/avatars/thumbs/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>"
                                         class="user-image" alt="Avatar" />
                                    <span><?= $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'); ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?= base_url('uploads/avatars/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>"
                                             class="img-circle" alt="Avatar" />
                                        <p>
                                            <?= $this->session->userdata('email'); ?>
                                            <small><?= lang('member_since') . ' ' . $this->session->userdata('created_on'); ?></small>
                                        </p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>"
                                               class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= site_url('logout'); ?>"
                                               class="btn btn-default btn-flat<?= $this->session->userdata('register_id') ? ' sign_out' : ''; ?>"><?= lang('sign_out'); ?></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="<?= site_url('logout'); ?>" class="sidebar-icon <?= $this->session->userdata('register_id') ? ' sign_out' : ''; ?>"><i class="fa fa-sign-out sidebar-icon"></i></a>
                            </li>
                            <li><a href="#" class="current-staff"></a></li>
                            <li>
                                <a href="/pos" class="sidebar-icon" id="lock_staff"><i class="fa fa-lock sidebar-icon"></i></a>
                            </li>
                            <li>
                                <a href="#" data-toggle="control-sidebar" class="sidebar-icon"><i class="fa fa-folder sidebar-icon"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar no-print">
                <section class="sidebar">
                    <ul class="sidebar-menu">
                        <li class="mm_welcome"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i>
                                <span><?= lang('dashboard'); ?></span></a></li>
                        <?php if ($Settings->multi_store && !$this->session->userdata('store_id')) { ?>
                            <li class="mm_stores"><a href="<?= site_url('stores'); ?>"><i class="fa fa-building-o"></i>
                                    <span><?= lang('stores'); ?></span></a></li>
                        <?php } ?>
                        <li class="mm_pos"><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i>
                                <span><?= lang('pos'); ?></span></a></li>
                                
                        <li class="mm_pickup"><a href="<?= site_url('pickup'); ?>"><i class="fa fa-check"></i>
                                <span>Pick up</span></a></li>
                                
                        <li class="mm_tag"><a href="<?= site_url('tag'); ?>"><i class="fa fa-tags"></i>
                                <span>Tagging</span></a></li>

                        <li class="mm_rack"><a href="<?= site_url('rack'); ?>"><i class="fa fa-comment"></i>
                                <span>Racking</span></a></li>

                        <?php if ($Admin) { ?>
                            <li class="treeview mm_products">
                                <a href="#">
                                    <i class="fa fa-barcode"></i>
                                    <span><?= lang('products'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="products_index"><a href="<?= site_url('products'); ?>"><i class="fa fa-circle-o"></i>
                                            <?= lang('list_products'); ?></a></li>
                                    <li id="products_add"><a href="<?= site_url('products/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_product'); ?></a></li>
                                    <li id="products_import"><a href="<?= site_url('products/import'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('import_products'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="products_print_barcodes">
                                        <a onclick="window.open('<?= site_url('products/print_barcodes'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;"
                                           href="#"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a>
                                    </li>
                                    <li id="products_print_labels">
                                        <a onclick="window.open('<?= site_url('products/print_labels'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;"
                                           href="#"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="treeview mm_categories">
                                <a href="#">
                                    <i class="fa fa-folder"></i>
                                    <span><?= lang('categories'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="categories_index"><a href="<?= site_url('categories'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_categories'); ?></a></li>
                                    <li id="categories_add"><a href="<?= site_url('categories/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_category'); ?></a></li>
                                    <li id="categories_import"><a href="<?= site_url('categories/import'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('import_categories'); ?></a></li>
                                </ul>
                            </li>
                            <?php if ($this->session->userdata('store_id')) { ?>
                                <li class="treeview mm_sales">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span><?= lang('sales'); ?></span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i>
                                                <?= lang('list_sales'); ?></a></li>
                                        <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                                    </ul>
                                </li>
                                <li class="treeview mm_purchases">
                                    <a href="#">
                                        <i class="fa fa-plus"></i>
                                        <span><?= lang('purchases'); ?></span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="purchases_index"><a href="<?= site_url('purchases'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('list_purchases'); ?></a></li>
                                        <li id="purchases_add"><a href="<?= site_url('purchases/add'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('add_purchase'); ?></a></li>
                                        <li class="divider"></li>
                                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="treeview mm_gift_cards">
                                <a href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span><?= lang('gift_cards'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                                    <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                                </ul>
                            </li>

                            <li class="treeview mm_auth mm_customers mm_suppliers">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span><?= lang('people'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="auth_users"><a href="<?= site_url('users'); ?>"><i class="fa fa-circle-o"></i>
                                            <?= lang('list_users'); ?></a></li>
                                    <li id="auth_add"><a href="<?= site_url('users/add'); ?>"><i class="fa fa-circle-o"></i>
                                            <?= lang('add_user'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                                    <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="suppliers_index"><a href="<?= site_url('suppliers'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                                    <li id="suppliers_add"><a href="<?= site_url('suppliers/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                                </ul>
                            </li>
                            
                            <li class="treeview mm_cloths">
                                <a href="#">
                                    <i class="fa fa-cog"></i>
                                    <span><?= lang('cloth_settings'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="cloths_list_colors"><a href="<?= site_url('cloths/colors'); ?>"><i class="fa fa-circle-o"></i> List Colors </a></li>
                                    <li id="cloths_add_color"><a href="<?= site_url('cloths/add_color'); ?>"><i class="fa fa-circle-o"></i> Add Color </a></li>
                                    <li id="cloths_import_color"><a href="<?= site_url('cloths/import_color'); ?>"><i class="fa fa-circle-o"></i> Import Colors </a></li>
                                    <li class="divider"></li>

                                    <li id="cloths_list_upcharges"><a href="<?= site_url('cloths/upcharges'); ?>"><i class="fa fa-circle-o"></i> List Upcharges </a></li>
                                    <li id="cloths_add_upcharge"><a href="<?= site_url('cloths/add_upcharge'); ?>"><i class="fa fa-circle-o"></i> Add Upcharge </a></li>
                                    <li id="cloths_import_upcharge"><a href="<?= site_url('cloths/import_upcharge'); ?>"><i class="fa fa-circle-o"></i> Import Upcharges </a></li>
                                    <li class="divider"></li>

                                    <li id="cloths_list_spotlists"><a href="<?= site_url('cloths/spotlists'); ?>"><i class="fa fa-circle-o"></i> List Spot/Damages </a></li>
                                    <li id="cloths_add_spotlist"><a href="<?= site_url('cloths/add_spotlist'); ?>"><i class="fa fa-circle-o"></i> Add Spot/Damage </a></li>
                                    <li id="cloths_import_spotlist"><a href="<?= site_url('cloths/import_spotlist'); ?>"><i class="fa fa-circle-o"></i> Import Spot/Damages </a></li>
                                    <li class="divider"></li>

                                    <li id="cloths_list_materials"><a href="<?= site_url('cloths/materials'); ?>"><i class="fa fa-circle-o"></i> List Materials </a></li>
                                    <li id="cloths_add_material"><a href="<?= site_url('cloths/add_material'); ?>"><i class="fa fa-circle-o"></i> Add Material </a></li>
                                    <li id="cloths_import_material"><a href="<?= site_url('cloths/import_material'); ?>"><i class="fa fa-circle-o"></i> Import Material </a></li>
                                </ul>
                            </li>

                            <li class="treeview mm_settings">
                                <a href="#">
                                    <i class="fa fa-cogs"></i>
                                    <span><?= lang('settings'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="settings_index"><a href="<?= site_url('settings'); ?>"><i class="fa fa-circle-o"></i>
                                            <?= lang('settings'); ?></a></li>
                                    <li class="divider"></li>
                                    <?php if ($Settings->multi_store) { ?>
                                        <li id="settings_stores"><a href="<?= site_url('settings/stores'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('stores'); ?></a></li>
                                        <li id="settings_add_store"><a href="<?= site_url('settings/add_store'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('add_store'); ?></a></li>
                                        <li class="divider"></li>
                                    <?php } ?>
                                    <li id="settings_printers"><a href="<?= site_url('settings/printers'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('printers'); ?></a></li>
                                    <li id="settings_add_printer"><a href="<?= site_url('settings/add_printer'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_printer'); ?></a></li>
                                    <li class="divider"></li>
                                    <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                                        <li id="settings_backups"><a href="<?= site_url('settings/backups'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('backups'); ?></a></li>
                                        <?php } ?>
                                    <!-- <li id="settings_updates"><a href="<?= site_url('settings/updates'); ?>"><i class="fa fa-circle-o"></i> <?= lang('updates'); ?></a></li> -->
                                </ul>
                            </li>
                            <li class="treeview mm_reports">
                                <a href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span><?= lang('reports'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="reports_daily_sales"><a href="<?= site_url('reports/daily_sales'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('daily_sales'); ?></a></li>
                                    <li id="reports_monthly_sales"><a href="<?= site_url('reports/monthly_sales'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('monthly_sales'); ?></a></li>
                                    <li id="reports_index"><a href="<?= site_url('reports'); ?>"><i class="fa fa-circle-o"></i>
                                            <?= lang('sales_report'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="reports_payments"><a href="<?= site_url('reports/payments'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('payments_report'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="reports_registers"><a href="<?= site_url('reports/registers'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('registers_report'); ?></a></li>
                                    <li class="divider"></li>
                                    <li id="reports_top_products"><a href="<?= site_url('reports/top_products'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('top_products'); ?></a></li>
                                    <li id="reports_products"><a href="<?= site_url('reports/products'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('products_report'); ?></a></li>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="mm_products"><a href="<?= site_url('products'); ?>"><i class="fa fa-barcode"></i>
                                    <span><?= lang('products'); ?></span></a></li>
                            <li class="mm_categories"><a href="<?= site_url('categories'); ?>"><i class="fa fa-folder-open"></i>
                                    <span><?= lang('categories'); ?></span></a></li>
                            <?php if ($this->session->userdata('store_id')) { ?>
                                <li class="treeview mm_sales">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span><?= lang('sales'); ?></span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i>
                                                <?= lang('list_sales'); ?></a></li>
                                        <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                                    </ul>
                                </li>
                                <li class="treeview mm_purchases">
                                    <a href="#">
                                        <i class="fa fa-plus"></i>
                                        <span><?= lang('expenses'); ?></span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i
                                                    class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="treeview mm_gift_cards">
                                <a href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span><?= lang('gift_cards'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                                    <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                                </ul>
                            </li>
                            <li class="treeview mm_customers">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span><?= lang('customers'); ?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                                    <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i
                                                class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            </aside>

            <div class="content-wrapper tag-panel d-flex">
                <table style="width:60%;" class="layout-table-s no-print">
                    <tr>
                        <td style="width: 100%;">
                            <div id="tag">
                                <?= form_open('pos', 'id="pos-sale-form"'); ?>
                                <div class="well well-sm" id="leftdiv">
                                    <div id="lefttop" style="margin-bottom:5px;">
                                        <div class="form-group">
                                            <label for="customer_sel_sec">Ticket Number</label>
                                            <div class="customer_sel_sec">
                                                <?php
                                                foreach ($customers as $customer) {
                                                    $cus[$customer->id] = $customer->text;
                                                }
                                                ?>
                                                <?= form_dropdown('customer_id', $cus, set_value('customer_id', $Settings->default_customer), 'id="spos_customer" data-placeholder="Search by Ticket number" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
                                                <!-- <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <i class="text-success fa fa-2x fa-check" id="confirmIcon"></i>
                                                </div> -->
                                                <!-- <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="#" id="add-customer" class="external" data-toggle="modal"
                                                       data-target="#myModal"><i class="fa fa-2x fa-plus-circle"
                                                                              id="addIcon"></i></a>
                                                </div> -->
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <div class="form-group">
                                            <?= lang('code', 'code'); ?> <?= lang('can_use_barcode'); ?>
                                            <?= form_input('code', set_value('code'), 'class="form-control tip" id="code" readonly="readonly" required="required"'); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="pickup_date">Target Date</label>
                                            <input type="text" name="pickup_date" class="form-control datepicker" id="target_date" style="height:34px; font-size: 18px;" value="">
                                        </div>
                                        <?php if ($eid && $Admin) { ?>
                                            <div class="form-group" style="margin-bottom:5px;">
                                                <?= form_input('date', set_value('date', $sale->date), 'id="date" required="required" class="form-control"'); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div id="pickup_details" class="pickup_details">
                                        
                                    </div>
                                    <div id="botbuttons" class="col-xs-12 text-left" style="display: none;">

                                        <div class="row" style="margin-top: 30px;">
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <label for="">Varify Customer Info Details</label>
                                            </div>
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <div class="col-sm-4"><label for="">Customer Name: </label></div>
                                                <div class="col-sm-8"><label for="" id="cus_name"></label></div>
                                            </div>
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <div class="col-sm-4"><label for="">Total Count: </label></div>
                                                <div class="col-sm-8"><label for="" id="total_num"></label></div>
                                            </div>
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <div class="col-sm-4"><label for="">Drop Date: </label></div>
                                                <div class="col-sm-8"><label for="" id="drop_dat"></label></div>
                                            </div>
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <button type="button" class="btn btn-info text-lg" 
                                                        id="tag_print" 
                                                        style="height:60px;">Verify & Print Tags</button>
                                            </div><!---------onclick="window.print();" --------->
                                           
                                            <!-- <div class="col-xs-4" style="padding: 0;">
                                                <h2 style="color: red; font-weight: 900; text-transform: uppercase;" id="cus_status"></h2>
                                                <h3 id="cus_name"></h3>
                                                <h4 id="cus_email"></h4>
                                                <h4 id="cus_phone"></h4>
                                                <h4 id="cus_hanger"></h4>
                                            </div>

                                            <div class="col-xs-4" style="padding: 0 15px;">
                                                <div class="d-flex justify-content-between">
                                                    <h4>Total:</h4><h4 id="total_amount"></h4>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h4>Order Tax:</h4><h4 id="order_tax"></h4>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h4>Grand Total:</h4><h4 id="grand_total"></h4>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h4>Paid Amount:</h4><h4 id="paid_amount"></h4>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h4 class="text-danger">Due Amount:</h4><h4 id="due_amount" class="text-danger"></h4>
                                                </div>
                                            </div>

                                            <div class="col-xs-4" style="padding: 0;">
                                                <button type="button" class="btn btn-success btn-block btn-flat text-lg"
                                                        id="payment_button"
                                                        style="height:67px;">Payment</button>
                                                <h3><i>Pick Up Date:  <span id="pickup_dat"></span></i></h3>
                                                <button type="button" class="btn btn-info btn-block btn-flat text-lg"
                                                        id="pickup_button"
                                                        style="height:67px;">Pick Up</button>
                                            </div> -->
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <div id="print" class="fixed-table-container" style="margin-top: 5px;">
                                        <div id="list-table-div">
                                            <!-- <table id="posTable"
                                                   class="table table-striped table-condensed table-hover list-table"
                                                   style="margin:0px;" data-height="100">
                                                    <thead>
                                                        <tr class="success">
                                                            <th style="width: 20%;text-align:center;"><?= lang('product') ?></th>
                                                            <th style="width: 10%;text-align:center;"><?= lang('price') ?></th>
                                                            <th style="width: 10%;text-align:center;"><?= lang('qty') ?></th>
                                                            <th style="width: 10%;text-align:center;">Color</th>
                                                            <th style="width: 15%;text-align:center;">Modifier</th>
                                                            <th style="width: 10%;text-align:center;"><?= lang('subtotal') ?></th>
                                                            <th style="width: auto;text-align:center;" class="satu">Pick Up</th>
                                                            <th style="width: auto;text-align:center;" class="satu">Re-order<i class="fa fa-level-up"></i></th>
                                                        </tr>
                                                    </thead>
                                                <tbody></tbody>
                                            </table> -->
                                        </div>
                                        <!-- <div style="clear:both;"></div>
                                        <div id="totaldiv">
                                            <table id="totaltbl" class="table table-condensed totals"
                                                   style="margin-bottom:10px;">
                                                <tbody>
                                                    <tr class="info">
                                                        <td width="25%"><?= lang('total_items') ?></td>
                                                        <td class="text-right" style="padding-right:10px;"><span
                                                                id="count">0</span></td>
                                                        <td width="25%"><?= lang('total') ?></td>
                                                        <td class="text-right" colspan="2"><span id="total">0</span></td>
                                                    </tr>
                                                    <tr class="info">
                                                        <td width="25%"><a href="#"
                                                                           id="add_discount"><?= lang('discount') ?></a></td>
                                                        <td class="text-right" style="padding-right:10px;"><span
                                                                id="ds_con">0</span></td>
                                                        <td width="25%"><a href="#" id="add_tax"><?= lang('order_tax') ?></a>
                                                        </td>
                                                        <td class="text-right"><span id="ts_con">0</span></td>
                                                    </tr>
                                                    <tr class="success">
                                                        <td colspan="2" style="font-weight:bold;">
                                                            <?= lang('total_payable') ?>
                                                            <a role="button" data-toggle="modal" data-target="#noteModal">
                                                                <i class="fa fa-comment"></i>
                                                            </a>
                                                        </td>
                                                        <td class="text-right" colspan="2" style="font-weight:bold;"><span
                                                                id="total-payable">0</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> -->
                                    </div>
                                    <div class="clearfix"></div>
                                    <span id="hidesuspend"></span>

                                    <div id="payment-con">
                                    <input type="hidden" name="spos_note" value="" id="spos_note">
                                        <input type="hidden" name="amount" id="amount_val"
                                               value="<?= $eid ? $sale->paid : ''; ?>" />
                                        <input type="hidden" name="balance_amount" id="balance_val" value="" />
                                        <input type="hidden" name="paid_by" id="paid_by_val" value="paid" />
                                        <input type="hidden" name="cc_no" id="cc_no_val" value="" />
                                        <input type="hidden" name="paying_gift_card_no" id="paying_gift_card_no_val"
                                               value="" />
                                        <input type="hidden" name="cc_holder" id="cc_holder_val" value="" />
                                        <input type="hidden" name="cheque_no" id="cheque_no_val" value="" />
                                        <input type="hidden" name="cc_month" id="cc_month_val" value="" />
                                        <input type="hidden" name="cc_year" id="cc_year_val" value="" />
                                        <input type="hidden" name="cc_type" id="cc_type_val" value="" />
                                        <input type="hidden" name="cc_cvv2" id="cc_cvv2_val" value="" />
                                        <input type="hidden" name="payment_note" id="payment_note_val" value="" />
                                    </div>
                                    <input type="hidden" name="customer" id="customer"
                                           value="<?= $Settings->default_customer ?>" />
                                    <input type="hidden" name="order_tax" id="tax_val" value="" />
                                    <input type="hidden" name="order_discount" id="discount_val" value="" />
                                    <input type="hidden" name="count" id="total_item" value="" />
                                    <input type="hidden" name="did" id="is_delete" value="<?= $sid; ?>" />
                                    <input type="hidden" name="eid" id="is_edit" value="<?= $eid; ?>" />
                                    <input type="hidden" name="total_items" id="total_items" value="0" />
                                    <input type="hidden" name="total_quantity" id="total_quantity" value="0" />
                                    <input type="hidden" name="items_detail" id="items_detail" value="" />
                                    <input type="hidden" name="pickup" id="pickup" value="" />
                                    <input type="hidden" name="created_by" id="created_by" value="" />
                                    <input type="submit" id="submit" value="Submit Sale" style="display: none;" />
                                </div>
                                <?= form_close(); ?>
                            </div>

                        </td>
                    </tr>
                </table>
                <div class="contents tag-print" id="right-col-s" style="width: 40%; margin: 0 auto; margin-top: 12px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-sm-5" id ="tags_div"  style="float:left padding-left: 0px; padding-right: 0px;">
                            <div class="col-sm-12 no-print" >
                                <label for="" class="w-100 text-center text-lg bg-primary">TAGS</label>
                            </div>
                            <div class="col-sm-12" id="print-section">
                        
                            </div>
                        </div>
                        <div class="col-sm-7 no-print" id="picture" style="float:right">
                            <div class="col-sm-12 no-print">
                                <label for="" class="w-100 text-center text-lg bg-primary">Snap Pictures</label>
                            </div>
                            <div class="col-sm-12" id="print-picture">
                                <div class="form-group">                         
                          
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="tag_modal" tabindex="-1" role="dialog"
     aria-labelledby="eModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-times"></i></button>
                <h4 class="modal-title" id="eModalLabel">
                    <?= lang('Note') ?>
                </h4>
            </div>
            <b><div style="margin-left:30px; font-type: bold; ">No found results!</div><b><div style="margin-left:30px ">You must photo.</div>
            
            <div class="modal-footer" style="margin-top:0;">
              
                <button type="button" class="btn btn-primary" id="alert_bt"> <?= lang('OK') ?> </button>
            </div>
        </div>
    </div>
</div>   
<div class="modal" data-easein="flipYIn" id="print_modal" tabindex="-1" role="dialog"
     aria-labelledby="eModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="eModalLabel">
                    <?= lang('Note') ?>
                </h4>
            </div>
            <b><div style="margin-left:30px; font-type: bold; font-size:20px">Were you sure to print?</div>
            
            <div class="modal-footer" style="margin-top:0;">
                <button type="button" class="btn btn-primary" id="print_bt">YES</button>
                <button type="button" class="btn btn-danger" id="cancel_bt"> NO</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var base_url = '<?= base_url(); ?>',
            assets = '<?= $assets ?>';
    var dateformat = '<?= $Settings->dateformat; ?>',
            timeformat = '<?= $Settings->timeformat ?>';
<?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
    var Settings = <?= json_encode($Settings); ?>;
    var sid = false,
            username = '<?= $this->session->userdata('username'); ?>',
            spositems = {};
        sid = <?= $sid; ?>;
    $(window).load(function () {
        $('#mm_<?= $m ?>').addClass('active');
        $('#<?= $m ?>_<?= $v ?>').addClass('active');
    });
    var pro_limit = <?= $Settings->pro_limit ?>,
            java_applet = 0,
            count = 1,
            total = 0,
            an = 1,
            p_page = 0,
            page = 0,
            cat_id = <?= $Settings->default_category ?>,
            tcp = <?= $tcp ?>;
    var gtotal = 0,
            order_discount = 0,
            order_tax = 0,
            protect_delete = <?= ($Admin) ? 0 : ($Settings->pin_code ? 1 : 0); ?>;
    var order_data = {},
            bill_data = {};
    var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
<?php
if ($Settings->remote_printing == 2) {
    ?>
        var ob_store_name = "<?= printText($store->name, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        order_data.store_name = ob_store_name;
        bill_data.store_name = ob_store_name;

        ob_header = "";
        ob_header +=
                "<?= printText($store->name . ' (' . $store->code . ')', (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
    <?php if ($store->address1) { ?>
            ob_header += "<?= printText($store->address1, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        <?php
    }
    if ($store->address2) {
        ?>
            ob_header += "<?= printText($store->address2, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        <?php
    }
    if ($store->city) {
        ?>
            ob_header += "<?= printText($store->city, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
    <?php }
    ?>
        ob_header +=
                "<?= printText(lang('tel') . ': ' . $store->phone, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        ob_header +=
                "<?= printText(str_replace(array("\n", "\r"), array("\\n", "\\r"), $store->receipt_header), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";

        order_data.header = ob_header +
                "<?= printText(lang('order'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        bill_data.header = ob_header +
                "<?= printText(lang('bill'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        order_data.totals = '';
        order_data.payments = '';
        bill_data.payments = '';
        order_data.footer = '';
        bill_data.footer = "<?= lang('merchant_copy'); ?> \n";
    <?php
}
?>
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['please_add_product'] = '<?= lang('please_add_product'); ?>';
    lang['paid_less_than_amount'] = '<?= lang('paid_less_than_amount'); ?>';
    lang['x_suspend'] = '<?= lang('x_suspend'); ?>';
    lang['discount_title'] = '<?= lang('discount_title'); ?>';
    lang['update'] = '<?= lang('update'); ?>';
    lang['tax_title'] = '<?= lang('tax_title'); ?>';
    lang['leave_alert'] = '<?= lang('leave_alert'); ?>';
    lang['close'] = '<?= lang('close'); ?>';
    lang['delete'] = '<?= lang('delete'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
    lang['wrong_pin'] = '<?= lang('wrong_pin'); ?>';
    lang['file_required_fields'] = '<?= lang('file_required_fields'); ?>';
    lang['enter_pin_code'] = '<?= lang('enter_pin_code'); ?>';
    lang['incorrect_gift_card'] = '<?= lang('incorrect_gift_card'); ?>';
    lang['card_no'] = '<?= lang('card_no'); ?>';
    lang['value'] = '<?= lang('value'); ?>';
    lang['balance'] = '<?= lang('balance'); ?>';
    lang['unexpected_value'] = '<?= lang('unexpected_value'); ?>';
    lang['inclusive'] = '<?= lang('inclusive'); ?>';
    lang['exclusive'] = '<?= lang('exclusive'); ?>';
    lang['total'] = '<?= lang('total'); ?>';
    lang['total_items'] = '<?= lang('total_items'); ?>';
    lang['order_tax'] = '<?= lang('order_tax'); ?>';
    lang['order_discount'] = '<?= lang('order_discount'); ?>';
    lang['total_payable'] = '<?= lang('total_payable'); ?>';
    lang['rounding'] = '<?= lang('rounding'); ?>';
    lang['grand_total'] = '<?= lang('grand_total'); ?>';
    lang['register_open_alert'] = '<?= lang('register_open_alert'); ?>';
    lang['discount'] = '<?= lang('discount'); ?>';
    lang['order'] = '<?= lang('order'); ?>';
    lang['bill'] = '<?= lang('bill'); ?>';
    lang['merchant_copy'] = '<?= lang('merchant_copy'); ?>';

    $(document).ready(function () {
<?php if ($this->session->userdata('rmspos')) { ?>
            if (get('spositems')) {
                remove('spositems');
            }
            if (get('spos_discount')) {
                remove('spos_discount');
            }
            if (get('spos_tax')) {
                remove('spos_tax');
            }
            if (get('spos_note')) {
                remove('spos_note');
            }
            if (get('spos_customer')) {
                remove('spos_customer');
            }
            if (get('amount')) {
                remove('amount');
            }
    <?php
    $this->tec->unset_data('rmspos');
}
?>

        if (get('rmspos')) {
            if (get('spositems')) {
                remove('spositems');
            }
            if (get('spos_discount')) {
                remove('spos_discount');
            }
            if (get('spos_tax')) {
                remove('spos_tax');
            }
            if (get('spos_note')) {
                remove('spos_note');
            }
            if (get('spos_customer')) {
                remove('spos_customer');
            }
            if (get('amount')) {
                remove('amount');
            }
            remove('rmspos');
        }
<?php if ($sid) { ?>

            store('spositems', JSON.stringify(<?= $items; ?>));
            store('spos_discount', '<?= $suspend_sale->order_discount_id; ?>');
            store('spos_tax', '<?= $suspend_sale->order_tax_id; ?>');
            store('spos_customer', '<?= $suspend_sale->customer_id; ?>');
            store('spos_customer_name', '<?= $suspend_sale->customer_name; ?>');
            // $('#spos_customer').select2().select2('val', '<?= $suspend_sale->customer_id; ?>');
            store('rmspos', '1');
            $('#tax_val').val('<?= $suspend_sale->order_tax_id; ?>');
            $('#discount_val').val('<?= $suspend_sale->order_discount_id; ?>'); 
<?php } elseif ($eid) { ?>
            $('#date').inputmask("y-m-d h:s:s", {
                "placeholder": "YYYY/MM/DD HH:mm:ss"
            });
            store('spositems', JSON.stringify(<?= $items; ?>));
            store('spos_discount', '<?= $sale->order_discount_id; ?>');
            store('spos_tax', '<?= $sale->order_tax_id; ?>');
            store('spos_customer', '<?= $sale->customer_id; ?>');
            store('sale_date', '<?= $sale->date; ?>');
            $('#spos_customer').select2().select2('val', '<?= $sale->customer_id; ?>');
            $('#date').val('<?= $sale->date; ?>');
            store('rmspos', '1');
            $('#tax_val').val('<?= $sale->order_tax_id; ?>');
            $('#discount_val').val('<?= $sale->order_discount_id; ?>');
<?php } else { ?>
            if (!get('spos_discount')) {
                store('spos_discount', '<?= $Settings->default_discount; ?>');
                $('#discount_val').val('<?= $Settings->default_discount; ?>');
            }
            if (!get('spos_tax')) {
                store('spos_tax', '<?= $Settings->default_tax_rate; ?>');
                $('#tax_val').val('<?= $Settings->default_tax_rate; ?>');
            }
<?php } ?>

        if (ots = get('spos_tax')) {
            $('#tax_val').val(ots);
        }
        if (ods = get('spos_discount')) {
            $('#discount_val').val(ods);
        }
        bootbox.addLocale('bl', {
            OK: '<?= lang('ok'); ?>',
            CANCEL: '<?= lang('no'); ?>',
            CONFIRM: '<?= lang('yes'); ?>'
        });
        bootbox.setDefaults({
            closeButton: false,
            locale: "bl"
        });
<?php if ($eid) { ?>
            $('#suspend').attr('disabled', true);
            $('#print_order').attr('disabled', true);
            $('#print_bill').attr('disabled', true);
<?php } ?>
    });
</script>

<script type="text/javascript">
    var socket = null;
<?php
if ($Settings->remote_printing == 2) {
    ?>
        try {
            socket = new WebSocket('ws://127.0.0.1:6441');
            socket.onopen = function () {
                console.log('Connected');
                return;
            };
            socket.onclose = function () {
                console.log('Connection closed');
                return;
            };
        } catch (e) {
            console.log(e);
        }
    <?php
}
?>

    function printBill(bill) {
        if (Settings.remote_printing == 1) {
            Popup($('#bill_tbl').html());
        } else if (Settings.remote_printing == 2) {
            if (socket.readyState == 1) {
                if (Settings.print_img == 1) {
                    $('#bill-data').show();
                    $('#preb').html(
                            '<pre style="background:#FFF;font-size:20px;margin:0;border:0;color:#000 !important;">' +
                            bill_data.info +
                            bill_data.items +
                            '\n' +
                            bill_data.totals +
                            '</pre>'
                            );
                    var element = $('#bill-data').get(0);
                    html2canvas(element, {
                        scrollY: 0,
                        scale: 1.7
                    }).then(function (canvas) {
                        var dataURL = canvas.toDataURL();
                        var socket_data = {
                            'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                            'text': dataURL,
                            'cash_drawer': 0
                        };
                        socket.send(JSON.stringify({
                            type: 'print-img',
                            data: socket_data
                        }));
                        // return Canvas2Image.saveAsPNG(canvas);
                    });
                    setTimeout(function () {
                        $('#bill-data').hide();
                    }, 500);
                } else {
                    var socket_data = {
                        'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                        'logo': '<?= !empty($store->logo) ? base_url('uploads/' . $store->logo) : ''; ?>',
                        'text': bill
                    };
                    socket.send(JSON.stringify({
                        type: 'print-receipt',
                        data: socket_data
                    }));
                }
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
    }
    var order_printers = <?= $Settings->local_printers ? "''" : json_encode($order_printers); ?>;

    function printOrder(order) {
        if (Settings.remote_printing == 1) {
            Popup($('#order_tbl').html());
        } else if (Settings.remote_printing == 2) {
            if (socket.readyState == 1) {
                if (Settings.print_img == 1) {
                    $('#order-data').show();
                    $('#preo').html(
                            '<pre style="background:#FFF;font-size:20px;margin:0;border:0;color:#000 !important;">' +
                            order_data.info + order_data.items + '</pre>'
                            );
                    var element = $('#order-data').get(0);
                    html2canvas(element, {
                        scrollY: 0,
                        scale: 1.7
                    }).then(function (canvas) {
                        var dataURL = canvas.toDataURL();
                        var socket_data = {
                            'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                            'text': dataURL,
                            'order': 1,
                            'cash_drawer': 0
                        };
                        socket.send(JSON.stringify({
                            type: 'print-img',
                            data: socket_data
                        }));
                        // return Canvas2Image.saveAsPNG(canvas);
                    });
                    setTimeout(function () {
                        $('#order-data').hide();
                    }, 500);
                } else {
                    if (order_printers == '') {
                        var socket_data = {
                            'printer': false,
                            'order': true,
                            'logo': '<?= !empty($store->logo) ? base_url('uploads/' . $store->logo) : ''; ?>',
                            'text': order
                        };
                        socket.send(JSON.stringify({
                            type: 'print-receipt',
                            data: socket_data
                        }));
                    } else {
                        $.each(order_printers, function () {
                            var socket_data = {
                                'printer': this,
                                'logo': '<?= !empty($store->logo) ? base_url('uploads/' . $store->logo) : ''; ?>',
                                'text': order
                            };
                            socket.send(JSON.stringify({
                                type: 'print-receipt',
                                data: socket_data
                            }));
                        });
                    }
                }
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
    }
</script>
<?php
// if (isset($print) && !empty($print)) {
//     /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
//     include 'remote_printing.php';
// }
?>

<script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dev/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?= $assets ?>dev/js/tag.js" type="text/javascript"></script>
<script src="<?= $assets ?>dev/js/webcam.min.js" type="text/javascript"></script>

<?php if ($Settings->remote_printing != 1 && $Settings->print_img) { ?>
    <script src="<?= $assets ?>dist/js/htmlimg.js"></script>
<?php } ?>

</body>

</html>