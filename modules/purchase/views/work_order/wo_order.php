<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php
      echo form_open_multipart($this->uri->uri_string(), array('id' => 'wo_order-form', 'class' => '_transaction_form'));
      if (isset($wo_order)) {
        echo form_hidden('isedit');
      }
      ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
          <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top">
              <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
              <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
              <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                    <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                      <?php echo _l('pur_general_infor'); ?>
                    </a>
                  </li>
                  <?php
                  $customer_custom_fields = false;
                  if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'pur_order', 'active' => 1)) > 0) {
                    $customer_custom_fields = true;
                  ?>

                  <?php } ?>

                  <li role="presentation" class="">
                    <a href="#shipping_infor" aria-controls="shipping_infor" role="tab" data-toggle="tab">
                      <?php echo _l('pur_shipping_infor'); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                  <?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <?php $wo_order_name = (isset($wo_order) ? $wo_order->wo_order_name : '');
                        echo render_input('wo_order_name', 'wo_order_description', $wo_order_name); ?>

                      </div>
                      <div class="col-md-6 form-group">
                        <?php $prefix = get_purchase_option('wo_order_prefix');
                        $next_number = get_purchase_option('next_wo_number');

                        $wo_order_number = (isset($wo_order) ? $wo_order->wo_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . '-' . date('M-Y'));
                        if (get_option('wo_only_prefix_and_number') == 1) {
                          $wo_order_number = (isset($wo_order) ? $wo_order->wo_order_number : $prefix . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT));
                        }


                        $number = (isset($wo_order) ? $wo_order->number : $next_number);
                        echo form_hidden('number', $number); ?>

                        <label for="wo_order_number"><?php echo _l('wo_order_number'); ?></label>

                        <input type="text" readonly class="form-control" name="wo_order_number" value="<?php echo pur_html_entity_decode($wo_order_number); ?>">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">

                        <label for="vendor"><?php echo _l('vendor'); ?></label>
                        <select name="vendor" id="vendor" class="selectpicker" <?php if (isset($wo_order)) {
                                                                                  echo 'disabled';
                                                                                } ?> onchange="estimate_by_vendor(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($vendors as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($wo_order) && $wo_order->vendor == $s['userid']) {
                                                                                                  echo 'selected';
                                                                                                } else {
                                                                                                  if (isset($ven) && $ven == $s['userid']) {
                                                                                                    echo 'selected';
                                                                                                  }
                                                                                                } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                          <?php } ?>
                        </select>

                      </div>

                      <?php
                      if ($convert_po && $selected_pr && $selected_project) {
                        $wo_order['pur_request'] = $selected_pr;
                        $wo_order['project'] = $selected_project;
                        $wo_order = (object) $wo_order;
                      }
                      ?>
                      <?php
                      $project_id = '';
                      if ($this->input->get('project')) {
                        $project_id = $this->input->get('project');
                      }
                      ?>
                      <div class="col-md-6 form-group">
                        <input type="hidden" name="project" id="project_val" value="">
                        <label for="project"><?php echo _l('project'); ?></label>
                        <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($projects as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['id']); ?>" <?php if (isset($wo_order) && $s['id'] == $wo_order->project) {
                                                                                              echo 'selected';
                                                                                            } else if (!isset($wo_order) && $s['id'] == $project_id) {
                                                                                              echo 'selected';
                                                                                            } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                    </div>



                    <div class="row">


                      <div class="col-md-6 form-group">
                        <label for="type"><?php echo _l('type'); ?></label>
                        <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="capex" <?php if (isset($wo_order) && $wo_order->type == 'capex') {
                                                  echo 'selected';
                                                } ?>><?php echo _l('capex'); ?></option>
                          <option value="opex" <?php if (isset($wo_order) && $wo_order->type == 'opex') {
                                                  echo 'selected';
                                                } ?>><?php echo _l('opex'); ?></option>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="type"><?php echo _l('Payment Terms'); ?></label>
                        <select name="payment_days" id="payment_days" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="8" <?php if (isset($wo_order) && $wo_order->payment_days == 8) {
                                              echo 'selected';
                                            } ?>>Advance Payment</option>
                          <option value="8" <?php if (isset($wo_order) && $wo_order->payment_days == 9) {
                                              echo 'selected';
                                            } ?>>Immediate after delievery</option>
                          <option value="1" <?php if (isset($wo_order) && $wo_order->payment_days == 1) {
                                              echo 'selected';
                                            } ?>>7 days after delivery</option>
                          <option value="2" <?php if (isset($wo_order) && $wo_order->payment_days == 2) {
                                              echo 'selected';
                                            } ?>>15 days after delivery</option>
                          <option value="3" <?php if (isset($wo_order) && $wo_order->payment_days == 3) {
                                              echo 'selected';
                                            } ?>>30 days after delivery</option>
                          <option value="4" <?php if (isset($wo_order) && $wo_order->payment_days == 4) {
                                              echo 'selected';
                                            } ?>>45 days after delivery</option>
                          <option value="5" <?php if (isset($wo_order) && $wo_order->payment_days == 5) {
                                              echo 'selected';
                                            } ?>>60 days after delivery</option>
                          <option value="6" <?php if (isset($wo_order) && $wo_order->payment_days == 6) {
                                              echo 'selected';
                                            } ?>>75 days after delivery</option>
                          <option value="7" <?php if (isset($wo_order) && $wo_order->payment_days == 7) {
                                              echo 'selected';
                                            } ?>>90 days after delivery</option>
                        </select>
                      </div>



                    </div>

                    <div class="row">
                      <div class="col-md-12 form-group">
                        <div id="inputTagsWrapper">
                          <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                          <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($wo_order) ? prep_tags_input(get_tags_in($wo_order->id, 'pur_order')) : ''); ?>" data-role="tagsinput">
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6 ">
                        <?php
                        $currency_attr = array('disabled' => true, 'data-show-subtext' => true);

                        $selected = '';
                        foreach ($currencies as $currency) {
                          if (isset($wo_order) && $wo_order->currency != 0) {
                            if ($currency['id'] == $wo_order->currency) {
                              $selected = $currency['id'];
                            }
                          } else {
                            if ($currency['isdefault'] == 1) {
                              $selected = $currency['id'];
                            }
                          }
                        }

                        ?>
                        <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                      </div>
                      <?php $clients_ed = (isset($wo_order) ? explode(',', $wo_order->clients ?? '') : []); ?>
                      <div class="col-md-6 form-group select-placeholder">
                        <label for="clients" class="control-label"><?php echo _l('clients'); ?></label>
                        <select id="clients" name="clients[]" data-live-search="true" onchange="client_change(this); return false;" multiple data-width="100%" class="ajax-search client-ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                          <?php
                          foreach ($clients_ed as $client_id) {
                            $selected = (is_numeric($client_id) ? $client_id : '');
                            if ($selected != '') {
                              $rel_data = get_relation_data('customer', $selected);
                              $rel_val = get_relation_values($rel_data, 'customer');
                              echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>


                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <?php $order_date = (isset($wo_order) ? _d($wo_order->order_date) : _d(date('Y-m-d')));
                        echo render_date_input('order_date', 'order_date', $order_date); ?>
                      </div>
                      <div class="col-md-6">
                        <?php
                        $selected = (isset($wo_order) ? $wo_order->wo_type : '');
                        $wo_type = [
                          ['id' => '1', 'name' => 'From Client'],
                          ['id' => '2', 'name' => 'For Vendor'],
                        ];
                        echo render_select('wo_type', $wo_type, ['id', 'name'], 'Wo Order Type', $selected); ?>
                      </div>


                    </div>




                    <div class="row">


                      <div class="col-md-12 ">
                        <div class="form-group select-placeholder">
                          <label for="discount_type"
                            class="control-label"><?php echo _l('discount_type'); ?></label>
                          <select name="discount_type" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                            <option value="before_tax" <?php
                                                        if (isset($wo_order)) {
                                                          if ($wo_order->discount_type == 'before_tax') {
                                                            echo 'selected';
                                                          }
                                                        } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                            <option value="after_tax" <?php if (isset($wo_order)) {
                                                        if ($wo_order->discount_type == 'after_tax' || $wo_order->discount_type == null) {
                                                          echo 'selected';
                                                        }
                                                      } else {
                                                        echo 'selected';
                                                      } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <?php if ($customer_custom_fields) { ?>

                  <?php $rel_id = (isset($wo_order) ? $wo_order->id : false); ?>
                  <?php echo render_custom_fields('pur_order', $rel_id); ?>

                <?php } ?>
              </div>

              <div role="tabpanel" class="tab-pane" id="shipping_infor">
                <div class="row">
                  <div class="col-md-6">
                    <?php $shipping_address = isset($wo_order) ? $wo_order->shipping_address : get_option('pur_company_address');
                    if ($shipping_address == '') {
                      $shipping_address = get_option('pur_company_address');
                    }

                    echo render_textarea('shipping_address', 'pur_company_address', $shipping_address, ['rows' => 7]); ?>

                    <?php $shipping_zip = isset($wo_order) ? $wo_order->shipping_zip : get_option('pur_company_zipcode');
                    if ($shipping_zip == '') {
                      $shipping_zip = get_option('pur_company_zipcode');
                    }
                    echo render_input('shipping_zip', 'pur_company_zipcode', $shipping_zip, 'text'); ?>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12">
                        <?php $shipping_city = isset($wo_order) ? $wo_order->shipping_city : get_option('pur_company_zipcode');
                        if ($shipping_city == '') {
                          $shipping_city = get_option('pur_company_city');
                        }
                        echo render_input('shipping_city', 'pur_company_city', $shipping_city, 'text'); ?>
                      </div>
                      <div class="col-md-12">
                        <?php $shipping_state = isset($wo_order) ? $wo_order->shipping_state : get_option('pur_company_state');
                        if ($shipping_state == '') {
                          $shipping_state = get_option('pur_company_state');
                        }
                        echo render_input('shipping_state', 'pur_company_state', $shipping_state, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $shipping_country_text = isset($wo_order) ? $wo_order->shipping_country_text : get_option('pur_company_country_text');
                        if ($shipping_country_text == '') {
                          $shipping_country_text = get_option('pur_company_country_text');
                        }
                        echo render_input('shipping_country_text', 'pur_company_country_text', $shipping_country_text, 'text'); ?>
                      </div>

                      <div class="col-md-12">
                        <?php $countries = get_all_countries();
                        $pur_company_country_code = get_option('pur_company_country_code');
                        $selected = isset($wo_order) ? $wo_order->shipping_country : $pur_company_country_code;
                        if ($selected == '') {
                          $selected = $pur_company_country_code;
                        }

                        echo render_select('shipping_country', $countries, array('country_id', array('short_name')), 'pur_company_country_code', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>

                      </div>
                    </div>
                  </div>
                </div>

              </div>


            </div>
          </div>

          <div class="panel-body">
            <label for="attachment"><?php echo _l('attachment'); ?></label>
            <div class="attachments">
              <div class="attachment">
                <div class="col-md-5 form-group" style="padding-left: 0px;">
                  <div class="input-group">
                    <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                    <span class="input-group-btn">
                      <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <br /> <br />

            <?php
            if (isset($attachments) && count($attachments) > 0) {
              foreach ($attachments as $value) {
                echo '<div class="col-md-3">';
                $path = get_upload_path_by_type('purchase') . 'pur_order/' . $value['rel_id'] . '/' . $value['file_name'];
                $is_image = is_image($path);
                if ($is_image) {
                  echo '<div class="preview_image">';
                }
            ?>
                <a href="<?php echo site_url('download/file/purchase/' . $value['id']); ?>" class="display-block mbot5" <?php if ($is_image) { ?> data-lightbox="attachment-purchase-<?php echo $value['rel_id']; ?>" <?php } ?>>
                  <i class="<?php echo get_mime_class($value['filetype']); ?>"></i> <?php echo $value['file_name']; ?>
                  <?php if ($is_image) { ?>
                    <img class="mtop5" src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $value['filetype']); ?>" style="height: 165px;">
                  <?php } ?>
                </a>
                <?php if ($is_image) {
                  echo '</div>';
                  echo '<a href="' . admin_url('purchase/delete_attachment/' . $value['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                } ?>
            <?php echo '</div>';
              }
            } ?>
          </div>

          <div class="panel-body mtop10 invoice-item">

            <div class="row">
              <div class="col-md-4">
                <?php $this->load->view('purchase/item_include/main_item_select'); ?>
              </div>
              <?php
              $po_currency = $base_currency;
              if (isset($wo_order) && $wo_order->currency != 0) {
                $po_currency = pur_get_currency_by_id($wo_order->currency);
              }

              $from_currency = (isset($wo_order) && $wo_order->from_currency != null) ? $wo_order->from_currency : $base_currency->id;
              echo form_hidden('from_currency', $from_currency);

              ?>
              <div class="col-md-8 <?php if ($po_currency->id == $base_currency->id) {
                                      echo 'hide';
                                    } ?>" id="currency_rate_div">
                <div class="col-md-10 text-right">

                  <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' (' . $base_currency->name . ' => ' . $po_currency->name . '): ';  ?></span></p>
                </div>
                <div class="col-md-2 pull-right">
                  <?php $currency_rate = 1;
                  if (isset($wo_order) && $wo_order->currency != 0) {
                    $currency_rate = pur_get_currency_rate($base_currency->name, $po_currency->name);
                  }
                  echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right');
                  ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive s_table ">
                  <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
                        <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
                        <th width="7%" align="right"><?php echo _l('Make List'); ?></th>
                        <th width="10%" align="right"><?php echo _l('Free Issue'); ?></th>
                        <th width="15%" align="left"><?php echo _l('Budgeted Amount'); ?></th>
                        <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
                        <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                        <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '(' . $po_currency->name . ')'; ?></span></th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $wo_order_row_template; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-md-8 col-md-offset-4">
                <table class="table text-right">
                  <tbody>
                    <tr id="subtotal">
                      <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
                        <?php echo form_hidden('total_mn', ''); ?>
                      </td>
                      <td class="wh-subtotal">
                      </td>
                    </tr>

                    <tr id="order_discount_percent">
                      <td>
                        <div class="row">
                          <div class="col-md-7">
                            <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>"></i></span>
                          </div>
                          <div class="col-md-3">
                            <?php $discount_total = isset($wo_order) ? $wo_order->discount_total : '';
                            echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
                          </div>
                          <div class="col-md-2">
                            <select name="add_discount_type" id="add_discount_type" class="selectpicker" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value="percent">%</option>
                              <option value="amount" selected><?php echo _l('amount'); ?></option>
                            </select>
                          </div>
                        </div>
                      </td>
                      <td class="order_discount_value">

                      </td>
                    </tr>

                    <tr id="total_discount">
                      <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
                        <?php echo form_hidden('dc_total', ''); ?>
                      </td>
                      <td class="wh-total_discount">
                      </td>
                    </tr>

                    <tr>
                      <td>
                        <div class="row">
                          <div class="col-md-9">
                            <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
                          </div>
                          <div class="col-md-3">
                            <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if (isset($wo_order)) {
                                                                                                                  echo $wo_order->shipping_fee;
                                                                                                                } else {
                                                                                                                  echo '0';
                                                                                                                } ?>" class="form-control pull-left text-right" name="shipping_fee">
                          </div>
                        </div>
                      </td>
                      <td class="shiping_fee">
                      </td>
                    </tr>

                    <tr id="totalmoney">
                      <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
                        <?php echo form_hidden('grand_total', ''); ?>
                      </td>
                      <td class="wh-total">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id="removed-items"></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mtop15">
              <div class="panel-body bottom-transaction">
                <?php $value = (isset($wo_order) ? $wo_order->vendornote : get_purchase_option('vendor_note')); ?>
                <?php echo render_textarea('vendornote', 'estimate_add_edit_vendor_note', $value, array(), array(), 'mtop15'); ?>
                <!-- <?php $value = (isset($wo_order) ? $wo_order->terms :  get_purchase_option('terms_and_conditions')); ?>
                <?php echo render_textarea('terms', 'terms_and_conditions', $value, array(), array(), 'mtop15', 'tinymce'); ?> -->
                <?php $value = (isset($wo_order) ? $wo_order->order_summary : get_purchase_option('order_summary'));
                $day = date("j");
                $month = date("F");
                $year = date("Y");
                function getOrdinalSuffix($day)
                {
                  if ($day > 3 && $day < 21) return $day . "th";
                  switch ($day % 10) {
                    case 1:
                      return $day . "st";
                    case 2:
                      return $day . "nd";
                    case 3:
                      return $day . "rd";
                    default:
                      return $day . "th";
                  }
                }
                $formatted_date = getOrdinalSuffix($day) . " " . $month . " " . $year;
                if (!isset($wo_order) && $wo_order->order_summary == '') {
                  $value = get_by_deafult_order_summary();
                }

                ?>
                <?php echo render_textarea('order_summary', 'terms_and_conditions', $value, array(), array(), 'mtop15', 'tinymce'); ?>
                <div id="vendor_data">

                </div>

                <div class="btn-bottom-toolbar text-right">

                  <button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
                    <?php echo _l('submit'); ?>
                  </button>
                </div>
              </div>
              <div class="btn-bottom-pusher"></div>
            </div>
          </div>
        </div>

      </div>
      <?php echo form_close(); ?>

    </div>
  </div>
</div>
</div>
<?php init_tail(); ?>
</body>

</html>

<script type="text/javascript">
  var convert_po = '<?php echo $convert_po; ?>';
  if (convert_po) {
    $('#project').attr('disabled', true);
    $('#pur_request').attr('disabled', true);
    $('#project_val').css('display', 'block');
    $('#project_val').val($('#project').val());
  } else {
    $('#project').attr('disabled', false);
    $('#pur_request').attr('disabled', false);
    $('#project_val').remove();
  }


  var pur_request = $('select[name="pur_request"]').val();
  var vendor = $('select[name="vendor"]').val();
  if (pur_request != '') {
    $.post(admin_url + 'purchase/coppy_pur_request_for_po/' + pur_request + '/' + vendor).done(function(response) {
      response = JSON.parse(response);
      if (response) {
        $('select[name="estimate"]').html(response.estimate_html);
        $('select[name="estimate"]').selectpicker('refresh');

        $('select[name="currency"]').val(response.currency).change();
        $('input[name="currency_rate"]').val(response.currency_rate).change();

        // $('.invoice-item table.invoice-items-table.items tbody').html('');
        // $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

        setTimeout(function() {
          pur_calculate_total();
        }, 15);

        init_selectpicker();
        pur_reorder_items('.invoice-item');
        pur_clear_item_preview_values('.invoice-item');
        $('body').find('#items-warning').remove();
        $("body").find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');
      }
    });
  }
</script>

<?php require 'modules/purchase/assets/js/wo_order_js.php'; ?>