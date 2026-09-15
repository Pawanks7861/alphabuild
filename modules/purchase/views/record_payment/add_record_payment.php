<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php
      echo form_open_multipart($this->uri->uri_string(), array('id' => 'add_record_payment-form', 'class' => '_transaction_form'));
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
                </ul>
              </div>
            </div>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">



                  <div class="col-md-6">

                    <div class="row">
                      <div class="form-group col-md-6">
                        <?php
                        $last_payment_code = get_last_payment_code();

                        // Extract numeric part (e.g. EXP-0009 -> 9)
                        $last_number = 0;

                        if (!empty($last_payment_code)) {
                          $last_number = (int) str_replace('PAY-', '', $last_payment_code);
                        }

                        $next_expense_code = 'PAY-' . str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);

                        $payment_code = isset($payment) ? $payment->payment_code : $next_expense_code;

                        echo render_input('payment_code', 'Payment Code', $payment_code, 'text', ['readonly' => true]);
                        ?>
                      </div>
                      <div class="form-group col-md-6">

                        <label for="vendor"><?php echo _l('vendor'); ?></label>
                        <select name="vendor" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($vendors as $s) { ?>
                            <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if (isset($payment) && $payment->vendor == $s['userid']) {
                                                                                                  echo 'selected';
                                                                                                }  ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
                          <?php } ?>
                        </select>

                      </div>

                      <div class="col-md-6 mbot10 form-group" style="clear: both;">
                        <?php
                        $selected = isset($payment) ? $payment->staff_id : '';

                        echo render_select('staff_id', $staff, array('staffid', array('firstname', 'lastname')), 'Satff', $selected);
                        ?>
                      </div>


                      <div class="col-md-6 form-group">
                        <?php
                        $selected = isset($payment) ? $payment->payment_category : '';
                        $payment_category = [
                          ['id' => '1', 'name' => 'Vendor'],
                          ['id' => '2', 'name' => 'Staff'],
                          ['id' => '3', 'name' => 'Contractor'],
                        ];

                        echo render_select(
                          'payment_category',
                          $payment_category,
                          ['id', 'name'],
                          'Payment Category',
                          $selected
                        );
                        ?>
                      </div>

                      <div class="col-md-6 form-group">
                        <?php
                        $re_value = isset($payment) ? $payment->remarks : '';
                        echo render_textarea('remarks', 'Remarks', $re_value); ?>
                      </div>
                      <div class="col-md-6 form-group">
                        <?php
                        $date_value = isset($payment) ? $payment->payment_date : '';
                        echo render_input('payment_date', 'Payemnt Date', $date_value,'date'); ?>
                      </div>

                    </div>



                  </div>
                  <div class="col-md-6">

                    <div class="row">
                      <div class="col-md-6 form-group">
                        <?php
                        $selected = isset($payment) ? $payment->payment_mode : '';
                        $payment_mode = [
                          ['id' => '1', 'name' => 'AU Bank OD 490'],
                          ['id' => '2', 'name' => 'AU Bank Current AC'],
                          ['id' => '3', 'name' => 'AXIS Bank 1391'],
                          ['id' => '4', 'name' => 'AXIS Bank 1790'],
                          ['id' => '5', 'name' => 'KOTAK Bank 299'],
                          ['id' => '6', 'name' => 'Other'],
                        ];

                        echo render_select(
                          'payment_mode',
                          $payment_mode,
                          ['id', 'name'],
                          'Payment Mode',
                          $selected
                        );
                        ?>
                      </div>

                      <div class="col-md-6 form-group">
                        <?php
                        $selected = isset($payment) ? $payment->payment_type : '';
                        $payment_type = [
                          ['id' => '1', 'name' => 'NEFT'],
                          ['id' => '2', 'name' => 'RTGS'],
                          ['id' => '3', 'name' => 'IMPS'],
                          ['id' => '4', 'name' => 'Cash'],
                          ['id' => '5', 'name' => 'Cheque'],
                          ['id' => '6', 'name' => 'UPI'],
                        ];

                        echo render_select(
                          'payment_type',
                          $payment_type,
                          ['id', 'name'],
                          'Payment Type',
                          $selected
                        );
                        ?>
                      </div>
                      <div class="col-md-6 form-group">
                        <?php
                        $selected = isset($payment) ? $payment->type_of_payment : '';
                        $type_of_payment = [
                          ['id' => '1', 'name' => 'Advance'],
                          ['id' => '2', 'name' => 'On Account'],
                          ['id' => '3', 'name' => 'Against Reference'],

                        ];

                        echo render_select(
                          'type_of_payment',
                          $type_of_payment,
                          ['id', 'name'],
                          'Type Of Payment',
                          $selected
                        );
                        ?>
                      </div>
                      <div class="col-md-6 form-group">
                        <?php
                        $amount_value = isset($payment) ? $payment->amount : '';
                        echo render_input('amount', 'Amount', $amount_value, 'number'); ?>
                      </div>
                      <div class="col-md-6 form-group">
                        <?php
                        $selected = isset($payment) ? $payment->inv_no : '';

                        foreach ($pur_inv as &$invoice) {
                          $invoice['invoice_display'] =
                            $invoice['invoice_number'] . ' - ' . $invoice['vendor_invoice_number'];
                        }
                        unset($invoice);

                        echo render_select(
                          'inv_no',
                          $pur_inv,
                          array('id', 'invoice_display'),
                          'Invoice Number',
                          $selected
                        );
                        ?>
                      </div>
                    </div>

                    <div class="row">




                    </div>

                    <div class="row">



                    </div>

                    <div class="row">
                      <div class="col-md-12 ">

                      </div>
                    </div>
                  </div>
                </div>


              </div>




            </div>
            <button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit pull-right">
              <?php echo _l('submit'); ?>
            </button>
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

</script>

<?php require 'modules/purchase/assets/js/record_payment_js.php'; ?>