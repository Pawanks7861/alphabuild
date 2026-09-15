<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Wo_order_pdf extends App_pdf
{
    protected $wo_order;

    public function __construct($wo_order)
    {
        $wo_order                = hooks()->apply_filters('request_html_pdf_data', $wo_order);
        $GLOBALS['wo_order_pdf'] = $wo_order;

        parent::__construct();

        $this->wo_order = $wo_order;

        // Set landscape orientation
        $this->SetPageOrientation('L'); // 'L' for Landscape, 'P' for Portrait

        $this->SetTitle(_l('work_order'));
        # Don't remove these lines - important for the PDF layout
        $this->wo_order = $this->fix_editor_html($this->wo_order);
    }

    public function prepare()
    {
        
        $this->set_view_vars('wo_order', $this->wo_order);

        return $this->build();
    }

    protected function type()
    {
        return 'wo_order';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/work_order/wo_orderpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}