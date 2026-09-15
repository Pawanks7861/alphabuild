<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <div class="_buttons">
                        <?php if (staff_can('create',  'expenses')) { ?>
                            <a href="<?php echo admin_url('expenses/expense'); ?>" class="btn btn-primary">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_expense'); ?>
                            </a>
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#addcredit" class="btn btn-primary">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('Add credit'); ?>
                            </a>
                            <!-- <a href="<?php echo admin_url('expenses/import'); ?>" class="btn btn-primary mleft5">
                                <i class="fa-solid fa-upload tw-mr-1"></i>
                                <?php echo _l('import_expenses'); ?>
                            </a> -->
                        <?php } ?>


                        <a href="#" onclick="slideToggle('#expense-chart'); return false;" class="pull-right btn btn-default mleft5 btn-with-tooltip" data-toggle="tooltip" title="Expense Chart"><i class="fa fa-pie-chart"></i></a>

                        <a href="#" onclick="slideToggle('#stats-top'); return false;"
                            class="pull-right btn btn-default mleft5 btn-with-tooltip" data-toggle="tooltip"
                            title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs"
                            onclick="toggle_small_view('.table-expenses','#expense'); return false;"
                            data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                class="fa fa-angle-double-left"></i></a>
                        <div id="stats-top" class="hide">
                            <hr />
                            <div id="expenses_total"></div>
                        </div>
                        <div id="expense-chart" class="hide mtop15">
                            <div class="col-md-3 pull-right" style="padding-right: 0px; padding-bottom: 10px;">
                                <select class="form-control" id="expenseType" name="expenseType" onchange="updateExpenseChart();">
                                    <option value="0">Category Wise</option>
                                    <option value="1">Payment Wise</option>
                                    <option value="2">Project Wise</option>
                                </select>
                            </div>
                            <div id="expense_chart" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="clearfix"></div>
                                <!-- if expenseid found in url -->
                                <?php echo form_hidden('expenseid', $expenseid); ?>
                                <div class="panel-table-full">
                                    <?php $this->load->view('admin/expenses/table_html', ['withBulkActions' => true]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 small-table-right-col">
                        <div id="expense" class="hide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="expense_convert_helper_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('additional_action_required'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="radio radio-primary">
                    <input type="radio" checked id="expense_convert_invoice_type_1" value="save_as_draft_false"
                        name="expense_convert_invoice_type">
                    <label for="expense_convert_invoice_type_1"><?php echo _l('convert'); ?></label>
                </div>
                <div class="radio radio-primary">
                    <input type="radio" id="expense_convert_invoice_type_2" value="save_as_draft_true"
                        name="expense_convert_invoice_type">
                    <label for="expense_convert_invoice_type_2"><?php echo _l('convert_and_save_as_draft'); ?></label>
                </div>
                <div id="inc_field_wrapper">
                    <hr />
                    <p><?php echo _l('expense_include_additional_data_on_convert'); ?></p>
                    <p><b><?php echo _l('expense_add_edit_description'); ?> +</b></p>
                    <div class="checkbox checkbox-primary inc_note">
                        <input type="checkbox" id="inc_note">
                        <label for="inc_note"><?php echo _l('expense'); ?>
                            <?php echo _l('expense_add_edit_note'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary inc_name">
                        <input type="checkbox" id="inc_name">
                        <label for="inc_name"><?php echo _l('expense'); ?> <?php echo _l('expense_name'); ?></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                    id="expense_confirm_convert"><?php echo _l('confirm'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="addcredit" tabindex="-1" role="dialog" aria-labelledby="addCreditLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <?php echo form_open('', [
                'id' => 'add-credit-form'
            ]); ?>

            <div class="modal-header">
                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="addCreditLabel">
                    <i class="fa-regular fa-plus"></i>
                    <?php echo _l('Add credit'); ?>
                </h4>
            </div>

            <div class="modal-body">

                <div id="add-credit-alert"></div>

                <div class="form-group">
                    <label for="credit_amount">
                        <?php echo _l('Amount'); ?>
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                        step="0.01"
                        min="0.01"
                        name="amount"
                        id="credit_amount"
                        class="form-control"
                        placeholder="Enter credit amount"
                        required>
                </div>

                <div class="form-group">
                    <label for="credit_date">
                        <?php echo _l('Date and time'); ?>
                        <span class="text-danger">*</span>
                    </label>

                    <input type="datetime-local"
                        name="credit_date"
                        id="credit_date"
                        class="form-control"
                        required>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-default"
                    data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>

                <button type="submit"
                    class="btn btn-primary"
                    id="save-credit-btn">


                    <?php echo _l('Save'); ?>

                </button>

            </div>

            <?php echo form_close(); ?>

        </div>
    </div>
</div>
<script>
    var hidden_columns = [3, 6, 9, 10, 11];
</script>
<?php init_tail(); ?>
<?php
echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts.js') . '"></script>';
echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/exporting.js') . '"></script>';
?>
<script>
    $(document).ready(function() {

        $('#add-credit-form').on('submit', function(e) {

            e.preventDefault();

            var form = $(this);
            var button = $('#save-credit-btn');

            var amount = $('#credit_amount').val();
            var creditDate = $('#credit_date').val();

            if (!amount || parseFloat(amount) <= 0) {

                alert_float(
                    'danger',
                    'Please enter a valid credit amount.'
                );

                return;
            }

            if (!creditDate) {

                alert_float(
                    'danger',
                    'Please select date and time.'
                );

                return;
            }

            button.prop('disabled', true);

            button.html(
                '<i class="fa fa-spinner fa-spin"></i> Saving...'
            );

            $.ajax({

                url: admin_url + 'credits/add_credit',

                type: 'POST',

                dataType: 'json',

                data: {
                    amount: amount,
                    credit_date: creditDate,
                    csrf_token_name: $('input[name="csrf_token_name"]').val()
                },

                success: function(response) {

                    if (response.success) {

                        alert_float(
                            'success',
                            response.message
                        );

                        $('#addcredit').modal('hide');

                        form[0].reset();

                        // Set current date/time again
                        var now = new Date();

                        var year = now.getFullYear();
                        var month = String(now.getMonth() + 1).padStart(2, '0');
                        var day = String(now.getDate()).padStart(2, '0');
                        var hours = String(now.getHours()).padStart(2, '0');
                        var mins = String(now.getMinutes()).padStart(2, '0');

                        $('#credit_date').val(
                            year + '-' +
                            month + '-' +
                            day + 'T' +
                            hours + ':' +
                            mins
                        );

                        /*
                         * If you have a DataTable, reload it here.
                         *
                         * Example:
                         *
                         * $('.table-credit').DataTable().ajax.reload(null, false);
                         */

                        if (typeof initDataTable === 'function') {
                            // Reload your DataTable here if required
                        }

                    } else {

                        alert_float(
                            'danger',
                            response.message
                        );
                    }

                },

                error: function(xhr) {

                    console.log(xhr.responseText);

                    alert_float(
                        'danger',
                        'Something went wrong while saving credit.'
                    );

                },

                complete: function() {

                    button.prop('disabled', false);

                    button.html(
                        'Save'
                    );

                }

            });

        });

    });
</script>
<script>
    $(document).ready(function() {

        var now = new Date();

        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var mins = String(now.getMinutes()).padStart(2, '0');

        $('#credit_date').val(
            year + '-' + month + '-' + day + 'T' + hours + ':' + mins
        );

    });
    Dropzone.autoDiscover = false;
    $(function() {
        // initDataTable('.table-expenses', admin_url + 'expenses/table', [0], [0], {},
        // <?php echo hooks()->apply_filters('expenses_table_default_order', json_encode([6, 'desc'])); ?>)
        // .column(1).visible(false, false).columns.adjust();
        var table_rec_task;
        var report_from_choose;
        var report_from = $('input[name="report-from"]');
        var report_to = $('input[name="report-to"]');
        var date_range = $('#date-range');
        $(function() {
            table_rec_task = $('.table-expenses');
            report_from_choose = $('#report-time');

            var Params = {
                "expense_category": "[name='expense_category[]']",
                "payment_mode": "[name='payment_mode[]']",
                "vendor": "[name='vendor[]']",
                "project": "[name='project[]']",
                "report_months": '[name="months-report"]',
                "report_from": '[name="report-from"]',
                "report_to": '[name="report-to"]',
                "year_requisition": "[name='year_requisition']",
                "wo_item": "[name='wo_item']"
            };
            initDataTable('.table-expenses', admin_url + 'expenses/table', [0], [0], Params,
                    <?php echo hooks()->apply_filters('expenses_table_default_order', json_encode([7, 'desc'])); ?>)
                .column(1).visible(false, false).columns.adjust();
            // initDataTable('.table-expenses', admin_url + 'expenses/table', [0], [0], Params,
            // [6, 'desc']);
            $.each(Params, function(i, obj) {
                $('select' + obj).on('change', function() {
                    table_rec_task.DataTable().ajax.reload();
                });
            });

            $('select[name="months-report"]').on('change', function() {
                if ($(this).val() != 'custom') {
                    table_rec_task.DataTable().ajax.reload();
                }
            });

            $('select[name="year_requisition"]').on('change', function() {
                table_rec_task.DataTable().ajax.reload();
            });

            report_from.on('change', function() {
                var val = $(this).val();
                var report_to_val = report_to.val();
                if (val != '') {
                    report_to.attr('disabled', false);
                    if (report_to_val != '') {
                        table_rec_task.DataTable().ajax.reload();
                    }
                } else {
                    report_to.attr('disabled', true);
                }
            });

            report_to.on('change', function() {
                var val = $(this).val();
                if (val != '') {
                    table_rec_task.DataTable().ajax.reload();
                }
            });

            $('select[name="months-report"]').on('change', function() {
                var val = $(this).val();
                report_to.attr('disabled', true);
                report_to.val('');
                report_from.val('');
                if (val == 'custom') {
                    date_range.addClass('fadeIn').removeClass('hide');
                    return;
                } else {
                    if (!date_range.hasClass('hide')) {
                        date_range.removeClass('fadeIn').addClass('hide');
                    }
                }
                table_rec_task.DataTable().ajax.reload();
            });

            $(document).on('click', '.reset_all_ot_filters', function() {
                var filterArea = $('.all_ot_filters');
                filterArea.find('input').val("");
                filterArea.find('select').selectpicker("val", "");
                table_rec_task.DataTable().ajax.reload();
            });
            $(document).on('change', 'select[name="expense_category[]"]', function() {
                $('select[name="expense_category[]"]').selectpicker('refresh');
            });

            $(document).on('change', 'select[name="payment_mode[]"]', function() {
                $('select[name="payment_mode[]"]').selectpicker('refresh');
            });

            $(document).on('change', 'select[name="vendor[]"]', function() {
                $('select[name="vendor[]"]').selectpicker('refresh');
            });
            $('.table-expenses').on('draw.dt', function() {
                var reportsTable = $(this).DataTable();
                var sums = reportsTable.ajax.json().sums;
                $(this).find('tfoot').addClass('bold');
                $(this).find('tfoot td').eq(0).html("Total (Per Page)");
                $(this).find('tfoot td.total_amount').html(sums.total_amount);
            });

            // var table_pur_payments = $('.table-expenses');
            // var Params = {};
            // initDataTable(table_pur_payments, admin_url + 'expenses/table', [], [], Params, [6, 'desc']);
            init_expense();

            $('#expense_convert_helper_modal').on('show.bs.modal', function() {
                var emptyNote = $('#tab_expense').attr('data-empty-note');
                var emptyName = $('#tab_expense').attr('data-empty-name');
                if (emptyNote == '1' && emptyName == '1') {
                    $('#inc_field_wrapper').addClass('hide');
                } else {
                    $('#inc_field_wrapper').removeClass('hide');
                    emptyNote === '1' && $('.inc_note').addClass('hide') || $('.inc_note').removeClass('hide')
                    emptyName === '1' && $('.inc_name').addClass('hide') || $('.inc_name').removeClass('hide')
                }
            });

            $('body').on('click', '#expense_confirm_convert', function() {
                var parameters = new Array();
                if ($('input[name="expense_convert_invoice_type"]:checked').val() == 'save_as_draft_true') {
                    parameters['save_as_draft'] = 'true';
                }
                parameters['include_name'] = $('#inc_name').prop('checked');
                parameters['include_note'] = $('#inc_note').prop('checked');
                window.location.href = buildUrl(admin_url + 'expenses/convert_to_invoice/' + $('body').find(
                    '.expense_convert_btn').attr('data-id'), parameters);
            });
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        renderChart(<?php echo json_encode($chart_data); ?>, 'Category Wise Expenses');
    });

    function renderChart(chartData, titleText) {
        Highcharts.chart('expense_chart', {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },
            title: {
                text: titleText
            },
            series: [{
                name: 'Expense',
                colorByPoint: true,
                data: chartData
            }]
        });
    }

    function updateExpenseChart() {
        var selectedType = document.getElementById("expenseType").value;
        var titleText = '';

        // Determine the chart title and request data based on the selected type
        if (selectedType == '0') {
            titleText = 'Category Wise Expenses';
        } else if (selectedType == '1') {
            titleText = 'Payment Wise Expenses';
        } else if (selectedType == '2') {
            titleText = 'Project Wise Expenses';
        }

        // Use AJAX to fetch the correct chart data
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '' + admin_url + 'expenses/get_expenses_chart_data_type_wise?type=' + selectedType, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var responseData = JSON.parse(xhr.responseText);
                renderChart(responseData, titleText); // Update chart with new data and title
            }
        };
        xhr.send();
    }
</script>
</body>

</html>