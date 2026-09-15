<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Credits_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add credit
     *
     * @param array $data
     * @return int|false
     */
    public function add_credit($data)
    {
        $this->db->insert(
            db_prefix() . 'pur_credits',
            $data
        );

        if ($this->db->affected_rows() > 0) {

            return $this->db->insert_id();
        }

        return false;
    }
}