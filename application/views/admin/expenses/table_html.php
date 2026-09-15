<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$hasPermission = staff_can('edit', 'expenses') || staff_can('edit', 'expenses');
if ($withBulkActions === true && $hasPermission) { ?>
  <!-- <a href="#" data-toggle="modal" data-target="#expenses_bulk_actions" class="hide bulk-actions-btn table-btn"
    data-table=".table-expenses">
    <?php echo _l('bulk_actions'); ?>
  </a> -->
<?php } ?>
<div class="row all_ot_filters">
  <?php
  $module_name = 'expenses';
  $expense_category_filter = get_module_filter($module_name, 'expense_category');
  $expense_category_filter_val = !empty($expense_category_filter) ? explode(",", $expense_category_filter->filter_value) : [];
  ?>
  <div class="col-md-3 form-group">
    <label for="expense_category"><?php echo _l('expense_category'); ?></label>
    <select name="expense_category[]" id="expense_category" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
      <?php foreach ($categories as $s) { ?>
        <option value="<?php echo pur_html_entity_decode($s['id']); ?>"
          <?php if (in_array($s['id'], $expense_category_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($s['name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>
  <?php
  $payment_mode_filter = get_module_filter($module_name, 'payment_mode');
  $payment_mode_filter_val = !empty($payment_mode_filter) ? explode(",", $payment_mode_filter->filter_value) : [];
  ?>

  <div class="col-md-3 form-group">
    <label for="payment_mode"><?php echo _l('payment_mode'); ?></label>
    <select name="payment_mode[]" id="payment_mode" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
      <?php foreach ($payment_modes as $mode) { ?>
        <option value="<?php echo pur_html_entity_decode($mode['id']); ?>"
          <?php if (in_array($mode['id'], $payment_mode_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($mode['name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <?php
  $project_filter = get_module_filter($module_name, 'project');
  $project_filter_val = !empty($project_filter) ? explode(",", $project_filter->filter_value) : [];
  ?>
  <div class="col-md-3 form-group">
    <label for="project"><?php echo _l('Project'); ?></label>
    <select name="project[]" id="project" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" data-actions-box="true">
      <?php foreach ($p_list as $p) { ?>
        <option value="<?php echo pur_html_entity_decode($p['id']); ?>"
          <?php if (in_array($p['id'], $project_filter_val)) {
            echo 'selected';
          } ?>>
          <?php echo pur_html_entity_decode($p['name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="col-md-3 form-group" id="report-time">
    <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
    <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
      <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
      <option value="this_month"><?php echo _l('this_month'); ?></option>
      <option value="1"><?php echo _l('last_month'); ?></option>
      <option value="this_year"><?php echo _l('this_year'); ?></option>
      <option value="last_year"><?php echo _l('last_year'); ?></option>
      <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
      <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
      <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
      <option value="custom"><?php echo _l('period_datepicker'); ?></option>
    </select>
  </div>
  <div id="date-range" class="hide mbot15">
    <div class="col-md-2 form-group">
      <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
      <div class="input-group date">
        <input type="text" class="form-control datepicker" id="report-from" name="report-from">
        <div class="input-group-addon">
          <i class="fa fa-calendar calendar-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-md-2 form-group">
      <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
      <div class="input-group date">
        <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
        <div class="input-group-addon">
          <i class="fa fa-calendar calendar-icon"></i>
        </div>
      </div>
    </div>
  </div>
  <?php $current_year = date('Y');
  $y0 = (int)$current_year;
  $y1 = (int)$current_year - 1;
  $y2 = (int)$current_year - 2;
  $y3 = (int)$current_year - 3;
  ?>
  <div class="form-group hide" id="year_requisition">
    <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
    <select name="year_requisition" id="year_requisition" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by') . ' ' . _l('year'); ?>">
      <option value="<?php echo pur_html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year') . ' ' . pur_html_entity_decode($y0); ?></option>
      <option value="<?php echo pur_html_entity_decode($y1); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y1); ?></option>
      <option value="<?php echo pur_html_entity_decode($y2); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y2); ?></option>
      <option value="<?php echo pur_html_entity_decode($y3); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y3); ?></option>
    </select>
  </div>

  <div class="col-md-3 form-group">
    <?php
    $wo_item_filter = get_module_filter($module_name, 'wo_item');
    $wo_item_filter_val = !empty($wo_item_filter) ? $wo_item_filter->filter_value : '';
    $wo_item = [
      ['id' => 1, 'name' => _l('Yes')],
      ['id' => 2, 'name' => _l('No')]
    ];
    ?>
    <select name="wo_item" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('wo_item?'); ?>" data-actions-box="true">
      <option value=""></option>
      <option value="1" <?php echo ($wo_item_filter_val == 1) ? 'selected' : ''; ?>><?php echo _l('Yes'); ?></option>
      <option value="2" <?php echo ($wo_item_filter_val == 2) ? 'selected' : ''; ?>><?php echo _l('No'); ?></option>
    </select>
  </div>
  <div class="col-md-12 form-group ">
    <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters pull-right">
      <?php echo _l('reset_filter'); ?>
    </a>
  </div>
</div>
<table class="table table-expenses table-striped table-bordered dt-responsive" id="<?php echo $table_id ?? 'expenses'; ?>">
  <thead>
    <tr>
      <?php if (!isset($project)) { ?>
        <th class="<?php echo ($withBulkActions === true && $hasPermission) ? '' : 'not_visible'; ?>">
          <div class="checkbox mass_select_all_wrap">
            <input type="checkbox" id="mass_select_all" data-to-table="expenses">
            <label></label>
          </div>
        </th>
      <?php } ?>

      <th><?php echo _l('the_number_sign'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_category'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_vendor'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_amount'); ?></th>
      <th><?php echo _l('expense_name'); ?></th>
      <th><?php echo _l('receipt'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_date'); ?></th>

      <?php if (!isset($project)) { ?>
        <th><?php echo _l('project'); ?></th>
        <th class="<?php echo isset($client) ? 'not_visible' : ''; ?>">
          <?php echo _l('expense_dt_table_heading_customer'); ?>
        </th>
      <?php } ?>

      <th><?php echo _l('invoice'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_reference_no'); ?></th>
      <th><?php echo _l('expense_dt_table_heading_payment_mode'); ?></th>

      <?php
      $custom_fields = get_custom_fields('expenses', ['show_on_table' => 1]);
      foreach ($custom_fields as $field) { ?>
        <th data-type="<?php echo $field['type']; ?>" data-custom-field="1">
          <?php echo $field['name']; ?>
        </th>
      <?php } ?>
    </tr>
  </thead>

  <tbody>
    <!-- DataTables Ajax will populate rows -->
  </tbody>

  <tfoot>
    <?php if (!isset($project)) { ?>
      <td></td>
    <?php } ?>

    <td></td>
    <td></td>
    <td></td>
    <td class="total_amount"></td>
    <td></td>
    <td></td>
    <td></td>

    <?php if (!isset($project)) { ?>
      <td></td>
      <td></td>
    <?php } ?>

    <td></td>
    <td></td>
    <td></td>

    <?php foreach ($custom_fields as $field) { ?>
      <td></td>
    <?php } ?>

  </tfoot>
</table>
<?php
echo $this->view('admin/expenses/_bulk_actions_modal');
