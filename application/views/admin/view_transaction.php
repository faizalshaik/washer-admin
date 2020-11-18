<link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css');?>" rel="stylesheet" />
<script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js');?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/jquery.app.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js');?>"></script>

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
                        <h4 class="page-title">Manage Transaction</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-5">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Users</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-flexar" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Phone</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Phone</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-7">
                    <div class="card-box table-responsive">

                        <h4 class="m-t-0 header-title"><b>Transactions</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-transaction" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Machine</th>
                                        <th>Program</th>
                                        <th>Price</th>
                                        <th>Balance</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Date</th>
                                        <th>Machine</th>
                                        <th>Program</th>
                                        <th>Price</th>
                                        <th>Balance</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div> <!-- container -->
        </div> <!-- content -->    
    </div> <!-- content-page -->
</div>
        <!-- END wrapper -->

<div id="user-balance-modal" class="modal-demo  col-sm-12" >
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title" id="_game_id">Edit User Balance</h4>
    <div class="custom-modal-text text-left">
        <div class="profile-detail card-box">
            <div>
                <h4> User Phone </h4>
                <p class="text-muted font-13 m-b-30" id="phone_number" name="phone_number" >
                    1234568
                </p>
                <input type ="text" hidden id="user_id" value="">
                <hr>
                <div class="form-group">
                    <label for="balance">Balance</label>
                    <input type="text" id ="balance" name="balance" parsley-trigger="change" required placeholder="Enter new balance" 
                        class="form-control">
                </div>
                <button type="button" class="btn btn-pink btn-custom btn-rounded waves-effect waves-light" onclick="onSaveBalance();">Save</button>
            </div>            
        </div>
    </div>
</div>


<script type="text/javascript">

    var MAX_TriggerSize = 2359296;

    var $dom = {
        user_id:$("#user_id"),
        phone:$("#phone_number"),
        balance:$("#balance")
    };    

    var table, table1;
    var tableName = "<?php global $MYSQL; echo $MYSQL['_userDB']?>";
    var handleDataTableButtons = function() {
        table = $("#datatable-flexar").DataTable({
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
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 1 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ -1 ], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/getUserBalance')?>",
                type: "POST",
            },
        })
    },
    handleDataTableButtons1 = function() {
        table1 = $("#datatable-transaction").DataTable({
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
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 1 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 2 ], //last column
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 3 ], //last column
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 4 ], //last column
                    orderable: false, //set not orderable
                    className: "dt-center"
                }                
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/getUserTransaction')?>",
                type: "POST",
            },
        })
    },    
    TableManageButtons = function() {
        return {
            init: function() {
                handleDataTableButtons();
            }
        }
    }();
    TableManageButtons.init();

    TableManageButtons1 = function() {
        return {
            init: function() {
                handleDataTableButtons1();
            }
        }
    }();
    TableManageButtons1.init();


    var EditBalance = function(_idx) {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {                
                document.getElementById('user_id').value = data.Id;
                document.getElementById('phone_number').innerText = data.phone;
                document.getElementById('balance').value = data.balance;

                Custombox.open({
                    target: "#user-balance-modal",
                    effect: "fadein",
                    overlaySpeed: "200",
                    overlayColor: "#36404a",
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }
    
    function onSaveBalance()
    {
        var id = document.getElementById('user_id').value;
        var balance = document.getElementById('balance').value;
        Custombox.close();
        $.post( "<?php echo base_url().'Cms_api/saveBalance';?>", {userId:id, balance:balance}, function( data1 )
        {
            table.ajax.reload(null,false);            
        });
    }

    function Viewtransaction(_idx) {
        table1.ajax.url("<?php echo site_url('Cms_api/getUserTransaction/')?>" + "/" + _idx);
        table1.ajax.reload(null,false); //reload datatable ajax 
    }

    var msg = "<?php if($this->session->flashdata('messagePr')) { echo $this->session->flashdata('messagePr'); 
                    $this->session->unset_userdata('messagePr');} else echo 'no'?>";
    if(msg !='no') {
        if(msg.includes('Successfully')) 
            $.Notification.notify('success','bottom right','Success', msg);
        else
            $.Notification.notify('error','bottom right','Error', msg);
    }
</script>