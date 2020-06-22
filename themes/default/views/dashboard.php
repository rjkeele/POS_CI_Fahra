<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script src="<?= $assets ?>plugins/highchart/highcharts.js"></script>

<?php
if ($chartData ) {
    foreach ($chartData as $month_sale) {
        $months[] = date('d-M-Y', strtotime($month_sale->month));
        $sales[] = $month_sale->total;
        $tax[] = $month_sale->tax;
        $discount[] = $month_sale->discount;
    } 
} else {
    $months[] = '';
    $sales[] = '';
    $tax[] = '';
    $discount[] = '';
}
?>
<?php
if ($chartDataTag ) {
    foreach ($chartDataTag as $day_count) {
        $days[] = date('d-M-Y', strtotime($day_count->DAY));
        $tagged[] = $day_count->tagged ? $day_count->tagged : 0  ;
        $not_tagged[] = $day_count->not_tagged ? $day_count->not_tagged : 0;
    }
} else {
    $days[] = '';
    $tagged[] = '';
    $not_tagged[] = '';
}
?>
<script type="text/javascript">

    $(document).ready(function () {
        
        var action = ''
        var action1 = ''
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
            return {
                radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
            };
        });
        <?php if ($chartData) { ?>
        $('#chart').highcharts({
            chart: { },
            credits: { enabled: false },
            exporting: { enabled: false },
            title: { text: '' },
            xAxis: { categories: [<?php foreach($months as $month) { echo "'".$month."', "; } ?>] },
            yAxis: { min: 0, title: "" },
            tooltip: {
                shared: true,
                followPointer: true,
                headerFormat: '<div class="well well-sm" style="margin-bottom:0;"><span style="font-size:12px">{point.key}</span><table class="table table-striped" style="margin-bottom:0;">',
                pointFormat: '<tr><td style="color:{series.color};padding:4px">{series.name}: </td>' +
                '<td style="color:{series.color};padding:4px;text-align:right;"> <b>{point.y}</b></td></tr>',
                footerFormat: '</table></div>',
                useHTML: true, borderWidth: 0, shadow: false,
                style: {fontSize: '14px', padding: '0', color: '#000000'}
            },
            plotOptions: {
                series: { stacking: 'normal' }
            },
            series: [
                {
                    type: 'column',
                    name: '<?= $this->lang->line("tax"); ?>',
                    data: [<?= implode(', ', $tax); ?>]
                },
                {
                    type: 'column',
                    name: '<?= $this->lang->line("discount"); ?>',
                    data: [<?= implode(', ', $discount); ?>]
                },
                {
                    type: 'column',
                    name: '<?= $this->lang->line("sales"); ?>',
                    data: [<?= implode(', ', $sales); ?>]
                }
            ]
        });
        <?php } ?>
        
        <?php if ($chartDataTag) { ?>

        var no_tagged = [<?= implode(', ', $not_tagged); ?>];
        var tagged = [<?= implode(', ', $tagged); ?>];
        var date_array = [<?php foreach($days as $day) { echo "'".$day."', "; } ?>];
            
        var opacity = true
        var opacity1 = true
        $('#chart3').highcharts({
            
            chart: { },
            credits: { enabled: false },
            exporting: { enabled: false },
            title: { text: '' },
            xAxis: { categories: [<?php foreach($days as $day) { echo "'".$day."', "; } ?>] },
            yAxis: { min: 0, title: "" },
            tooltip: {
                shared: true,
                followPointer: true,
                headerFormat: '<div class="well well-sm" style="margin-bottom:0;"><span style="font-size:12px">{point.key}</span><table class="table table-striped" style="margin-bottom:0;">',
                pointFormat: '<tr>'+
                '<td style="color:{series.color};padding:4px">{series.name}: </td>' +
                '<td style="color:{series.color};padding:4px;text-align:right;"> <b>{point.y}</b></td></tr>',
                footerFormat: '</table></div>',
                useHTML: true, borderWidth: 0, shadow: false,
                style: {fontSize: '14px', padding: '0', color: '#000000'}
            },
            plotOptions: {
                series: { stacking: 'normal'},
            },
            series: [
                {
                    type: 'column',
                    name: '<?= $this->lang->line("NOT TAGGED"); ?>',
                    data: [<?= implode(', ', $not_tagged); ?>],
                    color: 'red',
                    events: {
                        legendItemClick: function(){
                            if(opacity1){
                                opacity1 = false;
                                if(opacity){
                                    table_show(opacity, opacity1)
                                } else {
                                    table_show(opacity, opacity1)
                                }
                            } else {
                                opacity1 = true
                                if(opacity){
                                    table_show(opacity, opacity1)
                                } else { 
                                    table_show(opacity, opacity1)
                                }
                            }
                        }
                    }
                },
                {
                    type: 'column',
                    name: '<?= $this->lang->line("TAGGED"); ?>',
                    data: [<?= implode(', ', $tagged); ?>],
                    color: 'green',
                    events: {
                        legendItemClick: function(){
                            if(opacity){
                                opacity = false;
                                if(opacity1){
                                    table_show(opacity, opacity1)
                                } else{
                                        table_show(opacity, opacity1)
                                }
                            } else{
                                opacity = true
                                if(opacity1){
                                    table_show(opacity, opacity1)
                                } else { 
                                    table_show(opacity, opacity1)
                                }
                            }
                        }
                    }
                }
            ]
        });

        <?php }?>

        <?php if ($topProducts) { ?>
        $('#chart2').highcharts({
            chart: { },
            title: { text: '' },
            credits: { enabled: false },
            exporting: { enabled: false },
            tooltip: {
                shared: true, 
                followPointer: true,
                headerFormat: '<div class="well well-sm" style="margin-bottom:0;"><span style="font-size:12px">{point.key}</span><table class="table table-striped" style="margin-bottom:0;">',
                pointFormat: '<tr><td style="color:{series.color};padding:4px">{series.name}: </td>' +
                '<td style="color:{series.color};padding:4px;text-align:right;"> <b>{point.y}</b></td></tr>',
                footerFormat: '</table></div>',
                useHTML: true, borderWidth: 0, shadow: false,
                style: {fontSize: '14px', padding: '0', color: '#000000'}
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: false
                }
            },

            series: [{
                type: 'pie',
                name: '<?=$this->lang->line('total_sold')?>',
                data: [
                <?php
                foreach($topProducts as $tp) {
                    echo "['".$tp->product_name." (".$tp->product_code.")', ".$tp->quantity."],";

                } ?>
                ]
            }]
        });
        <?php } ?>
    // TAGGED LIST
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

        var table = $('#SLRData').DataTable({
            
        'ajax' : { url:'<?=site_url('reports/get_tag_count/'. $v);?>' , type: 'POST', "data":  function ( d ) { 
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                },
            },
            "buttons": [
                { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },//, 8, 9, 10, 11
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'colvis', text: 'Columns'},
            ],
            "columns": [
                { "data": "id", "visible": false },
                { "data": "date", "render": hrld },
                { "data": "customer_name" },
                { "data": "total_quantity", "render": status },
                { "data": "total_items", "render": status },
                { "data": "status", "render": status },
                { "data": "tick_print_date", "render": hrld },
                { "data": "tag_print_date", "render": hrld },
            ]
        });
        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        table.columns().every(function () {
            var self = this;
            $( 'input.datepicker', this.footer() ).on('dp.change', function (e) {
                self.search( this.value ).draw();
            });
            $( 'input:not(.datepicker)', this.footer() ).on('keyup change', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (((code == 13 && self.search() !== this.value) || (self.search() !== '' && this.value === ''))) {
                    self.ssearch( this.value ).draw();
                }
            });
            $( 'select', this.footer() ).on( 'change', function (e) {
                self.search( this.value ).draw();
            });
        });
    // end 

    function table_show(opacity, opacity1){
        var table = $('#SLRData').DataTable({
        'destroy': true,
        'ajax' : {
            url: opacity && opacity1? '<?=site_url('reports/get_tag_count/'. $v);?>'
            : !opacity && opacity1?'<?=site_url('reports/get_notTagged/'. $v);?>'
            : opacity && !opacity1?'<?=site_url('reports/get_tagged/'. $v);?>'
            :'<?=site_url('reports/get_noTag/'. $v); ?>',
            type : 'POST',
            "data" :  function ( d ) { 
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                }
            },
            "buttons": [
                { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },//, 8, 9, 10, 11
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
                { extend: 'colvis', text: 'Columns'},
            ],
            "columns": [
                { "data": "id", "visible": false },
                { "data": "date", "render": hrld },
                { "data": "customer_name" },
                { "data": "total_quantity", "render": status },
                { "data": "total_items", "render": status },
                { "data": "status", "render": status },
                { "data": "tick_print_date", "render": hrld },
                { "data": "tag_print_date", "render": hrld },
            ]
        });
    }
});

