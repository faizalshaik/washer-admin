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
                    <h4 class="page-title">Washer States</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>
            <div class="container" id="washers_status">
                <div class="row" id="status1" style="height:16%;">
                    <!-- <div style="padding-left: 10px;padding-right: 10px;float: left;width:8.3%;">
                        <div style="background-color:darkgrey; text-align:center; width:100%; height:100px;">
                            <img style="height:50px;" src="<?php echo site_url('assets/images/basket.png');?>"/>                                                    
                            <h2 class="text-danger text-center" >11</h2>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 col-sm-6 col-lg-3">
                        <div class="card-box widget-box-1 bg-white">
                            <h2 class="text-primary text-center">$<span data-plugin="counterup">5623</span></h2>
                        </div>
                    </div> -->
                </div>            
                <div class="row" id="status2" style="height:16%;">
                    <!-- <div class="col-md-6 col-sm-6 col-lg-3">
                        <div class="card-box widget-box-1 bg-white">
                            <h2 class="text-primary text-center">$<span data-plugin="counterup">5623</span></h2>
                        </div>
                    </div> -->
                </div>
                <div class="row" style="height:3%;"></div>
                <div class="row" id="status3" style="height:20%;">
                </div>
                <div class="row" style="height:3%;"></div>
                <div class="row" id="status4" style="height:20%;">
                </div>
                <div class="row" style="height:3%;"></div>
                <div class="row" id="status5" style="height:20%;">
                </div>
            </div>

        </div> <!-- container -->
    </div> <!-- content -->
</div> <!-- content-page -->
</div>
<!-- END wrapper -->
<script type="text/javascript">

    function displayStatus(data)
    {
        var height = window.innerHeight - 220;
        document.getElementById("washers_status").setAttribute("style", "height:"+height + "px;");

        var imgBase="<?php echo site_url('assets/images/basket_')?>"

        //row2
        var totalCnt = data.length;
        var elesInRow = totalCnt;
        var index = 0;
        if(elesInRow > 12)elesInRow = 12;

        var cellHeight = height / 7;
        var strContent = "";
        for(i=0; i <elesInRow; i++)
        {
            var imgUrl = imgBase + data[i+index].weight + ".png";
            var strEle = '<div style="padding-left: 10px;padding-right: 10px;float: left;width:8.3%;">\n'+
                '<div style="background-color:darkgrey; text-align:center; width:100%; height:' + cellHeight +'px;">'
                +'<img style="height:' + cellHeight/2 +'px;" src="' + imgUrl + '"/>';
            if(data[i+index].busy)
                strEle +='<h2 class="text-danger text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            else 
                strEle +='<h2 class="text-dark text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            strEle += '</div>\n</div>\n';
            strContent += strEle;            
        }
        document.getElementById("status2").innerHTML = strContent;

        totalCnt -=elesInRow;
        index += elesInRow;
        
        //row1
        if(totalCnt > 27) elesInRow = totalCnt - 27;
        else elesInRow = 0;
        strContent = "";
        for(i=0; i <elesInRow; i++)
        {
            var imgUrl = imgBase + data[i+index].weight + ".png";
            var strEle = '<div style="padding-left: 10px;padding-right: 10px;float: right;width:8.3%;">\n'+
                '<div style="background-color:darkgrey; text-align:center; width:100%; height:' + cellHeight +'px;">'
                +'<img style="height:' + cellHeight/2 +'px;" src="' + imgUrl + '"/>';
            if(data[i+index].busy)
                strEle +='<h2 class="text-danger text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            else 
                strEle +='<h2 class="text-dark text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            strEle += '</div>\n</div>\n';
            strContent += strEle;            
        }
        document.getElementById("status1").innerHTML = strContent;
        totalCnt -=elesInRow;
        index += elesInRow;

        //row3
        cellHeight = height / 6;
        if(totalCnt > 9) elesInRow = 9;
        else elesInRow = totalCnt;
        strContent = "";
        for(i=0; i <elesInRow; i++)
        {
            var imgUrl = imgBase + data[i+index].weight + ".png";
            var strEle = '<div style="padding-left: 10px;padding-right: 10px;float: left;width:11%;">\n'+
                '<div style="background-color:darkgrey; text-align:center; width:100%; height:' + cellHeight +'px;">'
                +'<img style="height:' + cellHeight/2 +'px;" src="' + imgUrl + '"/>';
            if(data[i+index].busy)
                strEle +='<h2 class="text-danger text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            else 
                strEle +='<h2 class="text-dark text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            strEle += '</div>\n</div>\n';
            strContent += strEle;            
        }
        document.getElementById("status3").innerHTML = strContent;
        totalCnt -=elesInRow;
        index += elesInRow;                

        //row4
        cellHeight = height / 6;
        if(totalCnt > 9) elesInRow = 9;
        else elesInRow = totalCnt;
        strContent = "";
        for(i=0; i <elesInRow; i++)
        {
            var imgUrl = imgBase + data[i+index].weight + ".png";
            var strEle = '<div style="padding-left: 10px;padding-right: 10px;float: left;width:11%;">\n'+
                '<div style="background-color:darkgrey; text-align:center; width:100%; height:' + cellHeight +'px;">'
                +'<img style="height:' + cellHeight/2 +'px;" src="' + imgUrl + '"/>';
            if(data[i+index].busy)
                strEle +='<h2 class="text-danger text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            else 
                strEle +='<h2 class="text-dark text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            strEle += '</div>\n</div>\n';
            strContent += strEle;            
        }
        document.getElementById("status4").innerHTML = strContent;
        totalCnt -=elesInRow;
        index += elesInRow;  

        //row5
        cellHeight = height / 6;
        if(totalCnt > 9) elesInRow = 9;
        else elesInRow = totalCnt;
        strContent = "";
        for(i=0; i <elesInRow; i++)
        {
            var imgUrl = imgBase + data[i+index].weight + ".png";
            var strEle = '<div style="padding-left: 10px;padding-right: 10px;float: left;width:11%;">\n'+
                '<div style="background-color:darkgrey; text-align:center; width:100%; height:' + cellHeight +'px;">'
                +'<img style="height:' + cellHeight/2 +'px;" src="' + imgUrl + '"/>';
            if(data[i+index].busy)
                strEle +='<h2 class="text-danger text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            else 
                strEle +='<h2 class="text-dark text-center" style ="font-weight:600;">' + data[i+index].id + '</h2>\n';
            strEle += '</div>\n</div>\n';
            strContent += strEle;            
        }
        document.getElementById("status5").innerHTML = strContent;
        totalCnt -=elesInRow;
        index += elesInRow;          

    }

    var busyTimer = false;
    function timerFunc()
    {
        if(busyTimer==true)
            return;
        busyTimer = true;

        var strContent = "";
        $.ajax({
            url : "<?php echo site_url('Cms_api/washer_states')?>",
            data: {},
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {                 
                busyTimer = false;
                if(data!=null)
                    displayStatus(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                busyTimer = false;
            }
        });
    }

    var timer = setInterval(timerFunc, 2000);    
</script>