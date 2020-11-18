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
                        <h4 class="page-title">Manage Day services</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Day services</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Model</th>
                                        <th>Program</th>
                                        <th>Start Day</th>
                                        <th>Start Time</th>
                                        <th>End Day</th>
                                        <th>End Time</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Type</th>
                                        <th>Model</th>
                                        <th>Program</th>
                                        <th>Start Day</th>
                                        <th>Start Time</th>
                                        <th>End Day</th>
                                        <th>End Time</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                     
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Service</b></h4>
                            <form action="<?php echo base_url().'Cms_api/edit_day_service'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="serviceId" id="serviceId">
                                <div class="form-group">
                                    <label for="model" class="col-sm-4 control-label">Model:</label>
                                   
                                        <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="model" 
                                        name="model">
                                        <?php foreach($models as $model) {?>
                                            <option value="<?php echo $model['Id']?>"><?php echo $model['name']?></option>
                                        <?}?>
                                        </select>
                                    
                                </div>

                                <div class="form-group">
                                    <label for="program" class="col-sm-4 control-label">Program:</label>
                                   
                                        <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="program" 
                                        name="program">
                                        <?php foreach($programs as $program) {?>
                                            <option value="<?php echo $program->Id?>"><?php echo $program->name?></option>
                                        <?}?>
                                        </select>
                                    
                                </div>

                                <div class="form-group">
                                    <label for="start_day" class="col-sm-4 control-label">Start Day:</label>
                                    
                                        <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="start_day" 
                                        name="start_day">
                                        <?php foreach($days as $day) {?>
                                            <option value="<?php echo $day->Id?>"><?php echo $day->name?></option>
                                        <?}?>
                                        </select>
                                </div>

                                <div class="form-group">
                                    <label for="start_time">Start Time</label>
                                    <input type="text" id="start_time" name="start_time" parsley-trigger="change" required placeholder="Enter Start Time" 
                                        class="form-control">
                                </div>


                                <div class="form-group">
                                    <label for="end_day" class="col-sm-4 control-label">End Day:</label>
                                    
                                        <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="end_day" 
                                        name="end_day">
                                        <?php foreach($days as $day) {?>
                                            <option value="<?php echo $day->Id?>"><?php echo $day->name?></option>
                                        <?}?>
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input type="text" id="end_time" name="end_time" parsley-trigger="change" required placeholder="Enter end time" 
                                        class="form-control">
                                </div>


                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" id="price" name="price" parsley-trigger="change" required placeholder="Enter price" 
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
        serviceId:$("#serviceId"),        
        model:$("#model"),
        program:$("#program"),
        start_day:$("#start_day"),        
        start_time:$("#start_time"),        
        end_day:$("#end_day"),        
        end_time:$("#end_time"),
        price:$("#price"),
    }      

    function clearForm()
    {
        $dom.serviceId.val("");
        $dom.model.val("");
        $dom.program.val("");

        $dom.start_day.val("");
        $dom.start_time.val("");
        $dom.end_day.val("");
        $dom.end_time.val("");
        $dom.price.val("");
    }

    function EditService(_idx) 
    {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $dom.serviceId.val(data.Id);
                $dom.model.val(data.model_id);
                $dom.model.selectpicker('refresh');

                $dom.program.val(data.program_id);
                $dom.program.selectpicker('refresh');

                $dom.start_day.val(data.start_day);
                $dom.start_day.selectpicker('refresh');
                $dom.start_time.val(data.start_time);

                $dom.end_day.val(data.end_day);
                $dom.end_day.selectpicker('refresh');
                $dom.end_time.val(data.end_time);

                $dom.price.val(data.price);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }

    function RemoveService(_idx) {
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
                        tableMatch.ajax.reload(null,false);
                        swal("Remove!", "", "success");
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
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 4 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 5 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 6 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 7 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },               
                { 
                    targets: [ -1 ], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/get_day_services')?>",
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