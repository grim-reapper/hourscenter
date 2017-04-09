<div id="page-wrapper">

       <div class="container-fluid">

           <!-- Page Heading -->
           <div class="row">
               <div class="col-lg-12">
                   <h1 class="page-header">
                       Add New Brand
                   </h1>
               </div>
           </div>
           <!-- /.row -->

           <div class="row">
               <div class="col-lg-12">
                 <?php
                if (isset($success) && strlen($success)) {
                    echo '<div class="success alert alert-success">';
                    echo '<p>' . $success . '</p>';
                    echo '</div>';
                }
                if (isset($errors) && strlen($errors)) {
                    echo '<div class="error alert alert-danger">';
                    echo '<p>' . $errors . '</p>';
                    echo '</div>';
                }
                if (validation_errors()) {
                    echo validation_errors('<div class="error">', '</div>');
                }
                ?>
                 <form id="add-brand" role="form" method="post" action="<?php echo site_url() ?>admin/brand" enctype="multipart/form-data">

                     <div class="form-group">
                         <label class="control-label" for="inputSuccess">Brand Name:</label>
                         <input type="text" class="form-control required" id="brand-name" name="brand_name" value="<?php echo set_value('brand_name', ''); ?>">
                     </div>

                     <div class="form-group">
                         <label class="control-label" for="page-heading">Page Heading:</label>
                         <input type="text" class="form-control required" id="page-heading" name="page_heading" value="<?php echo set_value('page_heading', ''); ?>">
                     </div>

                     <div class="form-group">
                         <label class="control-label" for="">Brand Logo:</label>
                         <input type="file" name="brand_logo" class="required">
                     </div>
                     <div class="form-group">
                         <input name="image_upload" type="submit" class="btn btn-primary" value="Add Brand">
                     </div>
                 </form>
               </div>
           </div>
           <!-- /.row -->

       </div>
       <!-- /.container-fluid -->

   </div>
   <!-- /#page-wrapper -->
<script>
$(function(){
  $('#add-brand').validate();
})
</script>
