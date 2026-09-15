<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-3 form-group">
				<label for="project"><?php echo _l('project'); ?></label>
				<select name="project[]" id="project" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
					<option value=""></option>
					<?php foreach ($projects as $pj) { ?>
						<option value="<?php echo pur_html_entity_decode($pj['id']); ?>"><?php echo pur_html_entity_decode($pj['name']); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="panel_s mbot10">

				<div class="row">

					<div class="col-md-12" id="small-table">
						<div class="panel_s">
							<div id="select_project_msg" class="alert alert-info text-center">
								<strong>Please select a project to show items.</strong>
							</div>

							<div class="panel-body" id="table_container" style="display:none;">
								<?php $table_data = array(
									_l('Work Order'),
									_l('Item'),
									_l('Project'),
									_l('Order Date'),
									_l('Options'),
								);
								render_datatable($table_data, 'table_boq_items'); ?>

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
		$(function() {

			var table = $('.table-table_boq_items');
			var tableInitialized = false;

			var Params = {
				"project": "[name='project[]']",
			};

			$('select[name="project[]"]').on('changed.bs.select change', function() {

				var projects = $(this).val();

				if (projects && projects.length > 0) {

					$('#select_project_msg').hide();
					$('#table_container').show();

					if (!tableInitialized) {

						initDataTable(
							table,
							admin_url + 'purchase/table_boq_items',
							[],
							[],
							Params,
							[3, 'desc']
						);

						tableInitialized = true;

					} else {

						table.DataTable().ajax.reload();

					}

				} else {

					$('#table_container').hide();
					$('#select_project_msg').show();

				}

			});

		});
	</script>
	</body>

	</html>