<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pdf_signer extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pdf_signer_model');
    }

    /**
     * PDF Signer Dashboard
     */
    public function index()
    {
        $data['title'] = 'PDF Signer';

        $data['documents'] = $this->pdf_signer_model->get_documents();

        $this->load->view('admin/pdf_signer/index', $data);
    }


    /**
     * Upload PDF
     */
    public function upload()
    {
        if (
            !isset($_FILES['pdf_file']) ||
            empty($_FILES['pdf_file']['name'])
        ) {
            echo json_encode([
                'success' => false,
                'message' => 'Please select a PDF file.'
            ]);
            return;
        }

        $file = $_FILES['pdf_file'];

        // Only PDF
        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if ($extension !== 'pdf') {
            echo json_encode([
                'success' => false,
                'message' => 'Only PDF files are allowed.'
            ]);
            return;
        }

        // 20 MB limit
        if ($file['size'] > (20 * 1024 * 1024)) {
            echo json_encode([
                'success' => false,
                'message' => 'Maximum file size is 20 MB.'
            ]);
            return;
        }

        $upload_path = FCPATH . 'uploads/pdf_signer/original/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $new_name = uniqid('pdf_', true) . '.pdf';

        $destination = $upload_path . $new_name;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to upload PDF.'
            ]);
            return;
        }

        $document_id = $this->pdf_signer_model->create_document([
            'client_id'   => null,
            'uploaded_by' => get_staff_user_id(),
            'document_name' => $file['name'],
            'original_file' => 'uploads/pdf_signer/original/' . $new_name,
            'status' => 'uploaded',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'document_id' => $document_id,
            'file' => base_url(
                'uploads/pdf_signer/original/' . $new_name
            )
        ]);
    }


    /**
     * Open signer
     */
    public function sign($document_id)
    {
        $document = $this->pdf_signer_model->get_document($document_id);

        if (!$document) {
            show_404();
        }

        $data['document'] = $document;

        $data['signature'] = base_url(
            'uploads/pdf_signer/signature.png'
        );

        $data['stamp'] = base_url(
            'uploads/pdf_signer/stamp.png'
        );

        $this->load->view(
            'admin/pdf_signer/signer',
            $data
        );
    }


    /**
     * Save final PDF
     */
    public function save_signed()
    {
        $document_id = (int) $this->input->post('document_id');

        $elements = json_decode(
            $this->input->post('elements'),
            true
        );

        if (!$document_id || empty($elements)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid signing data.'
            ]);
            return;
        }

        $document = $this->pdf_signer_model
            ->get_document($document_id);

        if (!$document) {
            echo json_encode([
                'success' => false,
                'message' => 'Document not found.'
            ]);
            return;
        }

        $original_file = FCPATH . $document->original_file;

        if (!file_exists($original_file)) {
            echo json_encode([
                'success' => false,
                'message' => 'Original PDF not found.'
            ]);
            return;
        }

        $signed_dir = FCPATH . 'uploads/pdf_signer/signed/';

        if (!is_dir($signed_dir)) {
            mkdir($signed_dir, 0755, true);
        }

        $signed_name =
            'signed_' .
            $document_id .
            '_' .
            time() .
            '.pdf';

        $signed_file = $signed_dir . $signed_name;

        try {

            $this->create_signed_pdf(
                $original_file,
                $signed_file,
                $elements
            );

        } catch (Throwable $e) {

            log_message(
                'error',
                'PDF Sign Error: ' . $e->getMessage()
            );

            echo json_encode([
                'success' => false,
                'message' => 'Unable to create signed PDF.'
            ]);

            return;
        }

        $this->pdf_signer_model
            ->delete_elements($document_id);

        foreach ($elements as $element) {

            $this->pdf_signer_model->add_element([
                'document_id' => $document_id,
                'element_type' => $element['type'],
                'page_number' => (int) $element['page'],
                'x' => (float) $element['x'],
                'y' => (float) $element['y'],
                'width' => (float) $element['width'],
                'height' => (float) $element['height'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->pdf_signer_model
            ->mark_signed(
                $document_id,
                'uploads/pdf_signer/signed/' . $signed_name
            );

        echo json_encode([
            'success' => true,
            'download_url' => base_url(
                'uploads/pdf_signer/signed/' . $signed_name
            )
        ]);
    }


    /**
     * Create signed PDF
     */
    private function create_signed_pdf(
        $source,
        $destination,
        $elements
    ) {

        require_once(FCPATH . 'vendor/autoload.php');

        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();

        $pdf->SetAutoPageBreak(false);

        $page_count = $pdf->setSourceFile($source);

        for ($page = 1; $page <= $page_count; $page++) {

            $template = $pdf->importPage($page);

            $size = $pdf->getTemplateSize($template);

            $orientation =
                ($size['width'] > $size['height'])
                    ? 'L'
                    : 'P';

            $pdf->AddPage(
                $orientation,
                [
                    $size['width'],
                    $size['height']
                ]
            );

            $pdf->useTemplate($template);

            foreach ($elements as $element) {

                if (
                    (int)$element['page'] !== $page
                ) {
                    continue;
                }

                if (
                    !in_array(
                        $element['type'],
                        ['signature', 'stamp'],
                        true
                    )
                ) {
                    continue;
                }

                $image = FCPATH .
                    'uploads/pdf_signer/' .
                    (
                        $element['type'] === 'signature'
                            ? 'signature.png'
                            : 'stamp.png'
                    );

                if (!file_exists($image)) {
                    continue;
                }

                $pdf->Image(
                    $image,
                    (float)$element['x'],
                    (float)$element['y'],
                    (float)$element['width'],
                    (float)$element['height'],
                    'PNG'
                );
            }
        }

        $pdf->Output(
            $destination,
            'F'
        );
    }
}