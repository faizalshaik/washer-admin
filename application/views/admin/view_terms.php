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
                    <div class="col-sm-12">
                        <h4 class="page-title">Terms of Service</h4>
                        <ol class="breadcrumb"> </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">
                            <form method="post" action="<?php echo base_url().'Cms_api/updateContent'?>" >
                                <textarea id="terms" name="content"><?php echo $terms;?></textarea>
                                <input type="hidden" name="tbl_Name" value="<?php global $MYSQL; echo $MYSQL['_termsDB']?>">
                                <div class="form-group text-right m-t-20">
                                <button class="btn btn-primary waves-effect waves-light" type="submit">
                                    Submit
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
    var $dom = {
        terms:$("#terms")
    };
    $(document).ready(function () {
        if($("#terms").length > 0){
            tinymce.init({
                selector: "textarea#terms",
                theme: "modern",
                height:460,
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | fontselect,fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
            }); 
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