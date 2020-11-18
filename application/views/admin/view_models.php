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
                        <h4 class="page-title">Manage Machines</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Machines</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Model</th>                                        
                                        <th>Type</th>
                                        <th>Weight</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Model</th>                                        
                                        <th>Type</th>
                                        <th>Weight</th>
                                        <th>Action</th>                                        
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Machine</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_model'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="modelId">
                                <div class="form-group">
                                    <label for="model">Model*</label>
                                    <input type="text" name="model" id="model" parsley-trigger="change" required placeholder="Enter model number"
                                        class="form-control">
                                </div>

                                <div class="form-group">                                
                                    <label for="type">Type</label>
                                    <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="typeId" name="typeId">
                                        <option value='0'>Washer</option>
                                        <option value='1'>Dryer</option>
                                    </select>
                                </div>                                


                                <div class="form-group">
                                    <label for="weight">Weight*</label>
                                    <input type="text" name="weight" id="weight" parsley-trigger="change" required placeholder="Enter weight" 
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
        modelId:$("input[name=modelId]"),
        model:$("input[name=model]"),
        typeId:$("input[name=typeId]"),
        weight:$("input[name=weight]"),
    }      

    function clearForm()
    {
        $dom.modelId.val("");
        $dom.model.val("");
        $dom.typeId.val("");
        $dom.weight.val("");
    }
    function EditModel(_idx) 
    {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $dom.modelId.val(data.Id);
                $dom.model.val(data.model);
                $dom.typeId.val(data.type);
                $dom.typeId.selectpicker('refresh');
                $dom.weight.val(data.weight);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }

    function RemoveModel(_idx) {
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
                        tableMatch.ajax.reload(null,false);                        
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
                    targets: [ -1 ], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/get_models')?>",
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