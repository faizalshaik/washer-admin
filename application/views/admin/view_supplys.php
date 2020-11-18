<link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/jquery.app.js'); ?>"></script>
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
                    <h4 class="page-title">Manage Supplies</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-9">
                    <div class="card-box table-responsive">
                        <h4 class="m-t-0 header-title"><b>Supplies</b></h4>
                        <p class="text-muted font-13 m-b-30"></p>
                        <table id="datatable-match" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Edit Supply</b></h4>
                        <form>
                            <input type="hidden" id="supply_id">
                            <div class="form-group">
                                <label for="name">Supply Name</label>
                                <input type="text" name="name" id="name" parsley-trigger="change" required placeholder="Name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" id="price" parsley-trigger="change" required placeholder="Price" class="form-control">
                            </div>

                            <img src="" id="img_supply" style="width:100%" />


                            <input type="file" onchange="photosFileChange(event);" accept="image/x-png,image/gif,image/jpeg" style="position: absolute;  padding: 0;   width: 100%;  height: 38px;  opacity: 0;" />
                            <button class="btn btn-block btn-success" type="button"><i class="fa fa-upload"></i> Upload image</button>

                            <div class="form-group text-right m-b-0 m-t-10">
                                <button class="btn btn-primary waves-effect waves-light" type="button" onclick="onSave()">
                                    Save
                                </button>
                                <button type="reset" onclick="clearForm()" class="btn btn-default waves-effect waves-light m-l-5">
                                    Clear
                                </button>
                            </div>

                            <div class="form-group m-t-20">
                                <label for="price">Current Qty</label>
                                <input type="text" id="cur_qty" parsley-trigger="change" readonly class="form-control">
                            </div>                            
                            <div class="form-group">
                                <label for="price">Charge Supply</label>
                                <input type="number" id="charge_count" parsley-trigger="change" required placeholder="charge count" class="form-control">
                            </div>
                            <div class="form-group text-right m-b-0 m-t-10">
                                <button class="btn btn-primary waves-effect waves-light" type="button" onclick="onCharge()">
                                    Charge
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
    var newImg = "";
    var tableMatch;
    var tableName = "<?php echo $table; ?>";

    var $dom = {
        Id: $("#supply_id"),
        name: $("#name"),
        price: $("#price"),
        cur_qty: $("#cur_qty"),
        charge_count: $("#charge_count")
    }

    function clearForm() {
        $dom.Id.val("");
        $dom.name.val("");
        $dom.price.val("");
        $("#img_supply").attr("src", "");
        newImg = "";
    }

    function photosFileChange(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var output = document.getElementById('img_supply');
            output.src = dataURL;
            newImg = dataURL;
        };
        reader.readAsDataURL(input.files[0]);
    }

    function onEdit(_idx) {
        $.ajax({
            url: "<?php echo site_url('Cms_api/getDataById') ?>",
            data: {
                Id: _idx,
                tbl_Name: tableName
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                newImg = "";
                $dom.Id.val(data.Id);
                $dom.name.val(data.name);
                $dom.price.val(data.price);
                $dom.cur_qty.val(data.qty);                
                $("#img_supply").attr("src", "<?php echo base_url(); ?>" + data.img);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

    function onRemove(_idx) {
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
            if (isConfirm) {
                $.ajax({
                    url: "<?php echo site_url('Cms_api/delete_supply') ?>",
                    data: {
                        Id: _idx
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        swal("Remove!", "", "success");
                        tableMatch.ajax.reload(null, false);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // alert('Error get data from ajax');
                        swal("Error!", "", "error");
                    }
                });
            }
        });
    }

    function onCharge(_idx) {
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
            if (isConfirm) {
                $.ajax({
                    url: "<?php echo site_url('Cms_api/delete_supply') ?>",
                    data: {
                        Id: _idx
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        swal("Remove!", "", "success");
                        tableMatch.ajax.reload(null, false);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // alert('Error get data from ajax');
                        swal("Error!", "", "error");
                    }
                });
            }
        });
    }    

    function onCharge()
    {
        let Id = $dom.Id.val();
        let charge = $dom.charge_count.val(); 
        if(Id==0 || charge<=0) return;
        $.ajax({
            url: "<?php echo site_url('Cms_api/charge_supply') ?>",
            data: {
                Id: Id,
                charge:charge
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                swal("Charged!", "", "success");
                tableMatch.ajax.reload(null, false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });

    }

    function onSave() {
        let Id = $dom.Id.val();
        let name = $dom.name.val();
        let price = $dom.price.val();
        if (name == "" || price == "") return;

        $.ajax({
            url: "<?php echo site_url('Cms_api/addEdit_supply') ?>",
            data: {
                Id: Id,
                name:name,
                price:price,
                img:newImg
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                swal("Add new!", "", "success");
                tableMatch.ajax.reload(null, false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
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
                columnDefs: [{
                        targets: [0], //first column 
                        orderable: true, //set not orderable
                        className: "dt-center"
                    },
                    {
                        targets: [1], //first column 
                        orderable: false, //set not orderable
                        className: "dt-center"
                    },
                    {
                        targets: [2], //first column 
                        orderable: false, //set not orderable
                        className: "dt-center"
                    },
                    {
                        targets: [3], //first column 
                        orderable: false, //set not orderable
                        className: "dt-center"
                    },
                    {
                        targets: [-1], //last column
                        orderable: false, //set not orderable
                        className: "actions dt-center"
                    }
                ],
                ajax: {
                    url: "<?php echo site_url('Cms_api/get_supplies') ?>",
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
</script>