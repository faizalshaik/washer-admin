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
                        <h4 class="page-title">Manage Home Bank</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Home Bank</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Coin</th>
                                        <th>Count</th>
                                        <th>Limit</th>
                                        <th>Count For Set</th>                                        
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Coin</th>
                                        <th>Count</th>
                                        <th>Limit</th>
                                        <th>Count For Set</th>                                                                                
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-3">

                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Admin phone</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_admin_phone'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="coin">Admin phone</label>
                                    <input type="text" id="admin_phone" name="admin_phone" parsley-trigger="change" required placeholder="Enter admin phone" 
                                        class="form-control" value="<?php echo $admin_phone; ?>">
                                </div>
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Flag for setting count</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_hopper_set_count_flag'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input class="todo-done" id="hopper_flag" name="hopper_flag" type="checkbox" <?php if($hopper_flag=="1") echo 'checked';?> >
                                        <label for="6">Setting Flag</label>
                                    </div>
                                </div>

                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>


                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Bank</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_coin'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="coinId" id="coinId">
                                <div class="form-group">
                                    <label for="coin">Coin</label>
                                    <input type="text" id="coin" name="coin" parsley-trigger="change" required placeholder="Enter coin" 
                                        class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="limit">limit</label>
                                    <input type="text" id="limit_cnt" name="limit_cnt" parsley-trigger="change" required placeholder="Enter limit" 
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="limit">Setting Count</label>
                                    <input type="text" id="cnt_for_set" name="cnt_for_set" parsley-trigger="change" required placeholder="Enter Seeting Count" 
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
                    </div>                      

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
        coinId:$("#coinId"),        
        coin:$("#coin"),
        limit_cnt:$("#limit_cnt"),
        cnt_for_set:$("#cnt_for_set"),
    }      

    function clearForm()
    {
        $dom.coinId.val("");
        $dom.coin.val("");  
        $dom.limit_cnt.val("");
        $dom.cnt_for_set.val("");
    }

    function EditCoin(_idx) 
    {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $dom.coinId.val(data.Id);
                $dom.coin.val(data.coin); 
                $dom.limit_cnt.val(data.limit_cnt);
                $dom.cnt_for_set.val(data.cnt_for_set);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }

    function RemoveCoin(_idx) {
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
                $.ajax({
                    url : "<?php echo site_url('Cms_api/delData')?>",
                    data: {Id:_idx, tbl_Name:tableName},
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        swal("Remove!", "", "success");
                        tableMatch.ajax.reload(null,false); //reload datatable ajax 
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        // alert('Error get data from ajax');
                        swal("Error!", "", "error");  
                    }
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
                    targets: [ 1 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 2 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 3 ], //first column 
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
                url: "<?php echo site_url('Cms_api/get_coins')?>",
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