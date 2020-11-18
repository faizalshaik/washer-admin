<link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/jquery.app.js'); ?>"></script>

<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<link href="https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>

  

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
                    <h4 class="page-title">Washer Transactions</h4>
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
                            <i class="md md-account-balance text-custom"></i>
                        </div>
                        <div>
                            <div class="row text_right">
                                <div class="col-lg-3">
                                    <h3 class="text-dark"><b class="counter"><?php echo $total; ?></b></h3>
                                    <p class="text-muted">Total Price</p>
                                </div>
                                <div class="col-lg-3">
                                    <h3 class="text-dark"><b class="counter"><?php echo $charge; ?></b></h3>
                                    <p class="text-muted">Total Charge</p>
                                </div>
                                <div class="col-lg-3">
                                    <h3 class="text-dark"><b class="counter"><?php echo $counts; ?></b></h3>
                                    <p class="text-muted">Total Counts</p>
                                </div>
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
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th># of Washers</th>
                                    <th>Account Type</th>
                                    <th>Payment Type</th>
                                    <th>Charge</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- <div class="col-sm-3">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Edit Service</b></h4>
                            <form action="<?php echo base_url() . 'Cms_api/edit_special' ?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
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



<div id="eidt-modal" class="modal-demo col-sm-12" style="padding: 0px !important;">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Transaction detail</h4>

    <div class="custom-modal-text text-left">
        <div class="profile-detail card-box">
            <form class="form-horizontal" role="form" id="trForm" style="width:480px;">
                <div class="row">
                    <div class="form-group has-success ">
                        <label class="col-md-4 control-label">Transaction ID</label>
                        <div class="col-md-3">
                            <p id="trId" style="padding-top:7px;">12345567</p>
                        </div>
                        <label class="col-md-2 control-label">Date</label>
                        <div class="col-md-3">
                            <p id="trDate" style="padding-top:7px;">11/18/2020</p>
                        </div>
                    </div>
                    <div class="form-group has-success  ">
                        <label class="col-md-4 control-label">Transaction type</label>
                        <div class="col-md-3">
                            <p id="trType" style="padding-top:7px;">Credit Card</p>
                        </div>
                        <label class="col-md-2 control-label">time</label>
                        <div class="col-md-3">
                            <p id="trTime" style="padding-top:7px;">11:05 PM</p>
                        </div>
                    </div>
                    <div class="form-group has-success  ">
                        <label class="col-md-4 control-label">Account type</label>
                        <div class="col-md-3">
                            <p id="accType" style="padding-top:7px;">Inhouse</p>
                        </div>
                    </div>
                </div>

                <table id="tblTrs" class="table table-striped table-bordered  m-t-10">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Washer #</th>
                            <th>Program</th>
                            <th>Options</th>
                            <th>Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </form>
            <div class="text-center m-t-30">
                <button type="button" class="btn btn-pink btn-custom btn-rounded waves-effect waves-light" onclick="printJS({ printable: 'trForm', type: 'pdf', header: 'PrintJS - Form Element Selection' })">Print</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var tableMatch;
    var tableName = "<?php echo $table; ?>";


    //print part
    var PAGE_WIDTH = 500;
    var PAGE_HEIGHT = 700;
    const content = [];

    function getPngDimensions(base64) {
        const header = atob(base64.slice(22, 70)).slice(16, 24);
        const uint8 = Uint8Array.from(header, c => c.charCodeAt(0));
        const dataView = new DataView(uint8.buffer);
        return {
            width: dataView.getInt32(0),
            height: dataView.getInt32(4)
        };
    }
    const splitImage = (img, content, callback) => () => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const printHeight = img.height * PAGE_WIDTH / img.width;

        canvas.width = PAGE_WIDTH;

        for (let pages = 0; printHeight > pages * PAGE_HEIGHT; pages++) {
            /* Don't use full height for the last image */
            canvas.height = Math.min(PAGE_HEIGHT, printHeight - pages * PAGE_HEIGHT);
            ctx.drawImage(img, 0, -pages * PAGE_HEIGHT, canvas.width, printHeight);
            content.push({
                image: canvas.toDataURL(),
                margin: [0, 5],
                width: PAGE_WIDTH
            });
        }

        callback();
    };

    function next() {
        /* add other content here, can call addImage() again for example */
        pdfMake.createPdf({
            content
        }).download();
    }

    function onPrint() {
        var ele = document.getElementById("trForm");
        html2canvas(ele, {
            onrendered: function(canvas) {
                var image = canvas.toDataURL();
                const {
                    width,
                    height
                } = getPngDimensions(image);
                const printHeight = height * PAGE_WIDTH / width;

                if (printHeight > PAGE_HEIGHT) {
                    const img = new Image();
                    img.onload = splitImage(img, content, next);
                    img.src = image;
                    return;
                }

                content.push({
                    image,
                    margin: [0, 5],
                    width: PAGE_WIDTH
                });
                next();
            }
        });

    }

    var $dom = {
        startDT: $("input[name=start]"),
        endDT: $("input[name=end]")
    };

    // jQuery(document).ready(function () {
    //     //Date range picker    
    //     jQuery('#date-range').datepicker({
    //         toggleActive: true,
    //         dateFormat: 'yyyy-mm-dd'
    //     });

    // });


    // function onChangeDate()
    // {
    //     var startDt = $dom.startDT.val();
    //     var endDt = $dom.endDT.val();
    //     if(startDt!="")
    //     {
    //         var res = startDt.split("/");
    //         if(res.length ==3)
    //             startDt = res[2] + "-" + res[0] + "-" + res[1];
    //     }

    //     if(endDt!="")
    //     {
    //         var res = endDt.split("/");
    //         if(res.length ==3)
    //             endDt = res[2] + "-" + res[0] + "-" + res[1];
    //     }
    //     tableMatch.ajax.url("<?php echo site_url('Cms_api/getWasherTransactions/') ?>" + 
    //         "/" + startDt + 
    //         "/" + endDt);
    //     tableMatch.ajax.reload(null,false); //reload datatable ajax 
    // }


    function onClearHistory() {
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
                $.post("<?php echo site_url('Cms_api/clearWasherTransactionHistory') ?>", function(data) {
                    window.location = "<?php echo site_url('Cms/washer_transactions/') ?>";
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
                columnDefs: [{
                        targets: [0], //first column 
                        orderable: true, //set not orderable
                        className: "dt-center"
                    },
                    {
                        targets: [1], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    },
                    {
                        targets: [2], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    },
                    {
                        targets: [3], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    },
                    {
                        targets: [4], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    },
                    {
                        targets: [5], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    },
                    {
                        targets: [6], //last column
                        orderable: true, //set not orderable
                        className: "actions dt-center"
                    }

                ],
                ajax: {
                    url: "<?php echo site_url('Cms_api/getWasherTransactions') ?>",
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


    function viewTR(_idx) {
        $.ajax({
            url: "<?php echo site_url('Cms_api/getWasherTransactionsByTrId') ?>",
            data: {
                Id: _idx
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                document.getElementById('tblTrs').tBodies[0].innerHTML = '';

                let trType = '',
                    accType = '',
                    dt = '';
                let total = 0.0,
                    no = 0,
                    content = '',
                    extraCharge = 0.0;

                for (let i = 0; i < data.length; i++) {
                    if (i == 0) {
                        trType = data[i].method;
                        accType = data[i].accType;
                        dt = data[i].dt;
                    }
                    total += parseFloat(data[i].price);
                    no++;
                    content += '<tr><td>' + no + '</td><td>' + data[i].machine + '</td><td>' +
                        data[i].program + '</td><td>' + data[i].option + '</td><td>$' + data[i].price + '</td></tr>';
                }
                content += '<tr><td></td><td></td><td></td><td>Sub-total</td><td>' + total + '</td></tr>';
                if (trType == "CardReader") {
                    content += '<tr><td></td><td></td><td></td><td>CC Charge</td><td>$0.50</td></tr>';
                    extraCharge = 0.50;
                }
                content += '<tr><td></td><td></td><td></td><td>Total</td><td>$' + (total + extraCharge) + '</td></tr>';
                document.getElementById('tblTrs').tBodies[0].innerHTML = content;
                document.getElementById('trId').innerText = _idx;
                document.getElementById('trType').innerText = trType;
                document.getElementById('accType').innerText = accType;

                var res = dt.split(" ");
                document.getElementById('trDate').innerText = res[0];
                document.getElementById('trTime').innerText = res[1];


                Custombox.open({
                    target: "#eidt-modal",
                    effect: "fadein",
                    overlaySpeed: "200",
                    overlayColor: "#36404a"
                });

            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

</script>