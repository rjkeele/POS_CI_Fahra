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
        <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css"/>
        <style type="text/css" media="all">
          body {
            color: #000;
          }

          #wrapper {
            max-width: 288px;
            margin: 0 auto;
            padding-top: 20px;
          }

          .btn {
            margin-bottom: 5px;
          }

          .table {
            border-radius: 3px;
          }

          .table th {
            background: #f5f5f5;
          }

          .table th, .table td {
            vertical-align: middle !important;
          }

          h3 {
            margin: 5px;
            font-weight: bold;
          }

          .receipt-title {
            font-size: 1.65em;
          }

          #receipt-data {
            background-color: #f1dbdb;
            background-image: url(<?php echo base_url('uploads/hangers.png'); ?>);
            padding: 15px;
          }

          table, table > thead, table > thead > tr, table > thead > tr > th, table > tbody, table > tfoot, table > tfoot > tr, table > tfoot > tr > th {
            background: transparent !important;
            border: 0 !important;
          }

          .name {
            margin-bottom: 0px;
            font-size: 1.8em;
            line-height: initial;
            padding-bottom: 0;
          }

          .telephone {
            font-weight: initial;
            padding-bottom: 0;
          }

          .price-detail-item {
            display: flex;
            justify-content: space-between;
            font-size: 1.0em;
          }

          .price-detail-item-name {
            width: 70%;
            text-align: right;
            font-weight: normal;
            font-size: 0.9em;
          }

          .invoice-id {
            margin-top: 0;
            font-size: 2.6em;
            font-weight: bold;
          }

          .pick-up-date {
            font-size: 1.5em;
          }

          .pick-up-drop-date {
          }

          .customer-name {
            font-size: 1.6em;
            font-weight: bold;
            text-align: left;
          }

          .dotted-bottom-border {
            border-bottom: #777 dotted 2px;
            margin-bottom: 0.5em;
            padding-bottom: 0.4em;
          }

          p {
            margin-bottom: 2px !important;
            font-weight: bold;
          }

          .text-sm {
            font-size: 80%;
            text-align: left
          }

          .text-lg {
            font-size: 110%;
          }

          img.bcimg {
            width: 80%;
          }

          .text-sm1 {
            margin-top: 10px;
          }

          @media print {
            .no-print {
              display: none;
            }

            #wrapper {
              width: 288px;
              margin: 0;
              background-color: #f1dbdb;
              background-image: url(<?php echo base_url('uploads/hangers.png'); ?>);
            }
          }

          <?php if ($Settings->rtl) { ?>
          .text-right {
            text-align: left;
          }

          .text-left {
            text-align: right;
          }

          tfoot tr th:first-child {
            text-align: left;
          }

          <?php } else { ?>
          tfoot tr th:first-child {
            text-align: right;
          }

          <?php } ?>
        </style>
      </head>
      <body>
      <?php
      }
      ?>
      <div id="wrapper">
        <div id="receiptData" style="width: 100%; margin: 0 auto;">
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
                  // echo '<h2 style=" margin:0;"><b>'.$store->receipt_header.'</b></h2>';
                  echo '<div style="text-align:center;display: flex;flex-flow: column;width: 75%;margin-left: auto;margin-right: auto;font-size: 15px;">';
                  echo '<p class="name">' . $Settings->site_name . '</p>';// print_r(json_encode($Settings));
                  echo '<p class="telephone">' . $Settings->tel . '</p>';

                  // echo '<p>' . $customer->email . '</p>'; //print_r(json_encode($customer));
                  // echo '<p>' . $customer->address . '</p>';
                  // echo '<p>' . $customer->city . '</p>';
                  // echo '<p>' . $customer->phone . '</p>';

                  echo '</div>';
                  echo '<div class="receipt-title"><strong>Customer Receipt</strong></div>';
                  echo $barcode;
                  echo '<div class="invoice-id"><strong>' . $invoice_id . '</strong></div>';
                }
                ?>
                <div id="pick-up-data" class="dotted-bottom-border">
                  <p class="text-sm1">===== PICK UP ======== PCs ==</p>
                  <div style=" justify-content: space-around;" class="pick-up-date">

                    <span style="float: left;"><?php echo date('D m/d h:m', strtotime($inv->pickup_date)); ?></span>
                    <span style="float:right"><?php echo intval($inv->total_quantity) . '&nbsp;&nbsp;&nbsp;&nbsp;'; ?></span>
                  </div>
                  <div class="text-sm pick-up-drop-date" style=" float:left; padding-bottom: 1em;"><b>Drop Date:</b>&nbsp;&nbsp; <?php echo $this->tec->hrld($inv->date); ?></div>
                  <!--                  <p style="color: transparent;">...............................................</p>-->

                  <div class="customer-name"><strong style="margin-top: 1em;"><?php echo $inv->customer_name; ?></strong></div>


                  <?= $customer->shirt_starch || $customer->packing ? '<div style="text-align: left; font-weight: bold; ">' . $customer->phone . '</div><div style="text-align: left; font-weight: bold;">' . $customer->shirt_starch . '&nbsp;&nbsp;&nbsp;&nbsp;' . $customer->packing . '</div>' : '<div style="text-align: left; text-decoration:underline;  font-weight: initial; ">' . $customer->phone . '</div>' ?>


                  <!--<p style="text-align: left;"><?= $inv->id; ?></p>-->
                  <?= $inv->note ? '<p style="text-align: right;"><strong>' . $this->tec->decode_html($inv->note) . '</strong></p>' : ''; ?>
                </div>
                <div id="price-overview" class="dotted-bottom-border">
                  <?php
                  foreach ($rows as $row) {
                    echo '<div style="display:flex; justify-content: space-between;"><p style="max-width: 180px;  text-align:left;">' . $row->product_name . '<br><i style="font-size: 90%;">';
                    echo $row->color_name == 'null' || $row->color_name == 'Skip' ? 'No Color,  ' : $row->color_name . ',  ';
                    echo $row->upcharge_name == 'null' ? 'No Upcharge</i></p>' : $row->upcharge_name . '</i></p>';
                    echo '<p>$ ' . $this->tec->formatMoney($row->subtotal) . '</p></div>';
                  }
                  ?>
                </div>
                <!--                <p style="color: #777;">...............................................</p>-->
                <div style="position: relative;">
                  <img src="<?php echo base_url('uploads/' . $inv->status . '.png'); ?>" alt="" style="position: absolute; top: -100px; left: 0; width: 100%; height: auto; opacity: 0.8;"/>
                </div>
                <div id="price-detail" class="dotted-bottom-border">
                  <div class="price-detail-item">
                    <p class="price-detail-item-name"><?= lang("total"); ?>:</p>
                    <p class="text-right">$ <?= $this->tec->formatMoney($inv->total + $inv->product_tax); ?></p>
                  </div>
                  <?php
                  if ($inv->order_tax != 0) {
                    echo '<div class="price-detail-item"><p class="price-detail-item-name">' . lang("order_tax") . ':</p><p class="text-right">$ ' . $this->tec->formatMoney($inv->order_tax) . '</p></div>';
                  }
                  if ($inv->total_discount != 0) {
                    echo '<div class="price-detail-item"><p class="price-detail-item-name">' . lang("order_discount") . ':</p><p class="text-right">$ ' . $this->tec->formatMoney($inv->total_discount) . '</p></div>';
                  }

                  if ($Settings->rounding) {
                    $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                    $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
                    ?>
                    <div class="price-detail-item"><p class="price-detail-item-name"><?= lang("rounding"); ?>:</p>
                      <p class="text-right">$ <?= $this->tec->formatMoney($rounding); ?></p>
                    </div>
                    <div class="price-detail-item"><p class="price-detail-item-name"><?= lang("grand_total"); ?>:</p>
                      <p class="text-right">$ <?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></p>
                    </div>
                    <?php
                  } else {
                    $round_total = $inv->grand_total;
                    ?>
                    <div class="price-detail-item"><p class="price-detail-item-name"><?= lang("grand_total"); ?>:</p>
                      <p class="text-right">$ <?= $this->tec->formatMoney($inv->grand_total); ?></p>
                    </div>
                    <?php
                  }
                  if ($inv->paid < $round_total) {
                    ?>
                    <div class="price-detail-item"><p class="price-detail-item-name"><?= lang("paid_amount"); ?>:</p>
                      <p class="text-right">$ <?= $this->tec->formatMoney($inv->paid); ?></p>
                    </div>
                    <div class="price-detail-item"><p class="text-lg price-detail-item-name"><?= lang("due_amount"); ?>:</p>
                      <p class="text-right text-lg"><b>$ <?= $this->tec->formatMoney($inv->grand_total - $inv->paid); ?></b></p>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>
              <!--              <p style="color: #777;text-align: center;">...............................................</p>-->
              <h4>Emp. <?= $created_by->first_name . " " . $created_by->last_name; ?></h4>
              <p class="text-sm1" style="text-align:justify; font-weight: initial; line-height: initial; font-size: 0.7em; text-transform: uppercase;"><?= $store->receipt_footer; ?></p>
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

            <span class="w-100">
                                        <?php
                                        if (!$Settings->remote_printing) {
                                          echo '<a href="' . site_url('pos/print_receipt/' . $inv->id . '/1') . '" id="print" class="btn btn-block btn-primary">' . lang("print") . '</a>';
                                          echo '<a href="' . site_url('pos/open_drawer/') . '" class="btn btn-block btn-default">' . lang("open_cash_drawer") . '</a>';
                                        } elseif ($Settings->remote_printing == 1) {
                                          echo '<button id="print" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                        } else {
                                          echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">' . lang("print") . '</button>';
                                          echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">' . lang("open_cash_drawer") . '</button>';
                                        }
                                        ?>
                                    </span>

            <span class="w-100"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>
            <span class="w-100">
                                        <a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                                    </span>
          <?php } ?>
          <div style="clear:both;"></div>
        </div>
        <!-- end -->
      </div>
    </div>
    <!-- start -->

    <div class="modal" data-easein="flipYIn" id="print_ticket_modal" tabindex="-1" role="dialog"
         aria-labelledby="eModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header modal-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title" id="eModalLabel">
              <?= lang('Note') ?>
            </h4>
          </div>
          <b>
            <div style="margin-left:30px; font-type: bold; font-size:20px">Were you sure to print?</div>

            <div class="modal-footer" style="margin-top:0;">
              <button type="button" class="btn btn-primary" id="print_bt" style="margin-bottom: 0px">YES</button>
              <button type="button" class="btn btn-danger" id="cancel_bt"> NO</button>
            </div>
        </div>
      </div>
    </div>
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

    <!-- Direct Print Start -->
    <!--<script src="<?/*= $assets */?>plugins/JSPrintManager/zip.js"></script>
    <script src="<?/*= $assets */?>plugins/JSPrintManager/zip-ext.js"></script>
    <script src="<?/*= $assets */?>plugins/JSPrintManager/deflate.js"></script>
    <script src="<?/*= $assets */?>plugins/JSPrintManager/JSPrintManager.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

    <script src="https://github.com/niklasvh/html2canvas/releases/download/v1.0.0-rc.5/html2canvas.min.js"></script>

    <script>
        //WebSocket settings
        JSPM.JSPrintManager.auto_reconnect = true;
        JSPM.JSPrintManager.start();
        JSPM.JSPrintManager.WS.onStatusChanged = function () {
            if (jspmWSStatus()) {
                //get client installed printers
                JSPM.JSPrintManager.getPrinters().then(function (myPrinters) {
                    var options = '';
                    for (var i = 0; i < myPrinters.length; i++) {
                        options += '<option>' + myPrinters[i] + '</option>';
                    }
                    $('#installedPrinterName').html(options);
                });
            }
        };

        //Check JSPM WebSocket status
        function jspmWSStatus() {
            if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
                return true;
            else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
                alert('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
                return false;
            }
            else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.BlackListed) {
                alert('JSPM has blacklisted this website!');
                return false;
            }
        }

        //Do printing...
        function print(o) {
            if (jspmWSStatus()) {
                //generate an image of HTML content through html2canvas utility
                html2canvas(document.getElementById('card'), { scale: 5 }).then(function (canvas) {

                    //Create a ClientPrintJob
                    var cpj = new JSPM.ClientPrintJob();
                    //Set Printer type (Refer to the help, there many of them!)
                    if ($('#useDefaultPrinter').prop('checked')) {
                        cpj.clientPrinter = new JSPM.DefaultPrinter();
                    } else {
                        cpj.clientPrinter = new JSPM.InstalledPrinter($('#installedPrinterName').val());
                    }
                    //Set content to print...
                    var b64Prefix = "data:image/png;base64,";
                    var imgBase64DataUri = canvas.toDataURL("image/png");
                    var imgBase64Content = imgBase64DataUri.substring(b64Prefix.length, imgBase64DataUri.length);

                    var myImageFile = new JSPM.PrintFile(imgBase64Content, JSPM.FileSourceType.Base64, 'myFileToPrint.png', 1);
                    //add file to print job
                    cpj.files.push(myImageFile);

                    //Send print job to printer!
                    cpj.sendToClient();


                });
            }
        }

    </script>-->
    <!-- Direct Print End -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#print').click(function (e) {
                e.preventDefault();
                var link = $(this).attr('href');
                $.get(link);
                var receipt_id = '<?php echo $inv->id; ?>';
                var tdate = new Date();
                var dd = tdate.getDate(); //yields day
                var MM = tdate.getMonth(); //yields month
                var yy = tdate.getFullYear().toString().substr(-2);
                var hh = tdate.getHours().toString().length > 1 ? tdate.getHours() : '0' + tdate.getHours();
                var mm = tdate.getMinutes().toString().length > 1 ? tdate.getMinutes() : '0' + tdate.getMinutes();
                var time = hh + ":" + mm + ":" + tdate.getSeconds();

                var currentDate_time = yy + "-" + (MM + 1) + "-" + dd + ' ' + time;

                $('#print_bt').on('click', function () {
                    $.get(base_url + 'pos/printDate', {print_date: currentDate_time, print_id: receipt_id}, function (e) {
                        // console.log(e);
                        $('#print_ticket_modal').hide();
                    });
                    // $('#print_ticket_modal').hide();
                })

                $('#cancel_bt').on('click', function () {
                    $('#print_ticket_modal').hide();
                })
                $('.close').on('click', function () {
                    $('#print_ticket_modal').hide();
                })

                 window.onafterprint = function (e) {
                     $('#print_ticket_modal').show()
                 }

                window.print();

                return false;
            });
            setTimeout(function () {
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
                                data: {<?= $this->security->get_csrf_token_name(); ?>:
                            "<?= $this->security->get_csrf_hash(); ?>", email
                        :
                            email, id
                        : <?= $inv->id; ?>},
                            dataType: "json",
                                success
                        :

                            function (data) {
                                bootbox.alert({message: data.msg, size: 'small'});
                            }

                        ,
                            error: function () {
                                bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                return false;
                            }
                        })
                            ;
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
