<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<?php
if ($modal) {
    ?>
    <div class="modal-dialog" role="document"<?= $Settings->rtl ? ' dir="rtl"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <?php
            } else {
                ?><!doctype html>
                <html<?= $Settings->rtl ? ' dir="rtl"' : ''; ?>>
                    <head>
                        <meta charset="utf-8">
                        <title><?= $page_title . " " . lang("no") . " " . $inv->id; ?></title>
                        <base href="<?= base_url() ?>"/>
                        <meta http-equiv="cache-control" content="max-age=0"/>
                        <meta http-equiv="cache-control" content="no-cache"/>
                        <meta http-equiv="expires" content="0"/>
                        <meta http-equiv="pragma" content="no-cache"/>
                        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
                        <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
                        <style type="text/css" media="all">
                            body { color: #000; }
                            #wrapper { max-width: 288px; margin: 0 auto; padding-top: 20px; }
                            .btn { margin-bottom: 5px; }
                            .table { border-radius: 3px; }
                            .table th { background: #f5f5f5; }
                            .table th, .table td { vertical-align: middle !important; }
                            h3 { margin: 5px 0; }
                            #receipt-data { background-color: #f1dbdb; background-image: url(<?php echo base_url('uploads/hangers.png'); ?>); padding: 10px; }
                            table, table>thead, table>thead>tr, table>thead>tr>th, table>tbody, table>tfoot, table>tfoot>tr, table>tfoot>tr>th { background: transparent!important; border: 0!important; }
                            p {margin-bottom: 5px!important;}
                            .text-sm { font-size: 95%; }
                            .text-lg { font-size: 110%; }
                            img.bcimg { width: 80%; }
                            @media print {
                                .no-print { display: none; }
                                #wrapper { width: 288px; margin: 0; background-color: #f1dbdb; background-image: url(<?php echo base_url('uploads/hangers.png'); ?>); }
                            }
                            <?php if ($Settings->rtl) { ?>
                                .text-right { text-align: left; }
                                .text-left { text-align: right; }
                                tfoot tr th:first-child { text-align: left; }
                            <?php } else { ?>
                                tfoot tr th:first-child { text-align: right; }
                            <?php } ?>
                        </style>
                    </head>
                    <body>
                        <?php
                    }
                    ?>
                    <div id="wrapper">
                        <div id="receiptData" style="width: 288px; margin: 0 auto;">
                            <div class="no-print">
                                <?php if ($message) { ?>
                                    <div class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button">×</button>
                                        <?= is_array($message) ? print_r($message, true) : $message; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div id="receipt-data">
                                <div>
                                    <div style="text-align:center;">
                                        <?php
                                        if ($store) {
                                            // echo '<img src="' . base_url('uploads/hangers.png) . '" alt="' . $store->name . '">';
                                            echo '<h2><b>'.$store->receipt_header.'</b></h2>';
                                            echo '<div style="text-align:center; display: flex; flex-flow: column;">';
                                            echo '<p>' . $customer->email . '</p>';
                                            echo '<p>' . $customer->address . '</p>';
                                            echo '<p>' . $customer->city . '</p>';
                                            echo '<p>' . $customer->phone . '</p>';
                                            echo '</div>';
                                            echo '<h3><strong>Dry Cleaning</strong></h3>';
                                            echo $barcode;
                                            echo '<h1><strong>' . $invoice_id . '</strong></h1>';
                                        }
                                        ?>
                                        <p class="text-sm">==== PICK UP =========== PCs ==</p>
                                        <div style="display:flex; justify-content: space-around;">
                                            <h3><?php echo date('D m/d/Y', strtotime($inv->pickup_date)); ?></h3>
                                            <h3><?php echo intval($inv->total_quantity); ?></h3>
                                        </div>
                                        <p class="text-sm">Drop Date : <?php echo $this->tec->hrld($inv->date); ?></p>
                                        <p style="color: #777;">.............................................................</p>
                                        <h3><strong><?php echo $inv->customer_name; ?></strong></h3>
                                        <?= $customer->shirt_starch || $customer->packing ? '<p style="text-align: center;">' . $customer->shirt_starch . '&nbsp;&nbsp;&nbsp;&nbsp;' . $customer->packing . '</p>':'' ?>
                                        <!--<p style="text-align: left;"><?= $inv->id; ?></p>-->
                                        <?= $inv->note ? '<p style="text-align: center;"><strong>' . $this->tec->decode_html($inv->note) . '</strong></p>' : ''; ?>
                                        <p style="color: #777;">.............................................................</p>
                                        <?php
                                            foreach ($rows as $row) {
                                                echo '<div style="display:flex; justify-content: space-between;"><p style="max-width: 180px;">' . $row->product_name . '<br><i style="font-size: 90%;">';
                                                echo $row->color_name=='null' || $row->color_name=='Skip'?'No Color,  ':$row->color_name.',  ';
                                                echo $row->upcharge_name=='null'?'No Upcharge</i></p>':$row->upcharge_name.'</i></p>';
                                                echo '<p>$ ' . $this->tec->formatMoney($row->subtotal) . '</p></div>';
                                            }
                                        ?>
                                        <p style="color: #777;">.............................................................</p>
                                        <div style="position: relative;">
                                            <img src="<?php echo base_url('uploads/'.$inv->status.'.png'); ?>" alt="" style="position: absolute; top: -100px; left: 0; width: 100%; height: auto; opacity: 0.8;" />
                                        </div>
                                        <div style="display:flex; justify-content: space-between;">
                                            <p><?= lang("total"); ?>:</p>
                                            <p class="text-right">$ <?= $this->tec->formatMoney($inv->total + $inv->product_tax); ?></p>
                                        </div>
                                            <?php
                                            if ($inv->order_tax != 0) {
                                                echo '<div style="display:flex; justify-content: space-between;"><p>' . lang("order_tax") . ':</p><p class="text-right">$ ' . $this->tec->formatMoney($inv->order_tax) . '</p></div>';
                                            }
                                            if ($inv->total_discount != 0) {
                                                echo '<div style="display:flex; justify-content: space-between;"><p>' . lang("order_discount") . ':</p><p class="text-right">$ ' . $this->tec->formatMoney($inv->total_discount) . '</p></div>';
                                            }

                                            if ($Settings->rounding) {
                                                $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                                                $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
                                                ?>
                                                <div style="display:flex; justify-content: space-between;"><p><?= lang("rounding"); ?>:</p>
                                                    <p class="text-right">$ <?= $this->tec->formatMoney($rounding); ?></p>
                                                </div>
                                                <div style="display:flex; justify-content: space-between;"><p><?= lang("grand_total"); ?>:</p>
                                                    <p class="text-right">$ <?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></p>
                                                </div>
                                                <?php
                                            } else {
                                                $round_total = $inv->grand_total;
                                                ?>
                                                <div style="display:flex; justify-content: space-between;"><p><?= lang("grand_total"); ?>:</p>
                                                    <p class="text-right">$ <?= $this->tec->formatMoney($inv->grand_total); ?></p>
                                                </div>
                                                <?php
                                            }
                                            if ($inv->paid < $round_total) {
                                                ?>
                                                <div style="display:flex; justify-content: space-between;"><p><?= lang("paid_amount"); ?>:</p>
                                                    <p class="text-right">$ <?= $this->tec->formatMoney($inv->paid); ?></p>
                                                </div>
                                                <div style="display:flex; justify-content: space-between;"><p class="text-lg"><b><?= lang("due_amount"); ?>:</b></p>
                                                    <p class="text-right text-lg"><b>$ <?= $this->tec->formatMoney($inv->grand_total - $inv->paid); ?></b></p>
                                                </div>
                                            <?php 
                                            } 
                                            ?>
                                        </div>
                                        <p style="color: #777;">.............................................................</p>
                                        <p class="text-sm"><?= $store->receipt_footer; ?></p>
                                        <h4><b>Emp. <?= $created_by->first_name . " " . $created_by->last_name; ?></b></h4>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <!--<?php
                                    if ($payments) {
                                        echo '<table class="table table-striped table-condensed" style="margin-top:0px;"><tbody>';
                                        foreach ($payments as $payment) {
                                            echo '<tr>';
                                            if ($payment->paid_by == 'cash' && $payment->pos_paid) {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>' . lang($payment->paid_by) . '</td>';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                                echo '<td class="text-right">' . lang("change") . ' :</td><td>' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                                            }
                                            if ($payment->paid_by == 'due') {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>Pay on Pickup';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                                echo '<td class="text-right">' . lang("change") . ' :</td><td>' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                                            }
                                            if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>' . lang($payment->paid_by) . '</td>';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                                echo '<td class="text-right">' . lang("no") . ' :</td><td>' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                                                echo '<td class="text-right">' . lang("name") . ' :</td><td>' . $payment->cc_holder . '</td>';
                                            }
                                            if ($payment->paid_by == 'Cheque' || $payment->paid_by == 'cheque' && $payment->cheque_no) {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>' . lang($payment->paid_by) . '</td>';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                                echo '<td class="text-right">' . lang("cheque_no") . ' :</td><td>' . $payment->cheque_no . '</td>';
                                            }
                                            if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>' . lang($payment->paid_by) . '</td>';
                                                echo '<td class="text-right">' . lang("no") . ' :</td><td>' . $payment->gc_no . '</td>';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                                echo '<td class="text-right">' . lang("balance") . ' :</td><td>' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                                            }
                                            if ($payment->paid_by == 'other' && $payment->amount) {
                                                echo '<td class="text-right">' . lang("paid_by") . ' :</td><td>' . lang($payment->paid_by) . '</td>';
                                                echo '<td class="text-right">' . lang("amount") . ' :</td><td>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                                echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ' :</td><td>' . $payment->note . '</td>' : '';
                                            }
                                            echo '</tr>';
                                        }
                                        echo '</tbody></table>';
                                    }
                                    ?>-->

                                    <?php if (!empty($store->receipt_footer)) { ?>
                                        <!--<div class="well well-sm"  style="margin-top:10px;">
                                            <div style="text-align: center;"><?= nl2br($store->receipt_footer); ?></div>
                                        </div>-->
                                    <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                            </div>

                            <!-- start -->
                            <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
                                <?php if ($modal) { ?>
                                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                        <div class="btn-group" role="group">
                                            <?php
                                            if (!$Settings->remote_printing) {
                                                echo '<a href="' . site_url('pos/print_receipt/' . $inv->id . '/0') . '" id="print" class="btn btn-block btn-primary">' . lang("print") . '</a>';
                                            } elseif ($Settings->remote_printing == 1) {
                                                echo '<button onclick="window.print();" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                            } else {
                                                echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                            }
                                            ?>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <span class="pull-right col-xs-12">
                                        <?php
                                        if (!$Settings->remote_printing) {
                                            echo '<a href="' . site_url('pos/print_receipt/' . $inv->id . '/1') . '" id="print" class="btn btn-block btn-primary">' . lang("print") . '</a>';
                                            echo '<a href="' . site_url('pos/open_drawer/') . '" class="btn btn-block btn-default">' . lang("open_cash_drawer") . '</a>';
                                        } elseif ($Settings->remote_printing == 1) {
                                            echo '<button onclick="window.print();" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                        } else {
                                            echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                            echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">' . lang("open_cash_drawer") . '</button>';
                                        }
                                        ?>
                                    </span>
                                    <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>
                                    <span class="col-xs-12">
                                        <a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                                    </span>
                                <?php } ?>
                                <div style="clear:both;"></div>
                            </div>
                            <!-- end -->
                        </div>
                    </div>
                    <!-- start -->
                    <?php
                    if (!$modal) {
                        ?>
                        <script type="text/javascript">
                            var base_url = '<?= base_url(); ?>';
                            var site_url = '<?= site_url(); ?>';
                            var dateformat = '<?= $Settings->dateformat; ?>', timeformat = '<?= $Settings->timeformat ?>';
    <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
                            var Settings = <?= json_encode($Settings); ?>;
                        </script>
                        <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
                        <script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
                        <script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
                        <?php
                    }
                    ?>
                    <script type="text/javascript">
                            $(document).ready(function () {
                                $('#print').click(function (e) {
                                    e.preventDefault();
                                    var link = $(this).attr('href');
                                    $.get(link);
                                    return false;
                                });
                                setTimeout(function(){
                                    $('.alert').slideUp();
                                }, 1000);
                                $('#email').click(function () {
                                    bootbox.prompt({
                                        title: "<?= lang("email_address"); ?>",
                                        inputType: 'email',
                                        value: "<?= $customer->email; ?>",
                                        callback: function (email) {
                                            if (email != null) {
                                                $.ajax({
                                                    type: "post",
                                                    url: "<?= site_url('pos/email_receipt') ?>",
                                                    data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
                                                    dataType: "json",
                                                    success: function (data) {
                                                        bootbox.alert({message: data.msg, size: 'small'});
                                                    },
                                                    error: function () {
                                                        bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                                        return false;
                                                    }
                                                });
                                            }
                                        }
                                    });
                                    return false;
                                });
                            });
                    </script>
                    <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
                    <?php include 'remote_printing.php'; ?>
                    <?php
                    if ($modal) {
                        ?>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <!-- end -->
    </body>
    </html>
    <?php
}
?>
