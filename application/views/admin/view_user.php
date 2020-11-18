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
                        <h4 class="page-title">Manage User</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Users</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-flexar" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Company Name</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Active</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Company Name</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Active</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Add&Edit User</b></h4>
                            <!-- <p class="text-muted font-13 m-b-30">
                                You can input here. (You don`t have to input same price id.)
                            </p> -->
                            <form action="<?php echo base_url().'Cms_api/addEditUser'?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <input type="hidden" name="user_id">
                                <input type="hidden" name="oldAvarta">

                                <div class="trigger_pic">
                                    <img class="thumb-md" id='avatarImg' name='avatarImg' src="">
                                </div>
                                <div class="trigger_fileUpload btn btn-default m-t-15">
                                    <span> Change Avatar Image </span>
                                    <input type="file" name="avatar" id="avatar" accept="image/x-png,image/jpeg" class="trigger_uploadfile">
                                </div>                                


                                <div class="form-group">
                                    <label for="cost_id">Select Company Name*</label>
                                    <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="company_id" 
                                        name="company_id">
                                        <?php foreach($company as $item) {?>
                                            <option value="<?php echo $item->Id?>"><?php echo $item->name?></option>
                                        <?}?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="user_type">Select User Type*</label>
                                    <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="user_type" 
                                        name="user_type">
                                        <option value="guest">Guest</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>               

                                <div class="form-group">
                                    <label for="fname">User Full Name*</label>
                                    <input type="text" name="fname" parsley-trigger="change" required placeholder="Enter User Full Name" 
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address*</label>
                                    <input type="email" name="email" parsley-trigger="change" required placeholder="Enter Email Address" 
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password*</label>
                                    <input type="text" name="password" parsley-trigger="change" required placeholder="Enter Password" 
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password">About*</label>
                                    <textarea type="text" name="about" id="about" parsley-trigger="change" required placeholder="Enter About" 
                                        class="form-control"></textarea>
                                </div>                                
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                                        Save
                                    </button>
                                    <button type="reset" class="btn btn-default waves-effect waves-light m-l-5" onclick="test()">
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

    var MAX_TriggerSize = 2359296;
    function test()
    {
        $.post( "<?php echo site_url('Api/deleteQuest')?>", function( data ) {
          alert(data);
        });

    }

    var table;
    var tableName = "<?php global $MYSQL; echo $MYSQL['_userDB']?>";
    var $dom = {
        userId:$("input[name=user_id]"),
        oldAvatar:$("input[name=oldAvatar]"),
        avatarImg:$("#avatarImg"),
        avatar:$("#avatar"),
        companyId:$("#company_id"),
        fname:$("input[name=fname]"),
        email:$("input[name=email]"),
        password:$("input[name=password]"),
        userType:$("#user_type"),
        about:$("#about")
    }

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
            serverSide: true,
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
                    targets: [ 2 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 3 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 4 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 5 ], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 6 ], //first column 
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
                url: "<?php echo site_url('Cms_api/getUser')?>",
                type: "POST",
            },
        })
    },
    TableManageButtons = function() {
        return {
            init: function() {
                handleDataTableButtons()
            }
        }
    }();

    TableManageButtons.init();
    clearForm();

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }
    var EditUser = function(_idx) {
        $.ajax({
            url : "<?php echo site_url('Cms_api/getDataById')?>",
            data: {Id:_idx, tbl_Name: tableName},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                // reload_table();
                $dom.userId.val(data.Id);
                $dom.companyId.val(data.company_id);
                $dom.companyId.selectpicker('refresh');

                $dom.userType.val(data.user_type);
                $dom.userType.selectpicker('refresh');

                $dom.fname.val(data.fname);
                $dom.email.val(data.email);
                $dom.password.val(data.pass);
                $dom.about.val(data.about);

                if(data.avatar!=undefined && data.avatar!='')
                {
                    $dom.oldAvatar.val(data.avatar);
                    $dom.avatarImg.attr("src", "<?php echo site_url('uploads/avatar'); ?>" + "/" + data.avatar);
                }
                else
                {
                    $dom.oldAvatar.val('');
                    $dom.avatarImg.attr("src", "");
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal("Error!", "", "error");  
            }
        });
    }
    
    function RemoveUser(_idx) {
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
                    url : "<?php echo site_url('Cms_api/delUser')?>",
                    data: {Id:_idx, tbl_Name:tableName},
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        swal("Remove!", "", "success");
                        table.ajax.reload(null,false);                        
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
    
    function clearForm(){
        $dom.userId.val("");
        $dom.fname.val("");
        $dom.email.val("");
        $dom.password.val("");
        $dom.about.val("");
        $dom.oldAvatar.val("");
        $dom.avatar.val("");
        $dom.avatarImg.attr("src", '');
    }

    $dom.avatar.on('change', function() {
        if(this.files[0].size > MAX_TriggerSize){
           showMsg("Error", "Max size is 2.25MB.");
        } else {
            if (typeof (FileReader) != "undefined") {
                
                var reader = new FileReader();
                reader.onload = function (e) {
                    $dom.avatarImg.attr("src", e.target.result);
                }
                reader.readAsDataURL($(this)[0].files[0]);
            } else {
                showMsg("Error", "This browser does not support FileReader.");
            }
        }
    });


    var msg = "<?php if($this->session->flashdata('messagePr')) { echo $this->session->flashdata('messagePr'); 
                    $this->session->unset_userdata('messagePr');} else echo 'no'?>";
    if(msg !='no') {
        if(msg.includes('Successfully')) 
            $.Notification.notify('success','bottom right','Success', msg);
        else
            $.Notification.notify('error','bottom right','Error', msg);
    }
</script>