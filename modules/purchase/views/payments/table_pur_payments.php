<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    
    'pur_order_number',
    'vendor',
    'order_date',
    'payment_days',
    'project',
    'department',
];



$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_orders';
$join         = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',
];
$i = 0;


$where = [];

// array_push($where, "AND approve_status = '2'");

$having = '';


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'pur_orders.id as id', 'company', 'pur_order_number', 'expense_convert', db_prefix() . 'projects.name as project_name', db_prefix() . 'departments.name as department_name', 'currency', '(SELECT GROUP_CONCAT(' . db_prefix() . 'project_members.staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE ' . db_prefix() . 'project_members.project_id=' . db_prefix() . 'pur_orders.project) as member_list'], '', [], $having);

$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('purchase/purchase_model');

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        $base_currency = get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if ($aColumns[$i] == 'total') {
            $_data = app_format_money($aRow['total'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'pur_order_number') {

            $numberOutput = '';

            $numberOutput = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '"  onclick="init_pur_order(' . $aRow['id'] . '); return false;" >' . $aRow['pur_order_number'] . '</a>';

            $numberOutput .= '<div class="row-options">';

            if (has_permission('purchase_orders', '', 'view') || has_permission('purchase_orders', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" onclick="init_pur_order(' . $aRow['id'] . '); return false;" >' . _l('view') . '</a>';
            }
            if ((has_permission('purchase_orders', '', 'edit') || is_admin()) && $aRow['approve_status'] != 2) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/pur_order/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_orders', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_pur_order/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'vendor') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
        } elseif ($aColumns[$i] == 'order_date') {
            $_data = _d($aRow['order_date']);
        } elseif ($aColumns[$i] == 'payment_days') {
            $_data = get_payment_term_name($aRow['payment_days']);
        }  elseif ($aColumns[$i] == 'project') {
            $_data = $aRow['project_name'];
        } elseif ($aColumns[$i] == 'department') {
            $_data = $aRow['department_name'];
        } elseif($aColumns[$i] == 1){
            $_data= 1;
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
