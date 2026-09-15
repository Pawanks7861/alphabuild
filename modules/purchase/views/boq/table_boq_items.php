<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'wo_order_number',
    'item_name',
    'project',
    'order_date',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'wo_order_detail';

$join = [
    'INNER JOIN ' . db_prefix() . 'wo_orders
        ON ' . db_prefix() . 'wo_orders.id = ' . db_prefix() . 'wo_order_detail.wo_order',

    'LEFT JOIN ' . db_prefix() . 'projects
        ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'wo_orders.project',
];

$where = [];

array_push($where, 'AND ' . db_prefix() . 'wo_orders.wo_type = 1');

if ($this->ci->input->post('project') && count($this->ci->input->post('project')) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'wo_orders.project IN (' . implode(',', $this->ci->input->post('project')) . ')');
}


$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        db_prefix() . 'wo_order_detail.id',
        db_prefix() . 'wo_order_detail.item_name',

        db_prefix() . 'wo_orders.wo_order_number',
        db_prefix() . 'wo_orders.wo_order_name',
        db_prefix() . 'wo_orders.project',
        db_prefix() . 'wo_orders.order_date',

        db_prefix() . 'projects.name as project_name'
    ]
);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];

    // Work Order
    $row[] = '<strong>'
        . $aRow['wo_order_number']
        . ' - '
        . $aRow['wo_order_name']
        . '</strong>';

    // Item
    $row[] = $aRow['item_name'];

    // Project
    $row[] = $aRow['project_name'];

    // Order Date
    $row[] = _d($aRow['order_date']);

    // Option
    $options = '';

    $options .= '<a href="' . admin_url('purchase/boq_view/' . $aRow['id']) . '" class="btn btn-default btn-icon" title="' . _l('view') . '">
                    <i class="fa fa-eye"></i>
                 </a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
