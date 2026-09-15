<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pdf_signer_model extends App_Model
{
    private $documents;
    private $elements;

    public function __construct()
    {
        parent::__construct();

        $this->documents =
            db_prefix() . 'pdf_signer_documents';

        $this->elements =
            db_prefix() . 'pdf_signer_elements';
    }


    public function get_documents()
    {
        return $this->db
            ->order_by('id', 'DESC')
            ->get($this->documents)
            ->result();
    }


    public function get_document($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->documents)
            ->row();
    }


    public function create_document($data)
    {
        $this->db->insert(
            $this->documents,
            $data
        );

        return $this->db->insert_id();
    }


    public function add_element($data)
    {
        return $this->db->insert(
            $this->elements,
            $data
        );
    }


    public function delete_elements($document_id)
    {
        return $this->db
            ->where('document_id', $document_id)
            ->delete($this->elements);
    }


    public function mark_signed(
        $document_id,
        $signed_file
    ) {
        return $this->db
            ->where('id', $document_id)
            ->update(
                $this->documents,
                [
                    'signed_file' => $signed_file,
                    'status' => 'signed',
                    'signed_at' =>
                        date('Y-m-d H:i:s'),
                    'signed_ip' =>
                        $this->input->ip_address()
                ]
            );
    }
}