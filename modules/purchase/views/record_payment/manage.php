<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="_buttons col-md-3">
                     <?php if (has_permission('record_payment', '', 'create') || is_admin()) { ?>
                        <a href="<?php echo admin_url('purchase/add_record_payment'); ?>" class="btn btn-info pull-left mright10 display-block">
                           <?php echo _l('new_work_order'); ?>
                        </a>
                     <?php } ?>

                  </div>

                  <!-- <div class="_buttons col-md-1 pull-right">
                     <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="toggle_small_pur_order_view('.table-table_pur_order','#pur_order'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                  </div> -->
               </div>

            </div>
         </div>
         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
                     <?php echo form_hidden('payment_id', $payment_id); ?>
                     <?php $table_data = array(
                        _l('Payment Code'),
                        _l('vendor'),
                        _l('Staff'),
                        _l('Payment Date'),
                        _l('Amount'),
                        _l('Payment Category'),
                        _l('payment_mode'),
                        _l('Payment Type'),
                        _l('Type Of Payment'),
                        _l('Invoice'),
                        _l('Remarks'),
                        _l('Created On'),
                        _l('Updated On'),
                     );
                     render_datatable($table_data, 'table_payment_record'); ?>

                  </div>
               </div>
            </div>

            <div class="col-md-7 small-table-right-col">
               <div id="wo_order" class="hide">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>



<?php init_tail(); ?>
</body>

</html>
<script>
   var hidden_columns = [],
      table_rec_campaign;

   (function($) {
      "use strict";
      table_rec_campaign = $('.table-table_payment_record');

      var Params = {

      };

      initDataTable('.table-table_payment_record', admin_url + 'purchase/table_payment_record', [], [], Params, [10, 'desc']);



   })(jQuery);


</script>