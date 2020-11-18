<link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/jquery.app.js'); ?>"></script>

<script src="<?php echo base_url('assets/plugins/moment/moment.js'); ?>"></script>
<link href="<?php echo base_url('assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet'); ?>">
<script src="<?php echo base_url('assets/plugins/timepicker/bootstrap-timepicker.js'); ?>"></script>


<link href="<?php echo base_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>

<link href="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css" rel="stylesheet'); ?>">
<script src="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js'); ?>"></script>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="page-title">Report</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <form class="form-horizontal" role="form">
                        <div class="row">
                            <div class="form-group has-success">
                                <label class="control-label col-sm-3 text-right">From</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group" id="date-range-new">
                                        <input type="text" class="form-control" id="from" name="from" />
                                        <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                        <input type="text" class="form-control" id="end" name="end" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onRefresh();">Refresh</button>
                </div>
            </div>

            <table id="table1" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>



        </div> <!-- container -->
    </div> <!-- content -->
</div> <!-- content-page -->
</div>
<!-- END wrapper -->
<script type="text/javascript">
    jQuery('#from').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    jQuery('#end').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        // use24hours: true
    });

    var tableMatch;
    var tableName = "<?php echo $table; ?>";

    var handleDataTableButtonsMatch = function() {
        tableMatch = $("#table1").DataTable({
            dom: "lfBrtip",
            buttons: [{
                extend: "copy",
                className: "btn-sm"
            }, {
                extend: "csv",
                className: "btn-sm"
            }, {
                extend: "excel",
                className: "btn-sm"
            }, {
                extend: "pdf",
                className: "btn-sm"
            }, {
                extend: "print",
                className: "btn-sm"
            }],
            responsive: !0,
            processing: true,
            serverSide: false,
            sPaginationType: "full_numbers",
            language: {
                paginate: {
                      next: '<i class="fa fa-angle-right"></i>',
                      previous: '<i class="fa fa-angle-left"></i>',
                      first: '<i class="fa fa-angle-double-left"></i>',
                      last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            //Set column definition initialisation properties.
            columnDefs: [
                { 
                    targets: [ 0 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 1 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 2 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                }                
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/get_report') ?>",
                type: "POST",
            },
        })
    },
    TableManageButtonsMatch = function() {
        return {
            init: function() {
                handleDataTableButtonsMatch()
            }
        }
    }();
    TableManageButtonsMatch.init();

    function onRefresh()
    {
        if(jQuery('#from').val()=='') return;
        if(jQuery('#end').val()=='') return;
        tableMatch.ajax.url("<?php echo site_url('Cms_api/get_report') ?>" + "/" + jQuery('#from').val() + "/" + jQuery('#end').val());
        tableMatch.ajax.reload();
    }

</script>