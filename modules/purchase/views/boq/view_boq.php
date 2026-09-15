<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOQ Item Cost Analysis Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 24px;
        }

        .k-report {
            padding: 10px;
            margin: 0 auto;
        }

        /* Header */
        .k-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .k-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .k-icon-box {
            width: 36px;
            height: 36px;
            background: #1e3a8a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .k-h1 {
            font-size: 20px;
            font-weight: 700;
        }

        .k-actions {
            display: flex;
            gap: 8px;
        }

        .k-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
        }

        .k-btn:hover {
            background: #f1f5f9;
        }

        /* Meta Grid */
        .k-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .k-meta-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .k-meta-icon {
            width: 36px;
            height: 36px;
            background: #eff6ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .k-meta-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .k-meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }

        /* KPI Cards */
        .k-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .k-kpi {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            position: relative;
        }

        .k-kpi-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .k-kpi-value {
            font-size: 22px;
            font-weight: 700;
        }

        .k-kpi-sub {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .k-kpi-icon {
            position: absolute;
            top: 14px;
            right: 14px;
            opacity: 0.35;
        }

        /* Rows */
        .k-row-3 {
            display: grid;
            grid-template-columns: 1fr 1.4fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .k-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .k-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
        }

        .k-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 14px 0;
        }

        .k-chart-wrap {
            height: 220px;
            position: relative;
        }

        .k-chart-legend {
            margin-top: 10px;
        }

        /* Legend */
        .k-legend-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .k-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .k-legend-label {
            color: #64748b;
        }

        .k-legend-val {
            font-weight: 600;
            color: #0f172a;
        }

        .k-legend-total {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #f1f5f9;
        }

        /* Material Flow */
        .k-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 20px 0;
        }

        .k-flow-step {
            text-align: center;
            min-width: 70px;
        }

        .k-flow-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
        }

        .k-flow-val {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .k-flow-label {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .k-flow-arrow {
            font-size: 18px;
            color: #94a3b8;
        }

        /* Tables */
        .k-table-wrap {
            overflow-x: auto;
        }

        .k-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .k-table th {
            text-align: left;
            padding: 10px 8px;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            white-space: nowrap;
        }

        .k-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .k-table tr:hover td {
            background: #f8fafc;
        }

        .k-table-sm td,
        .k-table-sm th {
            padding: 8px 6px;
            font-size: 11px;
        }

        .k-total td {
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
        }

        /* Badges & Status */
        .k-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .k-badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .k-badge-orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .k-badge-purple {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .k-badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .k-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .k-status-green {
            background: #dcfce7;
            color: #15803d;
        }

        .k-status-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* Summary */
        .k-summary {
            font-size: 13px;
        }

        .k-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .k-summary-row:last-child {
            border-bottom: none;
        }

        .k-summary-green {
            color: #059669;
            font-weight: 600;
        }

        .k-summary-red {
            color: #dc2626;
            font-weight: 600;
        }

        .k-progress {
            width: 80px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
        }

        .k-progress-bar {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }

        /* Note */
        .k-note {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #1e40af;
            margin-top: 8px;
        }

        .k-note-icon {
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .k-row-3 {
                grid-template-columns: 1fr;
            }

            .k-row-2 {
                grid-template-columns: 1fr;
            }

            .k-kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .k-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel_s mbot10">
                <div class="k-report">
                    <!-- Header -->
                    <div class="k-header">
                        <div class="k-title-group">
                            <div class="k-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <path d="M3 9h18M9 21V9" />
                                </svg>
                            </div>
                            <h1 class="k-h1">BOQ Item Cost Analysis Report</h1>
                        </div>
                        <div class="k-actions">
                            <!-- <button class="k-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg> Export PDF
                            </button>
                            <button class="k-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg> Export Excel
                            </button>
                            <button class="k-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                                </svg> Filter
                            </button> -->
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="k-meta-grid">
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">Project</div>
                                <div class="k-meta-value"><?php echo get_project_name_by_id($wo_order->project); ?></div>
                            </div>
                        </div>
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">BOQ Item</div>
                                <div class="k-meta-value"><?php echo $wo_order_details->item_name; ?></div>
                            </div>
                        </div>
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">Unit</div>
                                <div class="k-meta-value">Cum</div>
                            </div>
                        </div>
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">Work Order</div>
                                <div class="k-meta-value"><?php echo $wo_order->wo_order_number; ?></div>
                            </div>
                        </div>
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <path d="M20 7h-9" />
                                    <path d="M14 17H5" />
                                    <circle cx="17" cy="17" r="3" />
                                    <circle cx="7" cy="7" r="3" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">Vendor's</div>
                                <div class="k-meta-value"><?php echo $get_vendor_list_by_name; ?></div>
                            </div>
                        </div>
                        <div class="k-meta-card">
                            <div class="k-meta-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A6CF7" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <div>
                                <div class="k-meta-label">Date</div>
                                <div class="k-meta-value"><?php echo date('d M, Y', strtotime($wo_order->order_date));  ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- KPI Cards -->
                    <div class="k-kpi-grid">
                        <div class="k-kpi">
                            <div class="k-kpi-label">BOQ Amount</div>
                            <div class="k-kpi-value" style="color:#1e3a8a"><?php echo app_format_money($wo_order_details->total, '₹'); ?> </div>
                            <div class="k-kpi-sub"></div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                        </div>
                        <div class="k-kpi">
                            <div class="k-kpi-label">Purchase Ordered</div>
                            <div class="k-kpi-value" style="color:#059669"> <?php echo app_format_money($get_total_pur_value, '₹'); ?></div>
                            <div class="k-kpi-sub"><?php $po_percentage = ($get_total_pur_value / $wo_order_details->total) * 100;
                                                    echo number_format($po_percentage, 2); ?>% of BOQ</div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5">
                                    <circle cx="9" cy="21" r="1" />
                                    <circle cx="20" cy="21" r="1" />
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                </svg>
                            </div>
                        </div>
                        <!-- <div class="k-kpi">
                            <div class="k-kpi-label">Goods Received</div>
                            <div class="k-kpi-value" style="color:#d97706">₹7,80,000</div>
                            <div class="k-kpi-sub">78.00% of BOQ</div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.5">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                    <line x1="12" y1="22.08" x2="12" y2="12" />
                                </svg>
                            </div>
                        </div> -->
                        <div class="k-kpi">
                            <div class="k-kpi-label">Expenses</div>
                            <div class="k-kpi-value" style="color:#ea580c"> <?php echo app_format_money($get_total_expense_value, '₹'); ?></div>
                            <div class="k-kpi-sub"><?php $exp_percentage = ($get_total_expense_value / $wo_order_details->total) * 100;
                                                    echo number_format($exp_percentage, 2); ?>% of BOQ </div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.5">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                    <line x1="1" y1="10" x2="23" y2="10" />
                                </svg>
                            </div>
                        </div>
                        <div class="k-kpi">
                            <div class="k-kpi-label">Vendor Payments</div>
                            <div class="k-kpi-value" style="color:#7c3aed">₹0</div>
                            <div class="k-kpi-sub">00.00% of BOQ</div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                    <line x1="1" y1="10" x2="23" y2="10" />
                                </svg>
                            </div>
                        </div>
                        <div class="k-kpi">
                            <div class="k-kpi-label">Total Cost (PO + Exp)</div>
                            <div class="k-kpi-value" style="color:#059669"><?php
                                                                            $total_cost = $get_total_pur_value + $get_total_expense_value;
                                                                            echo app_format_money($total_cost, '₹'); ?></div>
                            <div class="k-kpi-sub"><?php $cost_percentage = ($total_cost / $wo_order_details->total) * 100;
                                                    echo number_format($cost_percentage, 2); ?>% of BOQ </div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                        </div>
                        <div class="k-kpi">
                            <div class="k-kpi-label">Remaining Budget</div>
                            <div class="k-kpi-value" style="color:#059669"><?php echo app_format_money($wo_order_details->total - $total_cost, '₹'); ?></div>
                            <div class="k-kpi-sub"><?php $rem_percentage = (($wo_order_details->total - $total_cost) / $wo_order_details->total) * 100;
                                                    echo number_format($rem_percentage, 2); ?>% of BOQ </div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5">
                                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                                    <path d="M22 12A10 10 0 0 0 12 2v10z" />
                                </svg>
                            </div>
                        </div>
                        <div class="k-kpi">
                            <div class="k-kpi-label">Cost Variance</div>
                            <div class="k-kpi-value" style="color:#26dc2f"><?php echo app_format_money($wo_order_details->total - $total_cost, '₹'); ?></div>
                            <div class="k-kpi-sub">Under Budget</div>
                            <div class="k-kpi-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                    <line x1="12" y1="9" x2="12" y2="13" />
                                    <line x1="12" y1="17" x2="12.01" y2="17" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="k-row-3">
                        <div class="k-card">
                            <h3 class="k-card-title">Budget Utilization</h3>
                            <div class="k-chart-wrap"><canvas id="budgetChart"></canvas></div>
                            <div class="k-chart-legend" id="budgetLegend"></div>
                        </div>
                        <!-- <div class="k-card">
                            <h3 class="k-card-title">Material Flow (Quantity)</h3>
                            <div class="k-flow">
                                <div class="k-flow-step">
                                    <div class="k-flow-icon" style="background:#dbeafe;color:#2563eb">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="9" cy="21" r="1" />
                                            <circle cx="20" cy="21" r="1" />
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                        </svg>
                                    </div>
                                    <div class="k-flow-val">100.00</div>
                                    <div class="k-flow-label">BOQ Qty (Cum)</div>
                                </div>
                                <div class="k-flow-arrow">→</div>
                                <div class="k-flow-step">
                                    <div class="k-flow-icon" style="background:#dcfce7;color:#16a34a">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                    </div>
                                    <div class="k-flow-val">90.00</div>
                                    <div class="k-flow-label">Ordered Qty (Cum)</div>
                                </div>
                                <div class="k-flow-arrow">→</div>
                                <div class="k-flow-step">
                                    <div class="k-flow-icon" style="background:#fef3c7;color:#d97706">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="1" y="3" width="15" height="13" />
                                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                            <circle cx="5.5" cy="18.5" r="2.5" />
                                            <circle cx="18.5" cy="18.5" r="2.5" />
                                        </svg>
                                    </div>
                                    <div class="k-flow-val">82.00</div>
                                    <div class="k-flow-label">Received Qty (Cum)</div>
                                </div>
                                <div class="k-flow-arrow">→</div>
                                <div class="k-flow-step">
                                    <div class="k-flow-icon" style="background:#f3e8ff;color:#7c3aed">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                        </svg>
                                    </div>
                                    <div class="k-flow-val">75.00</div>
                                    <div class="k-flow-label">Used Qty (Cum)</div>
                                </div>
                                <div class="k-flow-arrow">→</div>
                                <div class="k-flow-step">
                                    <div class="k-flow-icon" style="background:#ccfbf1;color:#0d9488">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        </svg>
                                    </div>
                                    <div class="k-flow-val">25.00</div>
                                    <div class="k-flow-label">Balance Qty (Cum)</div>
                                </div>
                            </div>
                        </div> -->
                        <div class="k-card">
                            <h3 class="k-card-title">Cost Distribution</h3>
                            <div class="k-chart-wrap"><canvas id="costDistChart"></canvas></div>
                            <div class="k-chart-legend" id="costDistLegend"></div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <!-- <div class="k-card" style="margin-bottom:16px;">
                        <h3 class="k-card-title">Activity Timeline (All Transactions)</h3>
                        <div class="k-table-wrap">
                            <table class="k-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Module</th>
                                        <th>Reference</th>
                                        <th>Vendor</th>
                                        <th>Description</th>
                                        <th>Amount (₹)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>12-Jul-2026</td>
                                        <td><span class="k-badge k-badge-blue">Purchase Order</span></td>
                                        <td>PO-0015</td>
                                        <td>ABC Constructions Pvt. Ltd.</td>
                                        <td>RCC M25 Concrete - 20 Cum</td>
                                        <td>2,50,000</td>
                                        <td><span class="k-status k-status-green">Approved</span></td>
                                    </tr>
                                    <tr>
                                        <td>13-Jul-2026</td>
                                        <td><span class="k-badge k-badge-orange">Expense</span></td>
                                        <td>EXP-0004</td>
                                        <td>Shree Transport</td>
                                        <td>Transport Charges</td>
                                        <td>12,000</td>
                                        <td><span class="k-status k-status-green">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>14-Jul-2026</td>
                                        <td><span class="k-badge k-badge-purple">Payment</span></td>
                                        <td>PAY-0010</td>
                                        <td>ABC Constructions Pvt. Ltd.</td>
                                        <td>Payment against PO-0015</td>
                                        <td>1,00,000</td>
                                        <td><span class="k-status k-status-green">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>15-Jul-2026</td>
                                        <td><span class="k-badge k-badge-green">Goods Receipt</span></td>
                                        <td>GRN-0007</td>
                                        <td>ABC Constructions Pvt. Ltd.</td>
                                        <td>Received 18 Cum</td>
                                        <td>2,20,000</td>
                                        <td><span class="k-status k-status-blue">Received</span></td>
                                    </tr>
                                    <tr>
                                        <td>18-Jul-2026</td>
                                        <td><span class="k-badge k-badge-orange">Expense</span></td>
                                        <td>EXP-0006</td>
                                        <td>Labour Services</td>
                                        <td>Loading Charges</td>
                                        <td>8,000</td>
                                        <td><span class="k-status k-status-green">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>19-Jul-2026</td>
                                        <td><span class="k-badge k-badge-purple">Payment</span></td>
                                        <td>PAY-0012</td>
                                        <td>ABC Constructions Pvt. Ltd.</td>
                                        <td>Payment against PO-0015</td>
                                        <td>1,50,000</td>
                                        <td><span class="k-status k-status-green">Paid</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->

                    <!-- Tables Row -->
                    <div class="k-row-2">
                        <div class="k-card">
                            <h3 class="k-card-title">Purchase Order Details</h3>
                            <div class="k-table-wrap">
                                <table class="k-table k-table-sm table_pur_order_boq" style="width:718px; height:272px; display:block;">
                                    <thead>
                                        <tr>
                                            <th><?= _l('purchase_order') ?></th>
                                            <th><?= _l('vendor') ?></th>
                                            <th><?= _l('order_date') ?></th>
                                            <th><?= _l('Ordered Qty') ?></th>
                                            <th><?= _l('po_description') ?></th>
                                            <th><?= _l('po_value') ?></th>
                                            <th><?= _l('tax_value') ?></th>
                                            <th><?= _l('po_value_included_tax') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="k-total">
                                            <td>Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="k-card">
                            <h3 class="k-card-title">Expense Details</h3>
                            <div class="k-table-wrap">
                                <table class="k-table k-table-sm table_expense_boq">
                                    <thead>
                                        <tr>
                                            <th>Expense No</th>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Amount (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="k-row-2">
                        <!-- <div class="k-card">
                            <h3 class="k-card-title">Vendor Payment Details</h3>
                            <div class="k-table-wrap">
                                <table class="k-table k-table-sm">
                                    <thead>
                                        <tr>
                                            <th>Payment No</th>
                                            <th>Date</th>
                                            <th>Vendor</th>
                                            <th>Against</th>
                                            <th>Amount (₹)</th>
                                            <th>Mode</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>PAY-0005</td>
                                            <td>14-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0012</td>
                                            <td>1,00,000</td>
                                            <td>NEFT</td>
                                            <td><span class="k-status k-status-green">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>PAY-0006</td>
                                            <td>15-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0013</td>
                                            <td>1,50,000</td>
                                            <td>NEFT</td>
                                            <td><span class="k-status k-status-green">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>PAY-0007</td>
                                            <td>17-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0014</td>
                                            <td>1,40,000</td>
                                            <td>RTGS</td>
                                            <td><span class="k-status k-status-green">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>PAY-0008</td>
                                            <td>19-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0015</td>
                                            <td>1,50,000</td>
                                            <td>NEFT</td>
                                            <td><span class="k-status k-status-green">Paid</span></td>
                                        </tr>
                                        <tr class="k-total">
                                            <td colspan="4">Total Payments</td>
                                            <td>5,40,000</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->
                        <!-- <div class="k-card">
                            <h3 class="k-card-title">Goods Receipt Details</h3>
                            <div class="k-table-wrap">
                                <table class="k-table k-table-sm">
                                    <thead>
                                        <tr>
                                            <th>GRN No</th>
                                            <th>Date</th>
                                            <th>Vendor</th>
                                            <th>PO No</th>
                                            <th>Received Qty</th>
                                            <th>Amount (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>GRN-0005</td>
                                            <td>12-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0012</td>
                                            <td>18.00</td>
                                            <td>90,000</td>
                                        </tr>
                                        <tr>
                                            <td>GRN-0006</td>
                                            <td>13-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0013</td>
                                            <td>28.00</td>
                                            <td>1,40,000</td>
                                        </tr>
                                        <tr>
                                            <td>GRN-0007</td>
                                            <td>15-Jul-2026</td>
                                            <td>ABC Constructions Pvt. Ltd.</td>
                                            <td>PO-0014</td>
                                            <td>36.00</td>
                                            <td>1,80,000</td>
                                        </tr>
                                        <tr class="k-total">
                                            <td colspan="4">Total Received</td>
                                            <td>82.00</td>
                                            <td>4,10,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->

                    </div>

                    <!-- Bottom Charts -->
                    <div class="k-row-6">
                        <div class="k-card" style="margin-bottom:16px;">
                            <h3 class="k-card-title">Budget vs Actual (Amount)</h3>
                            <div class="k-chart-wrap"><canvas id="budgetVsActualChart"></canvas></div>
                        </div>
                        <div class="k-card" style="margin-bottom:16px;">
                            <h3 class="k-card-title">Monthly Cost Trend (Cumulative)</h3>
                            <div class="k-chart-wrap"><canvas id="trendChart"></canvas></div>
                        </div>
                        <div class="k-card">
                            <h3 class="k-card-title">Budget Analysis Summary</h3>
                            <div class="k-summary">
                                <div class="k-summary-row"><span>BOQ Budget</span><span><?php echo app_format_money($wo_order_details->total, '₹'); ?></span></div>
                                <div class="k-summary-row"><span>Purchase Orders</span><span><?php echo app_format_money($get_total_pur_value, '₹'); ?></span></div>
                                <div class="k-summary-row"><span>Expenses</span><span><?php echo app_format_money($get_total_expense_value, '₹'); ?></span></div>
                                <div class="k-summary-row"><span>Total Cost (PO + Exp)</span><span><?php echo app_format_money($total_cost, '₹'); ?></span></div>
                                <div class="k-summary-row k-summary-green"><span>Remaining Budget</span><span><?php echo app_format_money($wo_order_details->total - $total_cost, '₹'); ?></span></div>
                                <div class="k-summary-row">
                                    <span>Utilization</span>
                                    <span>
                                        <div class="k-progress">
                                            <div class="k-progress-bar" style="width:87.5%"></div>
                                        </div> <?php echo app_format_money($rem_percentage, '₹'); ?>%
                                    </span>
                                </div>
                                <div class="k-summary-row"><span>Cost Variance</span><span><?php echo app_format_money($wo_order_details->total - $total_cost, '₹'); ?></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="k-note">
                        <span class="k-note-icon">ⓘ</span> Note: All amounts are in INR (₹). Quantities are in the respective unit mentioned above.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
<script>
    (function() {


        // Budget Utilization Doughnut
        const budgetCtx = document.getElementById('budgetChart').getContext('2d');
        new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Total Cost (PO + Exp)', 'Remaining Budget'],
                datasets: [{
                    data: [<?php echo $total_cost; ?>, <?php echo $rem_percentage; ?>],
                    backgroundColor: ['#059669', '#e2e8f0'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => c.label + ': ' + c.raw + '%'
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: (chart) => {
                    const {
                        ctx,
                        width,
                        height
                    } = chart;
                    ctx.save();
                    ctx.font = 'bold 22px Inter, sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('87.50%', width / 2, height / 2 - 8);
                    ctx.font = '12px Inter, sans-serif';
                    ctx.fillStyle = '#64748b';
                    ctx.fillText('Budget Utilized', width / 2, height / 2 + 12);
                    ctx.restore();
                }
            }]
        });
        document.getElementById('budgetLegend').innerHTML = `
    <div class="k-legend-item"><span class="k-legend-dot" style="background:#059669"></span><div><div class="k-legend-label">Total Cost (PO + Exp)</div><div class="k-legend-val"><?php echo app_format_money($total_cost, '₹'); ?>  (<?php echo number_format($cost_percentage, 2); ?>%)</div></div></div>
    <div class="k-legend-item"><span class="k-legend-dot" style="background:#e2e8f0"></span><div><div class="k-legend-label">Remaining Budget</div><div class="k-legend-val"><?php echo app_format_money($rem_percentage, '₹'); ?> (<?php echo number_format($rem_percentage, 2); ?>%)</div></div></div>
    <div class="k-legend-total">BOQ Amount: <strong><?php echo app_format_money($wo_order_details->total, '₹'); ?></strong></div>`;

        // Cost Distribution Doughnut
        const costCtx = document.getElementById('costDistChart').getContext('2d');
        new Chart(costCtx, {
            type: 'doughnut',
            data: {
                labels: ['Purchase Orders', 'Expenses'],
                datasets: [{
                    data: [<?php echo $get_total_pur_value; ?>, <?php echo $get_total_expense_value; ?>],
                    backgroundColor: ['#3b82f6', '#60a5fa'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => '₹' + c.raw.toLocaleString('en-IN') + ' (' + ((c.raw / 875000) * 100).toFixed(2) + '%)'
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText2',
                beforeDraw: (chart) => {
                    const {
                        ctx,
                        width,
                        height
                    } = chart;
                    ctx.save();
                    ctx.font = 'bold 18px Inter, sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('₹8,75,000', width / 2, height / 2 - 6);
                    ctx.font = '11px Inter, sans-serif';
                    ctx.fillStyle = '#64748b';
                    ctx.fillText('Total Cost', width / 2, height / 2 + 12);
                    ctx.restore();
                }
            }]
        });
        document.getElementById('costDistLegend').innerHTML = `
                        <div class="k-legend-item"><span class="k-legend-dot" style="background:#3b82f6"></span><div><div class="k-legend-label">Purchase Orders</div><div class="k-legend-val"><?php echo app_format_money($get_total_pur_value, '₹'); ?> (<?php echo number_format($po_percentage, 2); ?>%)</div></div></div>
                        <div class="k-legend-item"><span class="k-legend-dot" style="background:#60a5fa"></span><div><div class="k-legend-label">Expenses</div><div class="k-legend-val"><?php echo app_format_money($get_total_expense_value, '₹'); ?> (<?php echo number_format($exp_percentage, 2); ?>%)</div></div></div>`;

        // Budget vs Actual Bar
        const bvaCtx = document.getElementById('budgetVsActualChart').getContext('2d');
        new Chart(bvaCtx, {
            type: 'bar',
            data: {
                labels: ['BOQ Budget', 'Total Cost (PO + Exp)'],
                datasets: [{
                    data: [<?php echo $wo_order_details->total; ?>, <?php echo $total_cost; ?>],
                    backgroundColor: ['#3b82f6', '#059669'],
                    borderRadius: 4,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: v => '₹' + (v / 100000).toFixed(1) + 'L'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [{
                id: 'topLabels',
                afterDatasetsDraw: (chart) => {
                    const {
                        ctx
                    } = chart;
                    chart.data.datasets[0].data.forEach((val, i) => {
                        const meta = chart.getDatasetMeta(0);
                        const bar = meta.data[i];
                        ctx.save();
                        ctx.font = 'bold 12px Inter, sans-serif';
                        ctx.fillStyle = '#0f172a';
                        ctx.textAlign = 'center';
                        ctx.fillText(val.toLocaleString('en-IN'), bar.x, bar.y - 8);
                        ctx.restore();
                    });
                }
            }]
        });

        // Monthly Trend Line
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Apr-2026', 'May-2026', 'Jun-2026', 'Jul-2026'],
                datasets: [{
                    label: 'Cumulative Cost (₹)',
                    data: [120000, 280000, 560000, 875000],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: v => '₹' + (v / 100000).toFixed(1) + 'L'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [{
                id: 'pointLabels',
                afterDatasetsDraw: (chart) => {
                    const {
                        ctx
                    } = chart;
                    const meta = chart.getDatasetMeta(0);
                    meta.data.forEach((pt, i) => {
                        const val = chart.data.datasets[0].data[i];
                        ctx.save();
                        ctx.font = 'bold 11px Inter, sans-serif';
                        ctx.fillStyle = '#0f172a';
                        ctx.textAlign = 'center';
                        ctx.fillText(val.toLocaleString('en-IN'), pt.x, pt.y - 12);
                        ctx.restore();
                    });
                }
            }]
        });
    })();

    $(function() {
        var Params = {

        };
        initDataTable('.table_pur_order_boq', admin_url + 'purchase/table_pur_order_boq/<?php echo $wo_order_details->id; ?>', [], [], Params, [2, 'desc']);

    });

    $(function() {
        var ParamsExp = {

        };
        initDataTable('.table_expense_boq', admin_url + 'purchase/table_expense_boq/<?php echo $wo_order_details->id; ?>', [], [], ParamsExp, [2, 'desc']);

    });


</script>
</body>

</html>