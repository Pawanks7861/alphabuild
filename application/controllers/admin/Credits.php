<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Credits extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('credits_model');
    }

    public function add_credit()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $amount      = $this->input->post('amount');
        $credit_date = $this->input->post('credit_date');

        /*
         * Get currently logged-in user.
         */
        $user_id = get_staff_user_id();

        if (!$user_id) {

            echo json_encode([
                'success' => false,
                'message' => 'User session expired. Please login again.'
            ]);

            return;
        }

        /*
         * Validate amount
         */
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {

            echo json_encode([
                'success' => false,
                'message' => 'Please enter a valid credit amount.'
            ]);

            return;
        }

        /*
         * Validate date
         */
        if (empty($credit_date)) {

            echo json_encode([
                'success' => false,
                'message' => 'Please select date and time.'
            ]);

            return;
        }

        /*
         * Convert datetime-local:
         *
         * 2026-08-06T17:30
         *
         * into:
         *
         * 2026-08-06 17:30:00
         */
        $credit_date = str_replace('T', ' ', $credit_date);

        if (strlen($credit_date) === 16) {
            $credit_date .= ':00';
        }

        /*
         * Prepare data
         */
        $data = [
            'user_id'     => (int) $user_id,
            'amount'      => number_format((float) $amount, 2, '.', ''),
            'credit_date' => $credit_date,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        /*
         * Save through model
         */
        $insert_id = $this->credits_model->add_credit($data);

        if ($insert_id) {

            echo json_encode([
                'success' => true,
                'message' => 'Credit added successfully.',
                'id'      => $insert_id
            ]);

            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Unable to save credit. Please try again.'
        ]);
    }
}