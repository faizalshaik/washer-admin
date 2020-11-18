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
                        <h4 class="page-title">Dryer by id details</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4 class="m-t-0 header-title"><b>Dryer details</b></h4>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="washers" class="col-sm-4 control-label">Dryer:</label>
                                        <div class="col-sm-7">
                                            <select class="selectpicker show-tick form-control" data-style="btn-default btn-custom" id="gener" 
                                            name="washers" onchange="onSelectWasher(this)">
                                            <?php foreach($washers as $washer) {?>
                                                <option value="<?php echo $washer->Id?>"><?php echo $washer->machine_id?></option>
                                            <?}?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date-range" class="col-sm-2 control-label">Date:</label>
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

                            </div>

                            <p class="text-muted font-13 m-b-30"></p>
                            <table id="datatable-match" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date Time</th>                                        
                                        <th>Action</th>
                                        <th>Minutes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Date Time</th>                                        
                                        <th>Action</th>
                                        <th>Minutes</th>
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
<script type="text/javascript">

var $dom = {
        washers:$("#washers"),
        startDT:$("input[name=start]"),
        endDT:$("input[name=end]")
    };      
   

jQuery(document).ready(function () {
    //Date range picker    
    jQuery('#date-range').datepicker({
        toggleActive: true,
        dateFormat: 'yyyy-mm-dd'
    });

});

function onChangeDate()
{
    washerId =$dom.washers.val();

    if(washerId==undefined || washerId=="")
        return;

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
    table.ajax.url("<?php echo site_url('Cms_api/get_dryer_id_details/')?>" + 
        "/" + washerId + 
        "/" + startDt + 
        "/" + endDt);
    reload_table();    
}

function onSelectWasher(obj)
{
    var tableMatch;
    washerId = obj.options[obj.selectedIndex].value;

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
    table.ajax.url("<?php echo site_url('Cms_api/get_washer_time_details/')?>" + 
        "/" + washerId + 
        "/" + startDt + 
        "/" + endDt);
    reload_table();
}

    function reload_table()
    {
        tableMatch.ajax.reload(null,false); //reload datatable ajax 
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
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                { 
                    targets: [ 2 ], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                }
            ],
            ajax: {
                url: "<?php echo site_url('Cms_api/get_washer_time_details')?>",
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