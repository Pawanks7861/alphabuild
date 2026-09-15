<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   #vendor-exists-message {
      margin-top: 5px;
      font-size: 13px;
      color: #dc3545;
      display: none;
   }
</style>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php if (isset($client) && $client->registration_confirmed == 0 && is_admin()) { ?>
               <div class="alert alert-warning">
                  <?php echo _l('customer_requires_registration_confirmation'); ?>
                  <br />
                  <a href="<?php echo admin_url('purchase/confirm_registration/' . $client->userid); ?>"><?php echo _l('confirm_registration'); ?></a>
               </div>
            <?php } ?>

            <?php if (isset($client) && (!has_permission('purchase_vendors', '', 'view') && is_vendor_admin($client->userid))) { ?>
               <div class="alert alert-info">
                  <?php echo _l('customer_admin_login_as_client_message', get_staff_full_name(get_staff_user_id())); ?>
               </div>
            <?php } ?>
         </div>
         <?php if ($group == 'profile') { ?>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
               <button class="btn btn-info only-save customer-form-submiter">
                  <?php echo _l('submit'); ?>
               </button>
               <?php if (!isset($client)) { ?>
                  <button class="btn btn-info save-and-add-contact customer-form-submiter">
                     <?php echo _l('save_customer_and_add_contact'); ?>
                  </button>
               <?php } ?>
            </div>
         <?php } ?>
         <?php if (isset($client)) { ?>
            <div class="col-md-3">
               <div class="panel_s mbot5">
                  <div class="panel-body padding-10">
                     <h4 class="bold">
                        #<?php echo pur_html_entity_decode($client->userid . ' ' . $title); ?>


                     </h4>
                  </div>
               </div>
               <?php $this->load->view('vendors/tabs'); ?>
            </div>
         <?php } ?>
         <div class="col-md-<?php if (isset($client)) {
                                 echo 9;
                              } else {
                                 echo 12;
                              } ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if (isset($client)) { ?>
                     <?php echo form_hidden('isedit'); ?>
                     <?php echo form_hidden('userid', $client->userid); ?>
                     <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                        <?php $this->load->view((isset($tabs) ? $tabs['view'] : 'vendors/groups/profile')); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php if ($group == 'profile') { ?>
         <div class="btn-bottom-pusher"></div>
      <?php } ?>
   </div>
</div>
<?php init_tail(); ?>

<?php require 'modules/purchase/assets/js/vendor_js.php'; ?>

</body>

</html>
<script>
   $(document).ready(function() {

      let searchTimeout;

      $('#company').on('input', function() {

         clearTimeout(searchTimeout);

         const vendorName = $.trim($(this).val());

         $('#vendor-exists-message').hide();

         // Don't check empty or very short names
         if (vendorName.length < 2) {
            return;
         }

         // Wait 500ms after user stops typing
         searchTimeout = setTimeout(function() {

            $.ajax({
               url: admin_url + 'purchase/check_vendor_name',
               type: 'POST',
               dataType: 'json',
               data: {
                  vendor_name: vendorName,
                  csrf_token_name: $('input[name="csrf_token_name"]').val()
               },

               success: function(response) {

                  if (response.status === 'exists') {

                     $('#vendor-exists-message')
                        .text('Vendor already exists.')
                        .show();

                  } else {

                     $('#vendor-exists-message').hide();

                  }
               },

               error: function(xhr, status, error) {
                  console.error('Vendor check error:', error);
               }
            });

         }, 500);
      });

   });
</script>