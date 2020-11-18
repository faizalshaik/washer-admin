<link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css');?>" rel="stylesheet" />
<script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js');?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/jquery.app.js');?>"></script>
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
                        <h4 class="page-title">Kiosk Transactions</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="reset" class="btn btn-default waves-effect waves-light m-l-5" onclick="onClearHistory()">
                            Clear
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="widget-bg-color-icon card-box fadeInDown animated">
                            <div class="bg-icon bg-icon-custom pull-left">
                                <i class="md md-account-balance-wallet text-custom"></i>
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-lg-2 text-center">
                                        <h3 class="text-dark"><b class="counter"><?php echo '$ '.$total; ?></b></h3>
                                        <p class="text-muted">Total</p>
                                    </div>
                                    <?php foreach($bills as $bill=>$counter) { ?>
                                        <div class="col-lg-1 text-right">
                                            <h3 class="text-dark"><b class="counter"><?php echo $counter; ?></b></h3>
                                            <p class="text-muted"><?php echo '$ '.$bill; ?></p>
                                        </div>
                                    <?php } ?>

                                    <!-- <div class="col-lg-3">
                                        <h3 class="text-dark"><b class="counter">2</b></h3>
                                        <p class="text-muted">Experts</p>
                                    </div>                                            
                                    <div class="col-lg-3">
                                        <h3 class="text-dark"><b class="counter">3</b></h3>
                                        <p class="text-muted">Total User</p>
                                    </div>                                             -->
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>                
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">

                        <!-- <div class="row">
                            <div class="col-sm-3">
                                <h4 class="m-t-0 header-title"><b>Incomes</b></h4>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="washers" class="col-sm-2 control-label">Date:</label>
                                    <div class="col-sm-9">
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group" id="date-range" onchange="onChangeDate()">
                                                <input type="text" class="form-control" name="start">
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control" name="end">
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <button type="reset" class="btn btn-default waves-effect waves-light m-l-5" onclick="onClearHistory()">
                                    Clear
                                </button>
                            </div>

                        </div> -->
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Phone</th>
                                        <th>Price</th>
                                        <th>Count</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Phone</th>
                                        <th>Price</th>
                                        <th>Count</th>
                                        <th>Date</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Service</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_special'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="specialId" id="specialId">

                                <div class="form-group">
                                    <label for="price">Name</label>
                                    <input type="text" id="sp_name" name="sp_name" parsley-trigger="change" required placeholder="Enter price" 
                                        class="form-control">
                                </div>

                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                                        Save
                                    </button>
                                    <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
                                        Clear
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>                       -->

                </div>
            </div> <!-- container -->
        </div> <!-- content -->    
    </div> <!-- content-page -->
</div>
        <!-- END wrapper -->
<script type="text/javascript">
    var tableMatch;
    var tableName = "<?php echo $table; ?>";

    var $dom = {
        startDT:$("input[name=start]"),
        endDT:$("input[name=end]")
    };      

// jQuery(document).ready(function () {
//     //Date range picker    
//     jQuery('#date-range').datepicker({
//         toggleActive: true,
//         dateFormat: 'yyyy-mm-dd'
//     });

// });


function onChangeDate()
{
    var startDt = $dom.startDT.val();
    var endDt = $dom.endDT.val();
    if(startDt!="")
    {
        var res = startDt.split("/");
        if(res.length ==3)
            startDt = res[2] + "-" + res[0] + "-" + res[1];
    }

    if(endDt!="")
    {
        var res = endDt.split("/");
        if(res.length ==3)
            endDt = res[2] + "-" + res[0] + "-" + res[1];
    }
    tableMatch.ajax.url("<?php echo site_url('Cms_api/getKioskIncomes/')?>" + 
        "/" + startDt + 
        "/" + endDt);
    tableMatch.ajax.reload(null,false); //reload datatable ajax 
}


function onClearHistory()
{
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this user information!",
            type: "error",
            showCancelButton: true,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Remove',
            closeOnConfirm: false
        }, function(isConfirm) {
            if(isConfirm) {
                $.post("<?php echo site_url('Cms_api/clearIncomeHistory')?>", function(data){
                    window.location = "<?php echo site_url('Cms/incomes/')?>";
                });
            }
        });

}

    var handleDataTableButtonsMatch = function() {
        tableMatch = $("#datatable-match").DataTable({
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
                    targets: [ 1 ], //last column
                    orderable: true, //set not orderable
                    className: "actions dt-center"
                },               
                { 
                    targets: [ 2 ], //last column
                    orderable: true, //set not orderable
                    className: "actions dt-center"
                },               
                { 
                    targets: [ 3 ], //last column
                    orderable: true, //set not orderable
                    className: "actions dt-center"
                }                                
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/getKioskIncomes')?>",
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

    var msg = "<?php if($this->session->flashdata('messagePr')) { echo $this->session->flashdata('messagePr'); 
                    $this->session->unset_userdata('messagePr');} else echo 'no'?>";
    if(msg !='no') {
        if(msg.includes('Successfully')) 
            $.Notification.notify('success','bottom right','Success', msg);
        else
            $.Notification.notify('error','bottom right','Error', msg);
    }
</script>