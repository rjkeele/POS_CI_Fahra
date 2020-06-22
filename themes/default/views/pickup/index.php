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
    </head>

    <body class="skin-<?= $Settings->theme_style; ?> sidebar-collapse sidebar-mini ">
        <div class="wrapper rtl rtl-inv">
            <header class="main-header">
                <a href="<?= site_url(); ?>/pos" class="logo">
                    <?php if ($store) { ?>
                        <span class="logo-mini"><?= $store->code; ?></span>
                        <span class="logo-lg hide"><?= $store->name == 'SimplePOS' ? 'Simple<b>POS</b>' : $store->name; ?></span>
                    <?php } else { ?>
                        <span class="logo-mini">POS</span>
                        <span
                            class="logo-lg hide"><?= $Settings->site_name == 'SimplePOS' ? 'Simple<b>POS</b>' : $Settings->site_name; ?></span>
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
                            <li><a href="/pickup" class="btn btn-primary">PICK UP</a></li>
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

            <aside class="main-sidebar">
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

            <div class="content-wrapper pos-panel">

                <div class="col-lg-12 alerts">
                    <?php if ($error) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                            <?= $error; ?>
                        </div>
                    <?php }if ($message) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                            <?= $message; ?>
                        </div>
                    <?php } ?>
                </div>
                <section class="content">
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <div class="form-group">
                                        <label for="customer_sel_sec">Customer Name</label>
                                        <div class="customer_sel_sec">
                                            <?= form_dropdown('customer_id', [], set_value(null), 'id="spos_customer" data-placeholder="Search by Customer Name" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
                                        </div>
                                        <div class="mb-4" style="clear:both;"></div>
                                        <div class="row pickup-cus-detail">
                                            <div class="col-sm-12 mb-4 text-lg">
                                                <div class="col-sm-12"><label class="mr10" for="">Customer: </label> <label for="" id="cus_name"></label></div>  <!-- ( <label for="" id="cus_phone"></label> )-->
                                            </div>
                                            <div class="col-sm-4 mb-4 text-lg">
                                                <div class="col-sm-12"><label class="mr10" for="">Rack: </label><label for="" id="rack_num"></label></div>
                                            </div>
                                            <div class="col-sm-4 mb-4 text-lg">
                                                <div class="col-sm-12"><label class="mr10" for="">Total spent: </label><label for="" id="total_spent"></label></div>
                                            </div>
                                            <div class="col-sm-4 mb-4 text-lg">
                                                <div class="col-sm-12"><label class="mr10" for="">Visit: </label><label for="" id="visit_num"></label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table id="pickup_table" class="table table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr class="active">
                                                    <th class="hide"><?= lang("id"); ?></th>
                                                    <th class="col-xs-1">Ticket No</th>
                                                    <th>Rack</th>
                                                    <th class="col-xs-1">Price</th>
                                                    <th class="col-xs-1">Qty</th>
                                                    <th class="col-xs-1">Due Date</th>
                                                    <th class="col-xs-1"><?= lang("status"); ?></th>
                                                    <th class="col-xs-1">View Invoice</th>
                                                    <th class="col-xs-1">Pick</th>
                                                    <th class="col-xs-1">Pay</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="8" class="dataTables_empty text-center">Please Select Customer</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="active">
                                                    <th class="hide"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                                    <th class="col-sm-1">Ticket No</th>
                                                    <th class="col-sm-1">Rack</th>
                                                    <th class="col-sm-1">Price</th>
                                                    <th class="col-sm-1">Qty</th>
                                                    <th class="col-sm-1"><span class="datepickercon"><input type="text" class="text_filter datepicker" placeholder="Due Date"></span></th>
                                                    <th class="col-sm-1">
                                                        <select class="select2 select_filter"><option value=""><?= lang("all"); ?></option><option value="paid"><?= lang("paid"); ?></option><option value="partial"><?= lang("partial"); ?></option><option value="due"><?= lang("due"); ?></option></select>
                                                    </th>
                                                    <th class="col-sm-1">View Invoice</th>
                                                    <th class="col-xs-1">Pick</th>
                                                    <th class="col-xs-1">Pay</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 scaning">
                            <div class="box box-primary">
                                <div class="box-header text-center">
                                    <label for="">SCAN TICKET FOR PICKUP</label>
                                    <video id="video" width="640" height="480" autoplay></video>
                                    <button type="button" class="btn btn-primary" id="snap">Scan Ticket</button>
                                    <canvas id="canvas" width="640" height="480"></canvas>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table style="font-size:13px">
                                            <tr>
                                                <td><span>Total Number of PCS</span></td>
                                                <td><span id="total_number" style="text-align:right;" class="col-xs-3 scaning"><span></td>
                                            </tr>
                                            <tr>
                                                <td><span style="color: blue;">Amount due</span></td>
                                                <td><span id="amount_due"><span></td>
                                            </tr>
                                        </table> 
                                    </div>
                                <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="control-sidebar-bg sb"></div>
    </div>
</div>
<?php if ($Admin) { ?>
<div class="modal fade" id="stModal" tabindex="-1" role="dialog" aria-labelledby="stModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title" id="stModalLabel"><?= lang('update_status'); ?> <span id="status-id"></span></h4>
            </div>
            <?= form_open('sales/status'); ?>
            <div class="modal-body">
                <input type="hidden" value="" id="sale_id" name="sale_id" />
                <div class="form-group">
                    <?= lang('status', 'status'); ?>
                    <?php $opts = array('paid' => lang('paid'), 'partial' => lang('partial'), 'due' => lang('due'))  ?>
                    <?= form_dropdown('status', $opts, set_value('status'), 'class="form-control select2 tip" id="status" required="required" style="width:100%;"'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<script type="text/javascript">
    var Settings = <?= json_encode($Settings); ?>;
    var dateformat = '<?= $Settings->dateformat; ?>', timeformat = '<?= $Settings->timeformat ?>';
    var base_url = '<?= base_url(); ?>', assets = '<?= $assets ?>';
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
</script>
<?php } ?>
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

<script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dev/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">        
    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }
        var $container = $(
            "<div style='display:flex; justify-content:space-between;'><span>"+repo.text + " (" + repo.phone+ ")" +"</span></div>"
        );
        return $container;
    }

    function formatState (repo) {
        if (!repo.id) {
        return repo.text;
        }
        var $state = $(
        "<div style='display:flex; justify-content:space-between;'><span style='margin-right: 10px;'>"+repo.text + " (" + repo.phone+ ")" +"</span></div>"
        );
    
        return $state;
    }
    $(document).ready(function() {
        $('.pickup-cus-detail').hide();
        $('.box-body').hide();
        $('#spos_customer').focus();
        $('#spos_customer').select2({
            ajax: {
            url: function (params) {
                if(params.term==undefined) return base_url + 'pickup/searchPickupCustomer?search=';
                else return base_url + 'pickup/searchPickupCustomer?search=' + params.term;
            },
            dataType: 'json',
            delay: 0,
            data: function (params) {
                return {
                q: params.term, // search term
                };
            }
            },
            templateResult: formatRepo,
            templateSelection: formatState
        });
        $('.select_filter').change(function() {
            if($(this).val()) {
                $('.inv-list').hide();
                $('.' + $(this).val()).show();
            } else $('.inv-list').show();
        });
        $('#spos_customer').change(function() {
            $('.pickup-cus-detail').show();
            $('.box-body').show();
            $('#pickup_table tbody').html('<tr><td colspan="8" class="dataTables_empty text-center">Loading Data...</td></tr>')
            var cus_id = $(this).val();
            // $('#cus_name').text(t.data[0].customer_name);
            // $('#rack_num').text(t.data[0].customer_name);
            // $('#total_spent').text(t.data[0].customer_name);
            // $('#visit_num').text(t.data[0].customer_name);

            $.ajax({
                type: "get",
                data: {},
                url: base_url + 'pickup/get_sales?id='+cus_id,
                dataType: "json",
                success: function (t) {
                    // console.log("Sale========>", t);
                    let ranks = ''
                    let totalSpent = 0
                    let visits = 0
                    let pcs_num=0
                    let amonut_due = 0
                    let amount = 0
                    let paid_on = 0
                    let quantity = 0
                    if (t.recordsTotal > 0) {
                        $('#cus_name').text(t.data[0].customer_name);
                        var tableHtml = '';
                        var statusHtml = '';
                        visits = t.recordsTotal
                        $.each(t.data, function(i, row) {
                            tableHtml += '<tr class="inv-list ' + row.status + '">';
                            tableHtml += '<td class="hide">' + (i + 1) + '</td>';
                            tableHtml += '<td>' + '#' + row.id + '</td>';
                            tableHtml += '<td>' + '%' + row.label_id + '</td>';
                            tableHtml += '<td>' + '$' +parseFloat(row.grand_total).toFixed(2) + '</td>';
                            tableHtml += '<td>' + parseInt(row.total_quantity) + '</td>';
                            tableHtml += '<td>' + row.pickup_date + '</td>';
                            if (row.status == 'due') statusHtml = "<div class='text-center'><div class='btn-group'><button title='Ballence Due' class='tip btn btn-danger btn-xs'>Due</button></div></div>";
                            else if (row.status == 'paid') statusHtml = "<div class='text-center'><div class='btn-group'><button title='Full Paid' class='tip btn btn-info btn-xs'>Full Paid</button></div></div>";
                            else if (row.status == 'partial') statusHtml = "<div class='text-center'><div class='btn-group'><button title='Partial' class='tip btn btn-warning btn-xs'>Partial</button></div></div>";
                            tableHtml += '<td>' + statusHtml + '</td>';
                            tableHtml += '<td>' + row.Actions + '</td>';
                            if(row.pick == 1){ 
                                // console.log(row.pick)
                            tableHtml += '<td class="text-center">' + '<input type= "checkbox" value = "checkBox" name="pick" checked class="pick_checkbox" id=" pick_on_'
                            + row.id +
                            ' " data="'+ row.id +'">' + '</td>';
                            }else{                            
                            tableHtml += '<td class="text-center">' + '<input type="checkbox" name="pick" class="pick_checkbox" id=" pick_on_'
                            + row.id +
                            ' " data="'+ row.id +' " >' + '</td>';   
                            }                                                                                                    
                            tableHtml += '<td class="text-center">' + '<input type= "checkbox" class = "pay_on" data1="' + row.grand_total + '"  data2="' + row.paid + '" data3="' + row.total_quantity + '">' + '</td>'; 
                            tableHtml += '</tr>';

                            if(row.status !== 'paid') {
                                ranks = ranks + row.label_id + ', '
                            }

                            totalSpent = totalSpent + parseFloat(row.paid);
                            totalSpent = formatDecimal(totalSpent, 4);
                            pcs_num = pcs_num + parseInt(row.total_quantity);     
                       
                            amonut_due = amonut_due + parseFloat(row.grand_total-row.paid);             
                            amonut_due = formatDecimal(amonut_due, 2);      
                        });
                        ranks = ranks.substr(0, ranks.length-2)
                        $('#pickup_table tbody').html(tableHtml);
                        $('#visit_num').html(visits)
                        $('#total_spent').html(totalSpent + "$")
                        $('#rack_num').html(ranks)
                        $('.pick_checkbox').click(function(){
                            // if($(this).prop("checked") == true){pick=1}
                            // else if($(this).prop("checked") == false){pick=0}
                            let checked = $(this).prop("checked")
                            let _id = $(this).attr("data")
                            $.ajax({
                                type: "get",
                                data:{ isChecked: checked ? 1 : 0 ,  id: _id  }, // $('.pick_checkbox').prop("checked")                        
                                url: base_url + 'pickup/setPick',
                                // dataType: "json",
                                success: function () {   
                                    
                                }
                            });
                        });
                            
                        $('.pay_on').click(function(){
                            if($(this).prop("checked") == true){                                           
                                
                                amount +=  parseFloat($(this).attr("data1"));
                                paid_on = paid_on + parseFloat($(this).attr("data2"));
                                quantity = quantity + parseFloat($(this).attr("data3"));
                                $('#amount_due').html(formatDecimal(amount-paid_on));
                                $('#total_number').html(quantity)
                                return true;

                            }
                            else if($(this).prop("checked") == false){
                                
                                amount = amount - parseFloat($(this).attr("data1"));
                                paid_on = paid_on - parseFloat($(this).attr("data2"));
                                quantity = quantity - parseFloat($(this).attr("data3"));
                                $('#amount_due').html(formatDecimal(amount-paid_on));
                                $('#total_number').html(quantity)
                                return true;
                            }
                        }); 
                    } else {
                        bootbox.alert(lang.no_match_found);
                    }

                },
                error: function () {
                    bootbox.alert(lang.no_match_found);
                    return false;
            }
        });
    });

        function status(x) {
            var paid = '<?= lang('paid'); ?>';
            var partial = '<?= lang('partial'); ?>';
            var due = '<?= lang('due'); ?>';
            if (x == 'paid') {
                return '<div class="text-center"><span class="sale_status label label-success">'+paid+'</span></div>';
            } else if (x == 'partial') {
                return '<div class="text-center"><span class="sale_status label label-primary">'+partial+'</span></div>';
            } else if (x == 'due') {
                return '<div class="text-center"><span class="sale_status label label-danger">'+due+'</span></div>';
            } else {
                return '<div class="text-center"><span class="sale_status label label-default">'+x+'</span></div>';
            }
        }


        $(document).on('click', '.sale_status', function() {
            var sale_id = $(this).closest('tr').attr('id');
            var curr_status = $(this).text();
            var status = curr_status.toLowerCase();
            $('#status-id').text('( <?= lang('sale_id'); ?> '+sale_id+' )');
            $('#sale_id').val(sale_id);
            $('#status').val(status);
            $('#status').select2('val', status);
            $('#stModal').modal()
        });
        $('#posModal').on('hidden.bs.modal',function(){
            $('#spos_customer').change();
        });
    });
</script>
<script src="<?= $assets ?>dev/js/pickup.js" type="text/javascript"></script>
<?php if ($Settings->remote_printing != 1 && $Settings->print_img) { ?>
    <script src="<?= $assets ?>dist/js/htmlimg.js"></script>
<?php } ?>

</body>

</html>