</script>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('quick_links'); ?></h3>
                </div>
                <div class="box-body">
                    <?php if ($this->session->userdata('store_id')) { ?>
                    <a class="btn btn-app" href="<?= site_url('pos'); ?>">
                        <i class="fa fa-th"></i> <?= lang('pos'); ?>
                    </a>
                    <?php } ?>
                    <a class="btn btn-app" href="<?= site_url('tag'); ?>">
                        <i class="fa fa-tags"></i> Tagging
                    </a>
                    <a class="btn btn-app" href="<?= site_url('pickup'); ?>">
                        <i class="fa fa-check"></i> Pickup
                    </a>
                    <a class="btn btn-app" href="<?= site_url('rack'); ?>">
                        <i class="fa fa-comment"></i> Racking
                    </a>
                    
                    <a class="btn btn-app" href="<?= site_url('products'); ?>">
                        <i class="fa fa-barcode"></i> <?= lang('products'); ?>
                    </a>
                    <?php if ($this->session->userdata('store_id')) { ?>
                    <a class="btn btn-app" href="<?= site_url('sales'); ?>">
                        <i class="fa fa-shopping-cart"></i> <?= lang('sales'); ?>
                    </a>
                    <a class="btn btn-app" href="<?= site_url('sales/opened'); ?>">
                        <!-- <span class="badge bg-yellow"><?=sizeof($suspended_sales);?></span> -->
                        <i class="fa fa-bell-o"></i> <?= lang('opened_bills'); ?>
                    </a>
                    <?php } ?>
                    <a class="btn btn-app" href="<?= site_url('categories'); ?>">
                        <i class="fa fa-folder-open"></i> <?= lang('categories'); ?>
                    </a>
                    <a class="btn btn-app" href="<?= site_url('gift_cards'); ?>">
                        <i class="fa fa-credit-card"></i> <?= lang('gift_cards'); ?>
                    </a>
                    <a class="btn btn-app" href="<?= site_url('customers'); ?>">
                        <i class="fa fa-users"></i> <?= lang('customers'); ?>
                    </a>
                    <?php if ($Admin) { ?>
                    <a class="btn btn-app" href="<?= site_url('cloths/colors'); ?>">
                        <i class="fa fa-cog"></i> Cloth Settings
                    </a>
                    <a class="btn btn-app" href="<?= site_url('settings'); ?>">
                        <i class="fa fa-cogs"></i> <?= lang('settings'); ?>
                    </a>
                    <a class="btn btn-app" href="<?= site_url('reports'); ?>">
                        <i class="fa fa-bar-chart-o"></i> <?= lang('reports'); ?>
                    </a>
                    <a class="btn btn-app" href="<?= site_url('users'); ?>">
                        <i class="fa fa-users"></i> <?= lang('users'); ?>
                    </a>
                    <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                    <a class="btn btn-app" href="<?= site_url('settings/backups'); ?>">
                        <i class="fa fa-database"></i> <?= lang('backups'); ?>
                    </a>
                    <?php } ?>
                    <!-- <a class="btn btn-app" href="<?= site_url('settings/updates'); ?>">
                        <i class="fa fa-upload"></i> <?= lang('updates'); ?>
                    </a> -->
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><?= lang('sales_chart'); ?></h3>
                        </div>
                        <div class="box-body">
                            <div id="chart" style="height:300px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><?= lang('top_products').' ('.date('F Y').')'; ?></h3>
                        </div>
                        <div class="box-body">
                            <div id="chart2" style="height:300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?= lang('tagged_count'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div id="chart3" style="height:300px;"></div>
                    </div>
                </div>
            </div>
           
            <!--Tagged Table(List)-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="SLRData" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <tr class="active">
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-sm-2"><?= lang("date"); ?></th>
                                    <th class="col-sm-1"><?= lang("customer"); ?></th>
                                    <th class="col-sm-1"><?= lang("total_quantity"); ?></th>
                                    <th class="col-sm-1"><?= lang("total_items"); ?></th>
                                    <th class="col-sm-1"><?= lang("status"); ?></th>
                                    <th class="col-sm-2"><?= lang("tick_print"); ?></th>
                                    <th class="col-sm-2"><?= lang("tag_print"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-1"><span class="datepickercon"><input type="text" class="text_filter datepicker" placeholder="[<?= lang('date'); ?>]"></span></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('customer'); ?>]"></th>
                                    <th class="col-sm-1"><?= lang("total_quantity"); ?></th>
                                    <th class="col-sm-1"><?= lang("total_items"); ?></th>
                                    <th class="col-sm-1">
                                        <select class="select2 select_filter"><option value=""><?= lang("all"); ?></option><option value="paid"><?= lang("paid"); ?></option><option value="partial"><?= lang("partial"); ?></option><option value="due"><?= lang("due"); ?></option></select>
                                    </th>
                                    <th class="col-sm-1"><span class="datepickercon"><input type="text" class="text_filter datepicker" placeholder="[<?= lang('tick_print'); ?>]"></span></th>
                                    <th class="col-sm-1"><span class="datepickercon"><input type="text" class="text_filter datepicker" placeholder="[<?= lang('tag_print'); ?>]"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="8" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <?php if ($this->input->post('customer')) { ?>
            <div class="row">
                <div class="col-md-3">
                    <button class="btn bg-purple btn-lg btn-block" style="cursor:default;">
                        <strong><?= $this->tec->formatMoney($total_sales->number, 0); ?></strong>
                        <?= lang("sales"); ?>
                    </button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-lg btn-block" style="cursor:default;">
                        <strong><?= $this->tec->formatMoney($total_sales->amount); ?></strong>
                        <?= lang("amount"); ?>
                    </button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success btn-lg btn-block" style="cursor:default;">
                        <strong><?= $this->tec->formatMoney($total_sales->paid); ?></strong>
                        <?= lang("paid"); ?>
                    </button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-warning btn-lg btn-block" style="cursor:default;">
                        <strong><?= $this->tec->formatMoney($total_sales->amount-$total_sales->paid); ?></strong>
                        <?= lang("due"); ?>
                    </button>
                </div>
            </div>
        <?php } ?>
        <!--end-->
        </div>
    </div>
</section>
