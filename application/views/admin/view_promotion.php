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
                        <h4 class="page-title">Manage Promotion</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Promotion</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Price</th>
                                        <th>Bonus</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Price</th>
                                        <th>Bonus</th>
                                        <th>Action</th>                                       
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Bonus</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_bonus'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="promotionId" id="promotionId">

                                <div class="form-group">
                                    <label for="price">Name</label>
                                    <input type="text" id="price" name="price" parsley-trigger="change" required placeholder="Enter Price" 
                                        class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="bonus">Bonus</label>
                                    <input type="text" id="bonus" name="bonus" parsley-trigger="change" required placeholder="Enter bonus" 
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
        promotionId:$("#promotionId"),        
        price:$("#price"),
        bonus:$("#bonus"),
    }      

    function clearForm()
    {
        $dom.promotionId.val("");
        $dom.bonus.val("");  
        $dom.price.val("");
    }

    function EditPromotion(_idx) 
    {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $dom.promotionId.val(data.Id);
                $dom.bonus.val(data.bonus); 
                $dom.price.val(data.price);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }

    function RemovePromotion(_idx) {
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
                    targets: [ -1 ], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/get_promotions')?>",
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