<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="panel_s mbot10">
				
            <div class="row">
				<div class="col-md-12" id="small-table">
					<div class="panel_s">
						<div class="panel-body">
	                    <?php $table_data = array(
                           _l('purchase_order'),
                           _l('vendor'),
                           _l('order_date'),
                           _l('payment'),
                           _l('project'),
                           _l('department')
                           );
                       render_datatable($table_data,'table_pur_payments'); ?>
							
						</div>
					</div>
				</div>
            	
			<div class="col-md-7 small-table-right-col">
			    <div id="pur_order" class="hide">
			    </div>
			 </div>
            </div>
		</div>
	</div>
</div>




<?php init_tail(); ?>
<script>
   $(document).ready(function() {

      var table_pur_payments = $('.table-table_pur_payments');
      var Params = {};
      initDataTable(table_pur_payments, admin_url + 'purchase/table_pur_payments', [], [], Params, [1, 'desc']);
      



   });
</script>
</body>
</html>
