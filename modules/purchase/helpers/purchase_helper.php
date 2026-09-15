<?php
defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('after_email_templates', 'add_purchase_email_templates');
/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */
/**
 * Determines whether the specified identifier is empty vendor company.
 *
 * @param      <type>   $id     The identifier
 *
 * @return     boolean  True if the specified identifier is empty vendor company, False otherwise.
 */
function is_empty_vendor_company($id)
{
    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from(db_prefix() . 'pur_vendor');
    $CI->db->where('userid', $id);
    $row = $CI->db->get()->row();
    if ($row) {
        if ($row->company == '') {
            return true;
        }

        return false;
    }

    return true;
}

/**
 * Gets the sql select vendor company.
 *
 * @return     string  The sql select vendor company.
 */
function get_sql_select_vendor_company()
{
    return 'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'pur_contacts WHERE ' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid and is_primary = 1) ELSE company END as company';
}

/**
 * Determines if vendor admin.
 *
 * @param      string   $id        The identifier
 * @param      string   $staff_id  The staff identifier
 *
 * @return     integer  True if vendor admin, False otherwise.
 */
function is_vendor_admin($id, $staff_id = '')
{
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $CI       = &get_instance();
    $cache    = $CI->app_object_cache->get($id . '-is-vendor-admin-' . $staff_id);

    if ($cache) {
        return $cache['retval'];
    }

    $total = total_rows(db_prefix() . 'pur_vendor_admin', [
        'vendor_id' => $id,
        'staff_id'    => $staff_id,
    ]);

    $retval = $total > 0 ? true : false;
    $CI->app_object_cache->add($id . '-is-vendor-admin-' . $staff_id, ['retval' => $retval]);

    return $retval;
}

/**
 * Gets the vendor company name.
 *
 * @param      string   $userid                 The userid
 * @param      boolean  $prevent_empty_company  The prevent empty company
 *
 * @return     string   The vendor company name.
 */
function get_vendor_company_name($userid, $prevent_empty_company = false)
{
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI = &get_instance();

    $client = $CI->db->select('company')
        ->where('userid', $_userid)
        ->from(db_prefix() . 'pur_vendor')
        ->get()
        ->row();
    if ($client) {
        return $client->company;
    }

    return '';
}

/**
 * Gets the status approve.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status approve.
 */
function get_status_approve($status)
{
    $result = '';
    if ($status == 1) {
        $result = '<span class="label label-primary"> ' . _l('purchase_draft') . ' </span>';
    } elseif ($status == 2) {
        $result = '<span class="label label-success"> ' . _l('purchase_approved') . ' </span>';
    } elseif ($status == 3) {
        $result = '<span class="label label-warning"> ' . _l('pur_rejected') . ' </span>';
    } elseif ($status == 4) {
        $result = '<span class="label label-danger"> ' . _l('pur_canceled') . ' </span>';
    }

    return $result;
}

/**
 * Gets the status approve string.
 *
 * @param      integer  $status  The status
 *
 * @return     string   The status approve string.
 */
function get_status_approve_str($status)
{
    $result = '';
    if ($status == 1) {
        $result = _l('purchase_draft');
    } elseif ($status == 2) {
        $result = _l('purchase_approved');
    } elseif ($status == 3) {
        $result = _l('pur_rejected');
    } elseif ($status == 4) {
        $result = _l('pur_canceled');
    }

    return $result;
}

/**
 * Gets the status pur order.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status pur order.
 */
function get_status_pur_order($status)
{
    $result = '';
    if ($status == 1) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-1"> ' . _l('not_start') . ' </span>';
    } elseif ($status == 2) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-2"> ' . _l('in_proccess') . ' </span>';
    } elseif ($status == 3) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-3"> ' . _l('complete') . ' </span>';
    } elseif ($status == 4) {
        $result = '<span class="label label inline-block project-status-' . $status . ' status-pur-order-4"> ' . _l('cancel') . ' </span>';
    }

    return $result;
}

/**
 * { format pur estimate number }
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  ( estimate number )
 */
function format_pur_estimate_number($id)
{
    $CI = &get_instance();
    $CI->db->select('date,number,prefix,number_format')->from(db_prefix() . 'pur_estimates')->where('id', $id);
    $estimate = $CI->db->get()->row();

    if (!$estimate) {
        return '';
    }

    $number = sales_number_format($estimate->number, $estimate->number_format, $estimate->prefix, $estimate->date);

    return hooks()->apply_filters('format_estimate_number', $number, [
        'id'       => $id,
        'estimate' => $estimate,
    ]);
}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function get_item_hp($id = '')
{
    $CI           = &get_instance();
    if ($id != '') {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'items')->row();
    } elseif ($id == '') {
        return $CI->db->get(db_prefix() . 'items')->result_array();
    }
}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function get_item_hp2($id)
{
    $CI           = &get_instance();

    $CI->db->where('id', $id);
    return $CI->db->get(db_prefix() . 'items')->row();
}

/**
 * Gets the status modules pur.
 *
 * @param      string   $module_name  The module name
 *
 * @return     boolean  The status modules pur.
 */
function get_status_modules_pur($module_name)
{
    $CI             = &get_instance();
    $sql = 'select * from ' . db_prefix() . 'modules where module_name = "' . $module_name . '" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if ($module) {
        return true;
    } else {
        return false;
    }
}

/**
 * { reformat currency pur }
 *
 * @param      <string>  $value  The value
 *
 * @return     <string>  ( string replace ',' )
 */
function reformat_currency_pur($value, $currency = 0)
{
    $CI             = &get_instance();
    $CI->load->model('currencies_model');

    $base_currency = $CI->currencies_model->get_base_currency();

    if ($currency != 0) {
        $base_currency = pur_get_currency_by_id($currency);
    }

    if ($base_currency->decimal_separator == ',') {
        $new_val = str_replace('.', '', $value);
        return str_replace(',', '.', $new_val);
    }
    return str_replace(',', '', $value);
}

/**
 * { pur contract pdf }
 *
 * @param      <type>  $contract  The contract
 *
 * @return     <type>  ( pdf )
 */
function pur_contract_pdf($contract)
{
    return app_pdf('purchase_contract',  module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_contract_pdf'), $contract);
}

/**
 * { purchase process digital signature image }
 *
 * @param      <type>   $partBase64  The part base 64
 * @param      <type>   $path        The path
 * @param      string   $image_name  The image name
 *
 * @return     boolean  
 */
function purchase_process_digital_signature_image($partBase64, $path, $image_name)
{
    if (empty($partBase64)) {
        return false;
    }

    _maybe_create_upload_path($path);
    $filename = unique_filename($path, $image_name . '.png');

    $decoded_image = base64_decode($partBase64);

    $retval = false;

    $path = rtrim($path, '/') . '/' . $filename;

    $fp = fopen($path, 'w+');

    if (fwrite($fp, $decoded_image)) {
        $retval                                 = true;
        $GLOBALS['processed_digital_signature'] = $filename;
    }

    fclose($fp);

    return $retval;
}

/**
 * { handle request quotation upload file quotation }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function handle_request_quotation($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the staff email by identifier pur.
 *
 * @param      <type>   $id     The identifier
 *
 * @return     boolean  The staff email by identifier pur.
 */
function get_staff_email_by_id_pur($id)
{
    $CI = &get_instance();

    $staff = $CI->app_object_cache->get('staff-email-by-id-' . $id);

    if (!$staff) {
        $CI->db->where('staffid', $id);
        $staff = $CI->db->select('email')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-email-by-id-' . $id, $staff);
    }

    return ($staff ? $staff->email : '');
}

/**
 * Gets the purchase option.
 *
 * @param      <type>        $name   The name
 *
 * @return     array|string  The purchase option.
 */
function get_purchase_option($name)
{
    $CI = &get_instance();
    $options = [];
    $val  = '';
    $name = trim($name);


    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('option_val');
        $CI->db->where('option_name', $name);
        $row = $CI->db->get(db_prefix() . 'purchase_option')->row();
        if ($row) {
            $val = $row->option_val;
        }
    } else {
        $val = $options[$name];
    }

    return $val;
}

/**
 * { row purchase options exist }
 *
 * @param      <type>   $name   The name
 *
 * @return     integer  ( 1 or 0 )
 */
function row_purchase_options_exist($name)
{
    $CI = &get_instance();
    $i = count($CI->db->query('Select * from ' . db_prefix() . 'purchase_option where option_name = ' . $name)->result_array());
    if ($i == 0) {
        return 0;
    }
    if ($i > 0) {
        return 1;
    }
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_order_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];
                $CI->misc_model->add_attachment_to_database($id, 'pur_order', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_request_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_request/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];
                $CI->misc_model->add_attachment_to_database($id, 'pur_request', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { purorder left to pay }
 *
 * @param      <type>   $id     The purchase order
 *
 * @return     integer  ( purchase order left to pay )
 */
function purorder_left_to_pay($id)
{
    $CI = &get_instance();


    $CI->db->select('total')
        ->where('id', $id);
    $invoice_total = $CI->db->get(db_prefix() . 'pur_orders')->row()->total;



    $CI->load->model('purchase_model');

    $payments = $CI->purchase_model->get_payment_purchase_order($id);

    $totalPayments = 0;



    foreach ($payments as $payment) {

        $totalPayments += $payment['amount'];
    }

    return ($invoice_total - $totalPayments);
}

/**
 * Gets the payment mode by identifier.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  The payment mode by identifier.
 */
function get_payment_mode_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $mode = $CI->db->get(db_prefix() . 'payment_modes')->row();
    if ($mode) {
        return $mode->name;
    } else {
        return '';
    }
}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
function get_unit_type_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);
        return $CI->db->get(db_prefix() . 'ware_unit_type')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblware_unit_type')->result_array();
    }
}


/**
 * handle commodity attchment
 * @param  integer $id
 * @return array or row
 */
function handle_item_attachments($id)
{

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $id . '/';
    $CI   = &get_instance();

    if (isset($_FILES['file']['name'])) {

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            _maybe_create_upload_path($path);
            $filename    = $_FILES['file']['name'];
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];

                $CI->misc_model->add_attachment_to_database($id, 'commodity_item_file', $attachment);
            }
        }
    }
}


/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function get_tax_rate_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tbltaxes')->result_array();
    }
}


/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function get_group_name_item($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items_groups')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems_groups')->result_array();
    }
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function max_number_pur_order()
{
    $CI           = &get_instance();
    $max = $CI->db->query('select MAX(number) as max from ' . db_prefix() . 'pur_orders')->row();
    return $max->max;
}

/**
 * Gets all pur vendor attachments.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     array   All pur vendor attachments.
 */
function get_all_pur_vendor_attachments($id)
{
    $CI = &get_instance();

    $attachments                = [];
    $attachments['purchase_vendor']    = [];

    $CI->db->where('rel_id', $id);
    $CI->db->where('rel_type', 'pur_vendor');
    $client_main_attachments = $CI->db->get(db_prefix() . 'files')->result_array();

    $attachments['purchase_vendor'] = $client_main_attachments;

    return $attachments['purchase_vendor'];
}

/**
 * { handle purchase vendor attachments upload }
 *
 * @param      string   $id               The identifier
 * @param      boolean  $customer_upload  The customer upload
 *
 * @return     boolean  
 */
function handle_pur_vendor_attachments_upload($id, $customer_upload = false)
{

    $path           = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $id . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        _file_attachments_index_fix('file');
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            hooks()->do_action('before_upload_client_attachment', $id);
            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['file']['error'][$i])
                    || !_upload_extension_allowed($_FILES['file']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    if (is_image($newFilePath)) {
                        create_img_thumb($newFilePath, $filename);
                    }

                    if ($customer_upload == true) {
                        $attachment[0]['staffid']          = 0;
                        $attachment[0]['contact_id']       = get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($id, 'pur_vendor', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Gets the template part.
 *
 * @param      string   $name    The name
 * @param      array    $data    The data
 * @param      boolean  $return  The return
 *
 * @return     string   The template part.
 */
function get_template_part_pur($name, $data = [], $return = false)
{
    if ($name === '') {
        return '';
    }

    $CI   = &get_instance();
    $path = 'vendor_portal/template_parts/';

    if ($return == true) {
        return $CI->load->view($path . $name, $data, true);
    }

    $CI->load->view($path . $name, $data);
}

/**
 * Maybe upload contact profile image
 * @param  string $contact_id contact_id or current logged in contact id will be used if not passed
 * @return boolean
 */
function handle_vendor_contact_profile_image_upload($contact_id = '')
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        hooks()->do_action('before_upload_contact_profile_image');
        if ($contact_id == '') {
            $contact_id = get_vendor_contact_user_id();
        }
        $path =  PURCHASE_MODULE_UPLOAD_FOLDER . '/contact_profile/' . $contact_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
            ];

            $allowed_extensions = hooks()->apply_filters('contact_profile_image_upload_allowed_extensions', $allowed_extensions);

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['profile_image']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI                       = &get_instance();
                $config                   = [];
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('contact_profile_image_thumb_width', 320);
                $config['height']         = hooks()->apply_filters('contact_profile_image_thumb_height', 320);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width']          = hooks()->apply_filters('contact_profile_image_small_width', 32);
                $config['height']         = hooks()->apply_filters('contact_profile_image_small_height', 32);
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();

                $CI->db->where('id', $contact_id);
                $CI->db->update(db_prefix() . 'pur_contacts', [
                    'profile_image' => $filename,
                ]);
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Return contact profile image url
 * @param  mixed $contact_id
 * @param  string $type
 * @return string
 */
function vendor_contact_profile_image_url($contact_id, $type = 'small')
{
    $url  = base_url('assets/images/user-placeholder.jpg');
    $CI   = &get_instance();
    $path = $CI->app_object_cache->get('contact-profile-image-path-' . $contact_id);

    if (!$path) {
        $CI->app_object_cache->add('contact-profile-image-path-' . $contact_id, $url);

        $CI->db->select('profile_image');
        $CI->db->from(db_prefix() . 'pur_contacts');
        $CI->db->where('id', $contact_id);
        $contact = $CI->db->get()->row();

        if ($contact && !empty($contact->profile_image)) {
            $path = PURCHASE_PATH . 'contact_profile/' . $contact_id . '/' . $type . '_' . $contact->profile_image;
            $CI->app_object_cache->set('contact-profile-image-path-' . $contact_id, $path);
        }
    }

    if ($path && file_exists($path)) {
        $url = base_url($path);
    }

    return $url;
}

/**
 * Gets the pur order subject.
 *
 * @param      <type>  $pur_order  The pur order
 *
 * @return     string  The pur order subject.
 */
function get_pur_order_subject($pur_order)
{
    $CI   = &get_instance();
    $CI->db->where('id', $pur_order);
    $po = $CI->db->get(db_prefix() . 'pur_orders')->row();

    if ($po) {
        return $po->pur_order_number;
    } else {
        return '';
    }
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function max_number_estimates()
{
    $CI           = &get_instance();
    $max = $CI->db->query('select MAX(number) as max from ' . db_prefix() . 'pur_estimates')->row();
    return $max->max;
}

/**
 * Check if the document should be RTL or LTR
 * The checking are performed in multiple ways eq Contact/Staff Direction from profile or from general settings *
 * @param  boolean $client_area
 * @return boolean
 */
function is_rtl_pur($client_area = false)
{
    $CI = &get_instance();
    if (is_vendor_logged_in()) {
        $CI->db->select('direction')->from(db_prefix() . 'pur_contacts')->where('id', get_vendor_contact_user_id());
        $direction = $CI->db->get()->row()->direction;

        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_client') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == true) {
        // Client not logged in and checked from clients area
        if (get_option('rtl_support_client') == 1) {
            return true;
        }
    } elseif (is_staff_logged_in()) {
        if (isset($GLOBALS['current_user'])) {
            $direction = $GLOBALS['current_user']->direction;
        } else {
            $CI->db->select('direction')->from(db_prefix() . 'staff')->where('staffid', get_staff_user_id());
            $direction = $CI->db->get()->row()->direction;
        }

        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_admin') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == false) {
        if (get_option('rtl_support_admin') == 1) {
            return true;
        }
    }

    return false;
}

/**
 * init vendor area assets.
 */
function init_vendor_area_assets()
{
    // Used by themes to add assets
    hooks()->do_action('app_vendor_assets');

    hooks()->do_action('app_client_assets_added');
}

/**
 * { register theme vendor assets hook }
 *
 * @param      <type>   $function  The function
 *
 * @return     boolean  
 */
function register_theme_vendor_assets_hook($function)
{
    if (hooks()->has_action('app_vendor_assets', $function)) {
        return false;
    }

    return hooks()->add_action('app_vendor_assets', $function, 1);
}

/**
 * { app customers head }
 *
 * @param      <type>  $language  The language
 */
function app_vendor_head($language = null)
{
    // $language param is deprecated
    if (is_null($language)) {
        $language = $GLOBALS['language'];
    }

    if (file_exists(FCPATH . 'assets/css/custom.css')) {
        echo '<link href="' . base_url('assets/css/custom.css') . '" rel="stylesheet" type="text/css" id="custom-css">' . PHP_EOL;
    }

    hooks()->do_action('app_vendor_head');
}

/**
 * { app theme head hook }
 */
function app_theme_vendor_head_hook()
{
    $CI = &get_instance();
    ob_start();
    echo get_custom_fields_hyperlink_js_function();

    if (
        get_option('use_recaptcha_customers_area') == 1
        && get_option('recaptcha_secret_key') != ''
        && get_option('recaptcha_site_key') != ''
    ) {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    $isRTL = (is_rtl_pur(true) ? 'true' : 'false');

    $locale = get_locale_key($GLOBALS['language']);

    $maxUploadSize = file_upload_max_size();

    $date_format = get_option('dateformat');
    $date_format = explode('|', $date_format);
    $date_format = $date_format[0]; ?>
    <script>
        <?php if (is_staff_logged_in()) {
        ?>
            var admin_url = '<?php echo admin_url(); ?>';
        <?php
        } ?>

        var site_url = '<?php echo site_url(''); ?>',
            app = {},
            cfh_popover_templates = {};

        app.isRTL = '<?php echo pur_html_entity_decode($isRTL); ?>';
        app.is_mobile = '<?php echo is_mobile(); ?>';
        app.months_json = '<?php echo json_encode([_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')]); ?>';

        app.browser = "<?php echo strtolower($CI->agent->browser()); ?>";
        app.max_php_ini_upload_size_bytes = "<?php echo pur_html_entity_decode($maxUploadSize); ?>";
        app.locale = "<?php echo pur_html_entity_decode($locale); ?>";

        app.options = {
            calendar_events_limit: "<?php echo get_option('calendar_events_limit'); ?>",
            calendar_first_day: "<?php echo get_option('calendar_first_day'); ?>",
            tables_pagination_limit: "<?php echo get_option('tables_pagination_limit'); ?>",
            enable_google_picker: "<?php echo get_option('enable_google_picker'); ?>",
            google_client_id: "<?php echo get_option('google_client_id'); ?>",
            google_api: "<?php echo get_option('google_api_key'); ?>",
            default_view_calendar: "<?php echo get_option('default_view_calendar'); ?>",
            timezone: "<?php echo get_option('default_timezone'); ?>",
            allowed_files: "<?php echo get_option('allowed_files'); ?>",
            date_format: "<?php echo pur_html_entity_decode($date_format); ?>",
            time_format: "<?php echo get_option('time_format'); ?>",
        };

        app.lang = {
            file_exceeds_maxfile_size_in_form: "<?php echo _l('file_exceeds_maxfile_size_in_form'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            file_exceeds_max_filesize: "<?php echo _l('file_exceeds_max_filesize'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            validation_extension_not_allowed: "<?php echo _l('validation_extension_not_allowed'); ?>",
            sign_document_validation: "<?php echo _l('sign_document_validation'); ?>",
            dt_length_menu_all: "<?php echo _l('dt_length_menu_all'); ?>",
            drop_files_here_to_upload: "<?php echo _l('drop_files_here_to_upload'); ?>",
            browser_not_support_drag_and_drop: "<?php echo _l('browser_not_support_drag_and_drop'); ?>",
            confirm_action_prompt: "<?php echo _l('confirm_action_prompt'); ?>",
            datatables: <?php echo json_encode(get_datatables_language_array()); ?>,
            discussions_lang: <?php echo json_encode(get_project_discussions_language_array()); ?>,
        };
        window.addEventListener('load', function() {
            custom_fields_hyperlink();
        });
    </script>
<?php

    _do_clients_area_deprecated_js_vars($date_format, $locale, $maxUploadSize, $isRTL);

    $contents = ob_get_contents();
    ob_end_clean();
    echo pur_html_entity_decode($contents);
}

/**
 * Get customer id by passed contact id
 * @param  mixed $id
 * @return mixed
 */
function get_user_id_by_contact_id_pur($id)
{
    $CI = &get_instance();

    $userid = $CI->app_object_cache->get('user-id-by-contact-id-' . $id);
    if (!$userid) {
        $CI->db->select('userid')
            ->where('id', $id);
        $client = $CI->db->get(db_prefix() . 'pur_contacts')->row();

        if ($client) {
            $userid = $client->userid;
            $CI->app_object_cache->add('user-id-by-contact-id-' . $id, $userid);
        }
    }

    return $userid;
}

/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function get_group_name_pur($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items_groups')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems_groups')->result_array();
    }
}

/**
 * { check purchase order restrictions }
 *
 * @param        $id     The identifier
 * @param        $hash   The hash
 */
function check_pur_order_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if (!$hash || !$id) {
        show_404();
    }


    $pur_order = $CI->purchase_model->get_pur_order($id);
    if (!$pur_order || ($pur_order->hash != $hash)) {
        show_404();
    }
}

/**
 * { check purchase request restrictions }
 *
 * @param        $id     The identifier
 * @param        $hash   The hash
 */
function check_pur_request_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    if (!$hash || !$id) {
        show_404();
    }


    $pur_request = $CI->purchase_model->get_purchase_request($id);
    if (!$pur_request || ($pur_request->hash != $hash)) {
        show_404();
    }
}


function get_pur_order_by_client($client)
{
    $CI = &get_instance();
    $CI->db->where('find_in_set(' . $client . ', clients)');
    return $CI->db->get(db_prefix() . 'pur_orders')->result_array();
}

/**
 * { handle purchase contract file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean 
 */
function handle_pur_contract_file($id)
{

    $path           = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $id . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name']     = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type']     = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error']    = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size']     = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, 'pur_contract', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Is vendor logged in
 * @return boolean
 */
function is_vendor_logged_in()
{
    return get_instance()->session->has_userdata('vendor_logged_in');
}

/**
 * Return logged vendor User ID from session
 * @return mixed
 */
function get_vendor_user_id()
{
    if (!is_vendor_logged_in()) {
        return false;
    }

    return get_instance()->session->userdata('vendor_user_id');
}

/**
 * Get contact user id
 * @return mixed
 */
function get_vendor_contact_user_id()
{
    $CI = &get_instance();
    if (!$CI->session->has_userdata('vendor_contact_user_id')) {
        return false;
    }

    return $CI->session->userdata('vendor_contact_user_id');
}

/**
 * Check if contact id passed is primary contact
 * If you dont pass $contact_id the current logged in contact will be checked
 * @param  string  $contact_id
 * @return boolean
 */
function is_primary_contact_pur($contact_id = '')
{
    if (!is_numeric($contact_id)) {
        $contact_id = get_vendor_contact_user_id();
    }

    if (total_rows(db_prefix() . 'pur_contacts', ['id' => $contact_id, 'is_primary' => 1]) > 0) {
        return true;
    }

    return false;
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_estimate_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                ];
                $CI->misc_model->add_attachment_to_database($id, 'pur_estimate', $attachment);

                return true;
            }
        }
    }

    return false;
}

function get_vendor_cate_name_by_id($id)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $category = $CI->purchase_model->get_vendor_category($id);
    if ($category) {
        return $category->category_name;
    } else {
        return '';
    }
}

/**
 * Gets the vendor category html.
 *
 * @param      string  $category  The category
 */
function get_vendor_category_html($category)
{
    $rs = '';
    if ($category != '') {
        $cates = explode(',', $category);
        foreach ($cates as $cat) {
            $cat_name = get_vendor_cate_name_by_id($cat);
            if ($cat_name != '') {
                $rs .= '<span class="label label-tag">' . $cat_name . '</span>';
            }
        }
    }
    return $rs;
}

/**
 * { department pur request name }
 *
 * @param       $dpm    The dpm
 *
 * @return     string  
 */
function department_pur_request_name($dpm)
{
    $CI = &get_instance();
    $CI->load->model('departments_model');
    $department = $CI->departments_model->get($dpm);
    $name_rs = '';
    if ($department) {
        $name_repl = str_replace(' ', '', $department->name);
        $name_rs = strtoupper($name_repl);
    }

    return $name_rs;
}

/**
 * Gets the po html by pur request.
 *
 * @param  $pur_request  The pur request
 */
function get_po_html_by_pur_request($pur_request)
{
    $CI = &get_instance();
    $CI->db->where('pur_request', $pur_request);
    $list = $CI->db->get(db_prefix() . 'pur_orders')->result_array();
    $rs = '';
    $count = 0;
    if (count($list) > 0) {
        foreach ($list as $li) {
            $rs .= '<a href="' . admin_url('purchase/purchase_order/' . $li['id']) . '" ><span class="label label-tag mbot5">' . $li['pur_order_number'] . '</span></a>&nbsp;';
        }
    }
    return $rs;
}

/**
 * Gets the pur contract number.
 *
 * @param        $id     The identifier
 *
 * @return       The pur contract number.
 */
function get_pur_contract_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $contract = $CI->db->get(db_prefix() . 'pur_contracts')->row();
    if ($contract) {
        return $contract->contract_number;
    } else {
        return '';
    }
}

/**
 * Gets the pur invoice number.
 *
 * @param        $id     The identifier
 *
 * @return     string  The pur invoice number.
 */
function get_pur_invoice_number($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $inv = $CI->db->get(db_prefix() . 'pur_invoices')->row();
    if ($inv) {
        return $inv->invoice_number;
    } else {
        return '';
    }
}

/**
 * { purchase invoice left to pay }
 *
 * @param      <type>   $id     The purchase order
 *
 * @return     integer  ( purchase order left to pay )
 */
function purinvoice_left_to_pay($id)
{
    $CI = &get_instance();


    $CI->db->select('total')
        ->where('id', $id);
    $invoice_total = $CI->db->get(db_prefix() . 'pur_invoices')->row()->total;


    $CI->db->where('pur_invoice', $id);
    $CI->db->where('approval_status', 2);
    $payments = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();

    $debits  = $CI->purchase_model->get_applied_invoice_debits($id);

    $payments = array_merge($payments, $debits);


    $totalPayments = 0;


    foreach ($payments as $payment) {

        $totalPayments += $payment['amount'];
    }

    return ($invoice_total - $totalPayments);
}

/**
 * Gets the payment mode name by identifier.
 *
 * @param        $id     The identifier
 *
 * @return     string  The payment mode name by identifier.
 */
function get_payment_mode_name_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $mode = $CI->db->get(db_prefix() . 'payment_modes')->row();
    if ($mode) {
        return $mode->name;
    } else {
        return '';
    }
}

/**
 * Gets the payment request status by inv.
 *
 * @param        $id     The identifier
 *
 * @return     string  The payment request status by inv.
 */
function get_payment_request_status_by_inv($id)
{
    $CI = &get_instance();
    $CI->db->where('pur_invoice', $id);
    $payments = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();
    $status = '';
    $class = '';
    if (count($payments) > 0) {
        $status = 'created';
        $class = 'info';
        $CI->db->where('pur_invoice', $id);
        $CI->db->where('approval_status', 2);
        $payments_approved = $CI->db->get(db_prefix() . 'pur_invoice_payment')->result_array();
        if (count($payments_approved)) {
            $status = 'approved';
            $class = 'success';
        }
    } else {
        $status = 'blank';
        $class = 'warning';
    }

    if ($status != '') {
        return '<span class="label label-' . $class . ' s-status invoice-status-3">' . _l($status) . '</span>';
    } else {
        return '';
    }
}

/**
 * { handle pur invoice file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_pur_invoice_file($id)
{
    $type = 'pur_invoice';
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $id . '/';
    $CI = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name'] = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type'] = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error'] = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size'] = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, $type, $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * { handle send quotation upload file  }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function handle_send_quotation($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * { handle send quotation upload file  }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean   
 */
function handle_send_po($id)
{
    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/' . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, str_replace(" ", "_", $_FILES['attachment']['name']));
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return true;
            }
        }
    }

    return false;
}

if (!function_exists('add_purchase_email_templates')) {
    /**
     * Init appointly email templates and assign languages
     * @return void
     */
    function add_purchase_email_templates()
    {
        $CI = &get_instance();

        $data['purchase_templates'] = $CI->emails_model->get(['type' => 'purchase_order', 'language' => 'english']);

        $CI->load->view('purchase/email_templates', $data);
    }
}

/*
* php delete function that deals with directories recursively
*/
function delete_files_pur($target)
{
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
        foreach ($files as $file) {
            if ($file != $target . 'signature\\' && is_dir($file)) {
                delete_dir($file);
            }
        }
    }
}


/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_po_logo()
{
    if (isset($_FILES['po_logo']['name']) && $_FILES['po_logo']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . '0/';
        // Get the temp file path
        $tmpFilePath = $_FILES['po_logo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['po_logo']['name']);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filename = 'po_logo_' . time() . '.' . $extension;
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = &get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['po_logo']['type'],
                ];
                $CI->misc_model->add_attachment_to_database(0, 'po_logo', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the po logo.
 */
function get_po_logo($width = 120, $class = '', $type = 'pdf')
{
    $CI           = &get_instance();
    $CI->db->where('rel_id', 0);
    $CI->db->where('rel_type', 'po_logo');
    $logo = $CI->db->get(db_prefix() . 'files')->result_array();

    $logoUrl                   = '';
    if (count($logo) > 0) {
        $logoUrl = APP_MODULES_PATH . 'purchase/uploads/po_logo/0/' . $logo[0]['file_name'];
        if ($type != 'pdf') {
            $logoUrl = base_url(PURCHASE_PATH . 'po_logo/0/' . $logo[0]['file_name']);
        }
    }

    $logoImage = '';
    if ($logoUrl != '') {
        $logoImage = '<img style="width:' . $width . 'px" src="' . $logoUrl . '" class="' . $class . '">';
    }


    return  $logoImage;
}

/**
 * { total inv value by pur order }
 *
 * @param        $pur_order  The pur order
 *
 * @return     int     ( description of the return value )
 */
function total_inv_value_by_pur_order($pur_order)
{
    $CI           = &get_instance();
    $CI->db->where('pur_order', $pur_order);
    $list_inv = $CI->db->get(db_prefix() . 'pur_invoices')->result_array();
    $rs = 0;
    if (count($list_inv) > 0) {
        foreach ($list_inv as $inv) {
            $rs += $inv['total'];
        }
    }
    return $rs;
}

/**
 * Gets the item identifier by description.
 *
 * @param        $des       The description
 * @param        $long_des  The long description
 *
 * @return     string  The item identifier by description.
 */
function get_item_id_by_des($des, $long_des = '')
{
    $CI           = &get_instance();
    $CI->db->where('description', $des);

    $item = $CI->db->get(db_prefix() . 'items')->row();

    if ($item) {
        return $item->id;
    }
    return '';
}

/**
 * { purorder inv left to pay }
 *
 * @param        $pur_order  The pur order
 */
function purorder_inv_left_to_pay($pur_order)
{
    $CI           = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $list_payment = $CI->purchase_model->get_inv_payment_purchase_order($pur_order);
    $po = $CI->purchase_model->get_pur_order($pur_order);

    $list_applied_debit = $CI->purchase_model->get_inv_debit_purchase_order($pur_order);
    $paid = 0;
    foreach ($list_payment as $payment) {
        if ($payment['approval_status'] == 2) {
            $paid += $payment['amount'];
        }
    }

    foreach ($list_applied_debit as $debit) {
        $paid += $debit['amount'];
    }

    if ($po) {
        return $po->total - $paid;
    }
    return 0;
}

/**
 * { row purcahse options exist }
 *
 * @param      <type>   $name   The name
 *
 * @return     integer  ( 1 or 0 )
 */
function row_purchase_tbl_options_exist($name)
{
    $CI = &get_instance();
    $i = count($CI->db->query('Select * from ' . db_prefix() . 'options where name = ' . $name)->result_array());
    if ($i == 0) {
        return 0;
    }
    if ($i > 0) {
        return 1;
    }
}

/**
 * Gets the base currency pur.
 *
 * @return       The base currency pur.
 */
function get_base_currency_pur()
{
    $CI           = &get_instance();
    $CI->load->model('currencies_model');
    $base_currency = $CI->currencies_model->get_base_currency();
    return $base_currency;
}

/**
 * Gets the arr vendors by pr.
 *
 * @param        $pur_request  The pur request
 *
 * @return     array   The arr vendors by pr.
 */
function get_arr_vendors_by_pr($pur_request)
{
    $CI           = &get_instance();
    $CI->load->model('purchase/purchase_model');

    $CI->db->where('pur_request', $pur_request);
    $quotes = $CI->db->get(db_prefix() . 'pur_estimates')->result_array();
    $arr_vendor = [];
    $arr_vendor_rs = [];
    if (count($quotes) > 0) {
        foreach ($quotes as $quote) {
            if (!in_array($quote['vendor'], $arr_vendor)) {
                $arr_vendor[] = $quote['vendor'];
                $arr_vendor_rs[] = $CI->purchase_model->get_vendor($quote['vendor']);
            }
        }
    }
    return $arr_vendor_rs;
}

/**
 * Gets the quotations by pur request.
 */
function get_quotations_by_pur_request($pur_request)
{
    $CI           = &get_instance();

    $CI->db->where('pur_request', $pur_request);
    $quotes = $CI->db->get(db_prefix() . 'pur_estimates')->result_array();
    return $quotes;
}

/**
 * Gets the item detail in quote.
 *
 * @param        $item           The item
 * @param        $pur_estimates  The pur estimates
 */
function get_item_detail_in_quote($item, $pur_estimates)
{
    $CI           = &get_instance();
    $CI->db->where('pur_estimate', $pur_estimates);
    $CI->db->where('item_code', $item);
    $item_row = $CI->db->get(db_prefix() . 'pur_estimate_detail')->row();
    return $item_row;
}

/**
 * Gets the purchase request item taxes.
 *
 * @param       $itemid  The itemid
 *
 * @return       The invoice item taxes.
 */
function get_debit_note_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'debit_note');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Function that format credit note number based on the prefix option and the credit note number
 * @param  mixed $id credit note id
 * @return string
 */
function format_debit_note_number($id)
{
    $CI = &get_instance();
    $CI->db->select('date,number,prefix,number_format')
        ->from(db_prefix() . 'pur_debit_notes')
        ->where('id', $id);
    $debit_note = $CI->db->get()->row();

    if (!$debit_note) {
        return '';
    }

    $number = sales_number_format($debit_note->number, $debit_note->number_format, $debit_note->prefix, $debit_note->date);

    return $number;
}

/**
 * Format debit note status
 * @param  mixed  $status credit note current status
 * @param  boolean $text   to return text or with applied styles
 * @return string
 */
function format_debit_note_status($status, $text = false)
{
    $CI = &get_instance();
    if (!class_exists('purchase_model')) {
        $CI->load->model('purchase/purchase_model');
    }

    $statuses    = $CI->purchase_model->get_debit_note_statuses();
    $statusArray = false;
    foreach ($statuses as $s) {
        if ($s['id'] == $status) {
            $statusArray = $s;

            break;
        }
    }

    if (!$statusArray) {
        return $status;
    }

    if ($text) {
        return $statusArray['name'];
    }

    $style = 'border: 1px solid ' . $statusArray['color'] . ';color:' . $statusArray['color'] . ';';
    $class = 'label s-status';

    return '<span class="' . $class . '" style="' . $style . '">' . $statusArray['name'] . '</span>';
}

/**
 * Format vendor address info
 * @param  object  $data        vendor object from database
 * @param  string  $for         where this format will be used? Eq statement invoice etc
 * @param  string  $type        billing/shipping
 * @param  boolean $companyLink company link to be added on vendor company/name, this is used in admin area only
 * @return string
 */
function format_vendor_info($data, $for, $type, $companyLink = false)
{
    $format   = get_option('customer_info_format');
    $vendorId = '';

    if ($for == 'statement') {
        $vendorId = $data->userid;
    } elseif ($type == 'billing') {
        $vendorId = $data->vendorid;
    }

    $filterData = [
        'data'         => $data,
        'for'          => $for,
        'type'         => $type,
        'client_id'    => $vendorId,
        'company_link' => $companyLink,
    ];

    $companyName = '';
    if ($for == 'statement') {
        $companyName = get_vendor_company_name($vendorId);
    } elseif ($type == 'billing') {
        $companyName = $data->vendor->company;
    }

    $acceptsPrimaryContactDisplay = ['debit_note'];

    $street  = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_street'} : '';
    $city    = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_city'} : '';
    $state   = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_state'} : '';
    $zipCode = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_zip'} : '';

    $countryCode = '';
    $countryName = '';

    if ($country = in_array($type, ['billing', 'shipping']) ? get_country($data->{$type . '_country'}) : '') {
        $countryCode = $country->iso2;
        $countryName = $country->short_name;
    }

    $phone = '';
    if ($for == 'statement' && isset($data->phonenumber)) {
        $phone = $data->phonenumber;
    } elseif ($type == 'billing' && isset($data->client->phonenumber)) {
        $phone = $data->client->phonenumber;
    }

    $vat = '';
    if ($for == 'statement' && isset($data->vat)) {
        $vat = $data->vat;
    } elseif ($type == 'billing' && isset($data->client->vat)) {
        $vat = $data->client->vat;
    }

    if ($companyLink && (!isset($data->deleted_customer_name) ||
        (isset($data->deleted_customer_name) &&
            empty($data->deleted_customer_name)))) {
        $companyName = '<a href="' . admin_url('purchase/vendor/' . $vendorId) . '" target="_blank"><b>' . $companyName . '</b></a>';
    } elseif ($companyName != '') {
        $companyName = '<b>' . $companyName . '</b>';
    }

    $format = _info_format_replace('company_name', $companyName, $format);
    $format = _info_format_replace('customer_id', $vendorId, $format);
    $format = _info_format_replace('street', $street, $format);
    $format = _info_format_replace('city', $city, $format);
    $format = _info_format_replace('state', $state, $format);
    $format = _info_format_replace('zip_code', $zipCode, $format);
    $format = _info_format_replace('country_code', $countryCode, $format);
    $format = _info_format_replace('country_name', $countryName, $format);
    $format = _info_format_replace('phone', $phone, $format);
    $format = _info_format_replace('vat_number', $vat, $format);
    $format = _info_format_replace('vat_number_with_label', $vat == '' ? '' : _l('client_vat_number') . ': ' . $vat, $format);


    // Remove multiple white spaces
    $format = preg_replace('/\s+/', ' ', $format);
    $format = trim($format);

    return hooks()->apply_filters('customer_info_text', $format, $filterData);
}

/**
 * Prepare general debit note pdf
 * @param  object $debit_note Debit note as object with all necessary fields
 * @return mixed object
 */
function debit_note_pdf($debit_note)
{
    return app_pdf('debit_note', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Debit_note_pdf'), $debit_note);
}

/**
 * Return debit note status color RGBA for pdf
 * @param  mixed $status_id current credit note status id
 * @return string
 */
function debit_note_status_color_pdf($status_id)
{
    $statusColor = '';

    if ($status_id == 1) {
        $statusColor = '3, 169, 244';
    } elseif ($status_id == 2) {
        $statusColor = '132, 197, 41';
    } else {
        // Status VOID
        $statusColor = '119, 119, 119';
    }

    return $statusColor;
}

/**
 * Check if debit can be applied to invoice based on the invoice status
 * @param  mixed $status_id invoice status id
 * @return boolean
 */
function debits_can_be_applied_to_invoice($status)
{
    if (in_array($status, ["unpaid", "partially_paid"])) {
        return true;
    }
    return false;
}

/**
 * Prepare customer statement pdf
 * @param  object $statement statement
 * @return mixed
 */
function purchase_statement_pdf($statement)
{
    return app_pdf('vendor_statement', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Vendor_statement_pdf'), $statement);
}

/**
 * purchase get staff id hr permissions
 * @return [type] 
 */
function purchase_get_staff_id_permissions()
{
    $CI = &get_instance();
    $array_staff_id = [];
    $index = 0;

    $str_permissions = '';
    foreach (list_purchase_permisstion() as $per_key =>  $per_value) {
        if (strlen($str_permissions) > 0) {
            $str_permissions .= ",'" . $per_value . "'";
        } else {
            $str_permissions .= "'" . $per_value . "'";
        }
    }


    $sql_where = "SELECT distinct staff_id FROM " . db_prefix() . "staff_permissions
    where feature IN (" . $str_permissions . ")
    ";

    $staffs = $CI->db->query($sql_where)->result_array();

    if (count($staffs) > 0) {
        foreach ($staffs as $key => $value) {
            $array_staff_id[$index] = $value['staff_id'];
            $index++;
        }
    }
    return $array_staff_id;
}


/**
 * list purchase permisstion
 * @return [type] 
 */
function list_purchase_permisstion()
{
    $hr_profile_permissions = [];
    $hr_profile_permissions[] = 'purchase_items';
    $hr_profile_permissions[] = 'purchase_vendors';
    $hr_profile_permissions[] = 'purchase_vendor_items';
    $hr_profile_permissions[] = 'purchase_request';
    $hr_profile_permissions[] = 'purchase_quotations';
    $hr_profile_permissions[] = 'purchase_orders';
    $hr_profile_permissions[] = 'purchase_order_return';
    $hr_profile_permissions[] = 'purchase_contracts';
    $hr_profile_permissions[] = 'purchase_invoices';
    $hr_profile_permissions[] = 'purchase_reports';
    $hr_profile_permissions[] = 'purchase_debit_notes';
    $hr_profile_permissions[] = 'purchase_settings';
    $hr_profile_permissions[] = 'purchase_order_change_approve_status';
    $hr_profile_permissions[] = 'purchase_estimate_change_approve_status';
    $hr_profile_permissions[] = 'purchase_request_change_approve_status';


    return $hr_profile_permissions;
}

/**
 * purchase get staff id dont permissions
 * @return [type] 
 */
function purchase_get_staff_id_dont_permissions()
{
    $CI = &get_instance();

    $CI->db->where('admin != ', 1);

    if (count(purchase_get_staff_id_permissions()) > 0) {
        $CI->db->where_not_in('staffid', purchase_get_staff_id_permissions());
    }
    return $CI->db->get(db_prefix() . 'staff')->result_array();
}

function check_valid_number_with_setting($number)
{
    $decimal_separator = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');

    $decimal_separator_index = strpos($number, $decimal_separator);
    $thousand_separator_index = strpos($number, $thousand_separator);

    if ($decimal_separator_index == false || $thousand_separator_index == false) {
        return true;
    }

    if ($decimal_separator_index <= $thousand_separator_index) {
        return false;
    }

    return true;
}

function pur_convert_item_taxes($tax, $tax_rate, $tax_name)
{
    /*taxrate taxname
    5.00    TAX5
    id      rate        name
    2|1 ; 6.00|10.00 ; TAX5|TAX10%*/
    $CI           = &get_instance();
    $taxes = [];
    if ($tax != null && strlen($tax) > 0) {
        $arr_tax_id = explode('|', $tax);
        if ($tax_name != null && strlen($tax_name) > 0) {
            $arr_tax_name = explode('|', $tax_name);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_name as $key => $value) {
                $taxes[]['taxname'] = $value . '|' .  $arr_tax_rate[$key];
            }
        } elseif ($tax_rate != null && strlen($tax_rate) > 0) {
            $CI->load->model('purchase/purchase_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->purchase_model->get_tax_name($value);
                if (isset($arr_tax_rate[$key])) {
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                } else {
                    $taxes[]['taxname'] = $_tax_name . '|' .  $CI->purchase_model->tax_rate_by_id($value);
                }
            }
        } else {
            $CI->load->model('purchase/purchase_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->purchase_model->get_tax_name($value);
                $_tax_rate = $CI->purchase_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            }
        }
    }

    return $taxes;
}

/**
 * pur get unit name
 * @param  boolean $id 
 * @return [type]      
 */
function pur_get_unit_name($id = false)
{
    $CI           = &get_instance();
    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);

        $unit = $CI->db->get(db_prefix() . 'ware_unit_type')->row();
        if ($unit) {
            return $unit->unit_name;
        }
        return '';
    }
}

/**
 * wh get item variatiom
 * @param  [type] $id 
 * @return [type]     
 */
function pur_get_item_variatiom($id)
{
    $CI           = &get_instance();

    $CI->db->where('id', $id);
    $item_value = $CI->db->get(db_prefix() . 'items')->row();

    $name = '';
    if ($item_value) {
        $CI->load->model('purchase/purchase_model');
        $new_item_value = $CI->purchase_model->row_item_to_variation($item_value);

        $name .= $item_value->commodity_code . '_' . $new_item_value->new_description;
    }

    return $name;
}

/**
 * Function that return invoice item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function pur_get_invoice_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'invoice');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;


    return $taxes;
}

/**
 * { handle item password file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean
 */
function handle_vendor_item_attachment($id)
{

    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/vendor_items/' . $id . '/';
    $CI = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)
    ) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name'] = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type'] = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error'] = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size'] = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['attachments']['error'][$i])
                    || !_upload_extension_allowed($_FILES['attachments']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $_FILES['attachments']['type'][$i],
                    ];

                    $CI->misc_model->add_attachment_to_database($id, 'vendor_items', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * { vendor item images }
 *
 * @param        $item_id  The item identifier
 */
function vendor_item_images($item_id)
{
    $CI = &get_instance();

    $CI->db->order_by('dateadded', 'desc');
    $CI->db->where('rel_id', $item_id);
    $CI->db->where('rel_type', 'vendor_items');

    return $CI->db->get(db_prefix() . 'files')->result_array();
}

/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function pur_get_tax_rate($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tbltaxes')->result_array();
    }
}

/**
 * Purchase get currency name symbol
 * @param  [type] $id     
 * @param  string $column 
 * @return [type]         
 */
function pur_get_currency_name_symbol($id, $column = '')
{
    $CI   = &get_instance();
    $currency_value = '';

    if ($column == '') {
        $column = 'name';
    }

    $CI->db->select($column);
    $CI->db->from(db_prefix() . 'currencies');
    $CI->db->where('id', $id);
    $currency = $CI->db->get()->row();
    if ($currency) {
        $currency_value = $currency->$column;
    }

    return $currency_value;
}

/**
 * get currency rate
 * @param  [type] $from
 * @param  [type] $to
 * @return [type]           
 */
function pur_get_currency_rate($from, $to)
{
    $CI   = &get_instance();
    if ($from == $to) {
        return 1;
    }

    $amount_after_convertion = 1;

    $CI->db->where('from_currency_name', strtoupper($from));
    $CI->db->where('to_currency_name', strtoupper($to));
    $currency_rates = $CI->db->get(db_prefix() . 'currency_rates')->row();

    if ($currency_rates) {
        $amount_after_convertion = $currency_rates->to_currency_rate;
    }

    return $amount_after_convertion;
}


/**
 * { pur get currency by id }
 *
 * @param        $id     The identifier
 */
function pur_get_currency_by_id($id)
{
    $CI   = &get_instance();

    $CI->db->where('id', $id);
    return  $CI->db->get(db_prefix() . 'currencies')->row();
}

/**
 * Gets the vendor currency.
 *
 * @param        $vendor_id  The vendor identifier
 */
function get_vendor_currency($vendor_id)
{
    $CI   = &get_instance();

    $CI->db->where('userid', $vendor_id);
    $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();

    if ($vendor) {
        return $vendor->default_currency;
    }
    return 0;
}

function get_invoice_currency_id($invoice_id)
{
    $CI   = &get_instance();
    $CI->db->where('id', $invoice_id);
    $invoice = $CI->db->get(db_prefix() . 'pur_invoices')->row();
    if ($invoice) {
        return $invoice->currency;
    }
    return 0;
}

/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_vendor_po_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $purorder . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        _file_attachments_index_fix('file');
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['file']['error'][$i])
                    || !_upload_extension_allowed($_FILES['file']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    if (is_image($newFilePath)) {
                        create_img_thumb($newFilePath, $filename);
                    }

                    if ($customer_upload == true) {
                        $attachment[0]['staffid']          = 0;
                        $attachment[0]['contact_id']       = get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'pur_order', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_vendor_estimate_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $purorder . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        _file_attachments_index_fix('file');
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['file']['error'][$i])
                    || !_upload_extension_allowed($_FILES['file']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    if (is_image($newFilePath)) {
                        create_img_thumb($newFilePath, $filename);
                    }

                    if ($customer_upload == true) {
                        $attachment[0]['staffid']          = 0;
                        $attachment[0]['contact_id']       = get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'pur_estimate', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * wh check approval setting
 * @param  integer $type 
 * @return [type]       
 */
function pur_check_approval_setting($type)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');

    $check_appr = $CI->purchase_model->get_approve_setting($type);

    return $check_appr;
}

/**
 * Gets the warehouse option.
 *
 * @param      <type>        $name   The name
 *
 * @return     array|string  The warehouse option.
 */
function get_purchase_option_v2($name)
{
    $CI = &get_instance();
    $options = [];
    $val  = '';
    $name = trim($name);


    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('value');
        $CI->db->where('name', $name);
        $row = $CI->db->get(db_prefix() . 'options')->row();
        if ($row) {
            $val = $row->value;
        }
    } else {
        $val = $options[$name];
    }

    return $val;
}

/**
 * get commodity name
 * @param  integer $id
 * @return array or row
 */
function pur_get_commodity_name($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems')->result_array();
    }
}

/**
 * wh render taxes html
 * @param  [type] $item_tax 
 * @param  [type] $width    
 * @return [type]           
 */
function pur_render_taxes_html($item_tax, $width)
{
    $itemHTML = '';
    $itemHTML .= '<td align="right" width="' . $width . '%">';

    if (is_array($item_tax) && isset($item_tax)) {
        if (count($item_tax) > 0) {
            foreach ($item_tax as $tax) {

                $item_tax = '';
                if (get_option('remove_tax_name_from_item_table') == false || multiple_taxes_found_for_item($item_tax)) {
                    $tmp      = explode('|', $tax['taxname']);
                    $item_tax = $tmp[0] . ' ' . app_format_number($tmp[1]) . '%<br />';
                } else {
                    $item_tax .= app_format_number($tax['taxrate']) . '%';
                }
                $itemHTML .= $item_tax;
            }
        } else {
            $itemHTML .=  app_format_number(0) . '%';
        }
    }
    $itemHTML .= '</td>';

    return $itemHTML;
}

/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function pur_format_approve_status($status, $text = false, $clean = false)
{

    $status_name = '';
    if ($status == 1) {
        $status_name = _l('purchase_draft');
    } elseif ($status == 2) {
        $status_name = _l('purchase_approved');
    } elseif ($status == 3) {
        $status_name = _l('pur_rejected');
    } elseif ($status == 4) {
        $status_name = _l('pur_canceled');
    }

    if ($clean == true) {
        return $status_name;
    }

    $style = '';
    $class = '';
    if ($text == false) {
        if ($status == 1) {
            $class = 'label label-primary';
        } elseif ($status == 2) {
            $class = 'label label-success';
        } elseif ($status == 3) {
            $class = 'label label-warning';
        } elseif ($status == 4) {
            $class = 'label label-danger';
        }
    } else {
        if ($status == 1) {
            $class = 'label text-info';
        } elseif ($status == 2) {
            $class = 'label text-success';
        } elseif ($status == 3) {
            $class = 'label text-warning';
        } elseif ($status == 4) {
            $class = 'label text-danger';
        }
    }

    return '<span class="' . $class . '" >' . $status_name . '</span>';
}


/**
 * Client attachments
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_vendor_pr_attachments_upload($id, $customer_upload = false, $purorder = '')
{
    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_request/' . $purorder . '/';
    $CI            = &get_instance();
    $totalUploaded = 0;

    if (
        isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
    ) {
        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name']     = [$_FILES['file']['name']];
            $_FILES['file']['type']     = [$_FILES['file']['type']];
            $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
            $_FILES['file']['error']    = [$_FILES['file']['error']];
            $_FILES['file']['size']     = [$_FILES['file']['size']];
        }

        _file_attachments_index_fix('file');
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES['file']['error'][$i])
                    || !_upload_extension_allowed($_FILES['file']['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'][$i],
                    ];

                    if (is_image($newFilePath)) {
                        create_img_thumb($newFilePath, $filename);
                    }

                    if ($customer_upload == true) {
                        $attachment[0]['staffid']          = 0;
                        $attachment[0]['contact_id']       = get_vendor_contact_user_id();
                        $attachment['visible_to_customer'] = 1;
                    }

                    $CI->misc_model->add_attachment_to_database($purorder, 'pur_request', $attachment);
                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

/**
 * Gets the total order return refunded.
 *
 * @param        $order_return  The order return
 *
 * @return     int     The total order return refunded.
 */
function get_total_order_return_refunded($order_return)
{
    $CI            = &get_instance();
    $CI->db->where('order_return_id', $order_return);
    $refunds = $CI->db->get(db_prefix() . 'wh_order_returns_refunds')->result_array();

    $total_refunded = 0;
    if (count($refunds) > 0) {
        foreach ($refunds as $key => $refund) {
            $total_refunded += $refund['amount'];
        }
    }

    return $total_refunded;
}

function get_order_return_remaining_refund($order_return)
{
    $CI            = &get_instance();
    $CI->load->model('purchase/purchase_model');

    $order = $CI->purchase_model->get_order_return($order_return);

    $vendor = $CI->purchase_model->get_vendor($order->company_id);

    $remaining_refund = 0;
    $total_refunded = get_total_order_return_refunded($order_return);

    $remaining_refund = $order->total_after_discount - $total_refunded;

    return $remaining_refund;
}

function get_object_comment($rel_id, $rel_type)
{
    $CI            = &get_instance();
    $table = '';
    if ($rel_type == 'pur_order') {
        $table = db_prefix() . 'pur_orders';
    } else if ($rel_type == 'pur_quotation') {
        $table = db_prefix() . 'pur_estimates';
    } else if ($rel_type == 'pur_contract') {
        $table = db_prefix() . 'pur_contracts';
    } else if ($rel_type == 'pur_invoice') {
        $table = db_prefix() . 'pur_invoices';
    }

    $CI->db->where('id', $rel_id);
    $object = $CI->db->get($table)->row();

    return $object;
}

/**
 * Get primary contact user id for specific customer
 * @param  mixed $userid
 * @return mixed
 */
function pur_get_primary_contact_user_id($userid)
{
    $CI = &get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->where('is_primary', 1);
    $row = $CI->db->get(db_prefix() . 'pur_contacts')->row();

    if ($row) {
        return $row->id;
    }

    return false;
}

/**
 * Gets the vendor language by email.
 */
function get_vendor_language_by_email($email)
{
    $CI = &get_instance();
    $CI->db->where('email', $email);
    $contact = $CI->db->get(db_prefix() . 'pur_contacts')->row();

    if ($contact) {
        $CI->db->where('userid', $contact->userid);
        $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();
        if ($vendor) {
            return $vendor->default_language;
        }
    }

    return '';
}

/**
 * Send contract signed notification to staff members
 *
 * @param  int $contract_id
 *
 * @return void
 */
function pur_send_contract_signed_notification_to_staff($contract_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $contract_id);
    $contract = $CI->db->get(db_prefix() . 'pur_contracts')->row();

    if (!$contract) {
        return false;
    }

    // Get creator
    $CI->db->select('staffid, email');
    $CI->db->where('staffid', $contract->add_from);
    $staff_contract = $CI->db->get(db_prefix() . 'staff')->result_array();

    $notifiedUsers = [];

    foreach ($staff_contract as $member) {
        $notified = add_notification([
            'description'     => 'not_contract_signed',
            'touserid'        => $member['staffid'],
            'fromcompany'     => 1,
            'fromuserid'      => 0,
            'link'            => 'purchase/contract/' . $contract->id,
            'additional_data' => serialize([
                '<b>' . $contract->contract_number . '</b>',
            ]),
        ]);

        if ($notified) {
            array_push($notifiedUsers, $member['staffid']);
        }
    }

    pusher_trigger_notification($notifiedUsers);
}

/**
 * Gets the vendor language.
 */
function get_vendor_language()
{
    $vendor_id = get_vendor_user_id();
    $CI = &get_instance();
    $CI->db->where('userid', $vendor_id);
    $vendor = $CI->db->get(db_prefix() . 'pur_vendor')->row();

    if ($vendor) {
        return $vendor->default_language;
    }

    return '';
}

/**
 * { pur_pur_html_entity_decode }
 *
 * @param      string  $str    The string
 *
 * @return     <string>  
 */
function pur_html_entity_decode($str)
{
    return html_entity_decode($str ?? '');
}

/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function pur_render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
    }
    $input .= '<div class="input-group date">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-regular fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}

/**
 * Gets the vendor admin list.
 *
 * @param        $staffid  The staffid
 */
function get_vendor_admin_list($staffid)
{

    $CI = &get_instance();
    $CI->db->where('staff_id', $staffid);
    $list = $CI->db->get(db_prefix() . 'pur_vendor_admin')->result_array();

    $vendor_ids = [];
    if (count($list) > 0) {
        foreach ($list as $row) {
            if (!in_array($row['vendor_id'], $vendor_ids)) {
                $vendor_ids[] = $row['vendor_id'];
            }
        }
    }

    return $vendor_ids;
}

/**
 * { pur_check_csrf_protection }
 *
 * @return     string  (  )
 */
function pur_check_csrf_protection()
{
    if (config_item('csrf_protection')) {
        return 'true';
    }
    return 'false';
}

function format_po_ship_to_info($pur_order)
{
    $html = '';
    $CI = &get_instance();

    if (!empty($pur_order->shipping_address) || !empty($pur_order->shipping_city) || !empty($pur_order->shipping_state) || !empty($pur_order->shipping_zip)) {
        $html .= '<b>' . _l('ship_to') . '</b>';
        if (!empty($pur_order->shipping_address)) {
            $html .= '<br />' . $pur_order->shipping_address;
        }
        if (!empty($pur_order->shipping_city) || !empty($pur_order->shipping_state)) {
            $html .= '<br />';
            if (!empty($pur_order->shipping_city)) {
                $html .= $pur_order->shipping_city . " ";
            }
            if (!empty($pur_order->shipping_state)) {
                $html .= $pur_order->shipping_state;
            }
        }
        if (!empty($pur_order->shipping_country) || !empty($pur_order->shipping_zip)) {
            $html .= '<br />';
            if (!empty($pur_order->shipping_country)) {
                $shipping_country = $CI->db->select('short_name')
                    ->where('country_id', $pur_order->shipping_country)
                    ->from(db_prefix() . 'countries')
                    ->get()
                    ->row();
                $html .= $shipping_country->short_name . " ";
            }
            if (!empty($pur_order->shipping_zip)) {
                $html .= $pur_order->shipping_zip;
            }
        }
    }

    return $html;
}

function format_pdf_vendor_info($vendor_id)
{
    $html = '';
    $CI = &get_instance();

    if (!empty($vendor_id)) {
        $vendor = $CI->db->select('*')
            ->where('userid', $vendor_id)
            ->from(db_prefix() . 'pur_vendor')
            ->get()
            ->row();

        if (!empty($vendor)) {
            $html .= '<b>' . _l('vendor') . '</b>';
            $html .= '<br /><b>' . $vendor->company . '</b>';
            if (!empty($vendor->address)) {
                $html .= '<br />' . $vendor->address;
            }
            if (!empty($vendor->city) || !empty($vendor->state)) {
                $html .= '<br />';
                if (!empty($vendor->city)) {
                    $html .= $vendor->city . " ";
                }
                if (!empty($vendor->state)) {
                    $html .= $vendor->state;
                }
            }
            if (!empty($vendor->country) || !empty($vendor->zip)) {
                $html .= '<br />';
                if (!empty($vendor->country)) {
                    $country = $CI->db->select('short_name')
                        ->where('country_id', $vendor->country)
                        ->from(db_prefix() . 'countries')
                        ->get()
                        ->row();
                    $html .= $country->short_name . " ";
                }
                if (!empty($vendor->zip)) {
                    $html .= $vendor->zip;
                }
            }
            if (!empty($vendor->vat)) {
                $html .= '<br />' . _l('company_vat_number') . ': ' . $vendor->vat;
            }
            if (!empty($vendor->phonenumber)) {
                $html .= '<br />' . $vendor->phonenumber;
            }
        }

        return $html;
    }
}

if (!function_exists('get_payment_term_name')) {
    function get_payment_term_name($id)
    {
        $terms = [
            8 => 'Advance Payment',
            9 => 'Immediate after delievery ',
            1 => '7 days after delivery',
            2 => '15 days after delivery',
            3 => '30 days after delivery',
            4 => '45 days after delivery',
            5 => '60 days after delivery',
            6 => '75 days after delivery',
            7 => '90 days after delivery',
        ];

        return $terms[$id] ?? '';
    }
}

function get_hsn_sac_code_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $hsn_sac_code = $CI->db->get(db_prefix() . 'hsn_sac_code')->row();

    if ($hsn_sac_code) {
        return $hsn_sac_code->name;
    }

    return '';
}

function get_last_vendor_code()
{
    $CI = &get_instance();
    $CI->db->select('vendor_code');
    $CI->db->order_by('userid', 'DESC');
    $CI->db->limit(1);
    $result = $CI->db->get(db_prefix() . 'pur_vendor')->row();

    return $result ? $result->vendor_code : '';
}

function get_pur_order_for_db($id = false)
{
    $CI           = &get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'pur_orders')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblpur_orders where approve_status = 2')->result_array();
    }
}
function get_by_deafult_order_summary()
{
    $next_number = get_purchase_option('next_wo_number');
    $default_project = '';
    $val = '<p class="p1"></p>
<p class="p2" style="text-align: center;"><span class="s1"><b>WORK ORDER</b></span></p>
<p class="p1"><b><span class="Apple-converted-space">                                                                                                            </span></b></p>
<p class="p3">Ref: HSB/26-27/' . str_pad($next_number, 5, '0', STR_PAD_LEFT) . ' <strong><br>Dated: <span class="order_full_date">' . date("d-M-Y") . '</span></strong></p>
<p class="p5">To,<b></b></p>
<p class="p6"><b>M/s. <span class="vendor_name"></span><br></b><span class="vendor_address"></span><br><span class="vendor_city"></span><span class="vendor_state"></span><span class="vendor_pincode"></span></span><span class="vendor_country"></span><br>Email:<span class="s2"> </span><span class="s1"><span class="vendor_contact_email"></span></span> <br>Contact - <span class="vendor_contact_phone"><br>GST Registration no- <span class="vendor_gst"></span><br>Bank Details:<span class="vendor_bank_details"></span></p>
<p class="p5">Dear Mr. <span class="vendor_name"></span>,</p>
<p class="p5"><b>Sub: </b></p>
<p class="p7">As subsequent discussions we had with you, We at <b>HSB Projects</b> (referred as Employer), accept your final offer and appoint <b>M/s. <span class="vendor_name"></span></b> (herein after called the Contractor) for<b> <span class="wo_order_name"></span> at Project Name.</b> on the basis of detailed Bill of Quantities inclusive of remedying of any defects and safety measures of the above project for the item rate Contract Amount of <b><span class="order_summary_currency">INR</span> <span class="total_in_value"></span>/- (<span class="subtotal_in_words"></span> only) inclusive of GST, as tabulated below; </b></p>
<p class="p4"><b></b></p>
<table cellspacing="0" cellpadding="0" class="t1">
<tbody>
<tr>
<td valign="middle" class="td1">
<p class="p8"><b><span class="wo_order_name"></span></b></p>
</td>
<td valign="middle" class="td2">
<p class="p9"><span class="order_summary_currency">INR</span>          <span class="subtotal_in_value"></span><span class="Apple-converted-space">       </span></p>
</td>
</tr>
<tr>
<td valign="middle" class="td3">
<p class="p8"><b>GST @ 18% in <span class="order_summary_currency">INR</span> </b></p>
</td>
<td valign="middle" class="td4">
<p class="p9"><span class="Apple-converted-space">   </span><span class="order_summary_currency">INR</span> <span class="Apple-converted-space">        </span><span class="total_tax_in_value"></span></p>
</td>
</tr>
<tr>
<td valign="middle" class="td1">
<p class="p8"><b> GRAND TOTAL </b></p>
</td>
<td valign="middle" class="td2">
<p class="p9"><b> <span class="order_summary_currency">INR</span><span class="Apple-converted-space">          </span><span class="total_in_value"></span></b></p>
</td>
</tr>
</tbody>
</table>
<p class="p4"></p>
<p class="p10">We are pleased to inform you that your offer agreed in the negotiation is being hereby accepted by us with the terms and conditions given below. The salient terms and conditions of the work package will be as follows:</p>
<p><b><span class="Apple-converted-space">1.  </span>Scope of work</b></p>
<p class="p14">The scope of work shall be as per bill of quantities with the Contract rates as agreed, herewith enclosed as Annexure ‘A’. We reserve right to revise the scope of works. Quantities mentioned herein are indicative and may vary. Payment will be made as per actual quantum of work done as per agreed unit rate.</p>
<p><b>2. Time Schedule</b></p>
<p class="p15">Date of commencement of work shall be <b>immediately</b>.</p>
<p><strong>3. Time of completion</strong></p>
<p class="p16">The time of completion will be within <b>3 months</b> or earlier from the date of Work Order. Time is the essence of the contract and the work covered in this contract must be completed in all respects, with in the period stipulated in the label above. The period includes all holidays and rainy days.</p>
<p><b>4. Total contract value</b><b></b></p>
<ol class="ol2">
<li class="li3">The contract price as mentioned in Annexure- A is Inclusive of all taxes.</li>
<li class="li3">Actual payment shall be made as per actual work executed against each item of schedule of Quantities here with enclosed as Annexure ‘A’</li>
<li class="li3">The quoted rates shall remain ‘FIRM’ only for External Finishes Works as approved by Project Head.</li>
<li class="li3">It is agreed that the Contractor shall carryout modifications and minor additional works without charging for the same.</li>
<li class="li3">Your rates are inclusive of all based on actual site conditions any additional work required at site to facilitate shall be done by you and no additional claims in this regard will be entertained.</li>
<li class="li3">All rates are inclusive of scaffolding / staging at all heights, safety procedures, PPE etc; wherever required for all type of works / trades etc whether specified or not.</li>
<li class="li3">All scaffolding to be used shall be of MS sections / materials.</li>
</ol>
<p class="p14"><b style="font-family: ‘-apple-system’, BlinkMacSystemFont, ‘Segoe UI’, Roboto, Oxygen, Ubuntu, Cantarell, ‘Open Sans’, ‘Helvetica Neue’, sans-serif; font-size: 12pt;">5. Taxes &amp; duties</b></p>
<p class="p14">The quoted amount as contained in the Contract sum is Inclusive of all taxes. Applicable TDS will be deducted by <b>M/s. HSB Projects</b> while making the payments. It is the sole responsibility of you to comply with necessary statuary requirements like VAT, Service Tax, Labour laws etc. as per norms. <b>M/s. HSB Projects</b> shall not be anyway responsible for non-Compliance of the above by you.</p>
<p><b>6. Free Issue Materials </b></p>
<p class="p14">Cement will be provided by <b>M/s. HSB Projects</b> for Block Work and Plastering only.</p>
<p class="p18">P &amp; M, Scaffolding Material will be provided by <b>M/s. HSB Projects. </b></p>
<p><b>7. Quantities</b></p>
<p class="p14">Quantities mentioned in BOQ are estimated Quantities subject to variation &amp; omission at any extent. Actual executed quantities will be paid. Variation of the quantities to any extent shall not attract any compensation or any rise in Item Rates.</p>
<p><b>8. Payment Terms</b></p>
<p>100% after final bill certification every month.
The Contractor shall provide a weekly labour advance (Kharchi) of ₹1,500/- to each labour.
Deduction of ₹500/- if anyone obey safety laws.
TDS of 1.00% in every bill amount will be deducted.
Administration charges of 2.00% in every RA bill for the manpower enegaged.
Safety debit of Rs 2000 will be applicable, if any worker find defaulter.
In case of Any accident or fatalities, the worker and contractor are responsible.</p>
<p class="p21">Payment will be affected as per actual delivery/execution at site. Payment of final bill will be done in 30 working days from the date of Certification from Engineer and receiving all supporting documents.</p>
<p><b>9. Payment of Final Bill</b></p>
<p class="p14">30 days commencing on certification and receiving all supporting documents.</p>
<p><b>10. Defect Liability Period</b></p>
<p class="p14">12 Months from the completion certificate (on Virtual Completion) issued by Employer.</p>
<p><b>11. Liquidity damages</b></p>
<p class="p14">Amount of Liquidated Damage for non-completion of `work with in stipulated time is 0.5% of the contract price per week or part thereof subject to a maximum of 5 % of the Contract price. The LD will be imposed on final completion schedule or as per mutually agreed mile-stones. The contractor must abide to the time specified.</p>
<p><b>12. Escalation</b></p>
<p class="p14">Not applicable</p>
<p><b>13. Basic Rate</b></p>
<p class="p14">Not applicable</p>
<p><b>14. Material Advance</b></p>
<p class="p14">Not applicable</p>
<p><b>15. ESIS, PF, Labour License </b><b></b></p>
<p class="p14">Contractor will pay ESIS and PF as per the rules of the concerned authorities if applicable. This will not be reimbursed by us and we shall be indemnified against such a demand. In the event if we want to have the copies of ESIC and PF challans, you will submit the same. (If Applicable). As per the Contract Labour (Abolition and Regulation Act,(1970) you must apply for the license at the Deputy Labour Commissioner’s office by taking form V from the Project department. The copy of this license must be submitted by you within commencement period. (If applicable).</p>
<p><b>16. Insurance Policies </b></p>
<p class="p14">Not Applicable</p>
<p><b>17. Programme for Works</b></p>
<p class="p14">Contractor shall progress with the work keeping in view the completion date indicated under clause “Time of Completion”, You shall prepare a detailed schedule including major milestones based on the end date, which shall be mutually agreed to and accepted between us. In case of delays in achieving the target dates the Employer shall be entitled to charge the compensation for the delay and adjust the same against your running bills irrespective of consequential delays.<b></b></p>
<p><b>18. Construction Power and Water<br><br></b>One power point connection may be provided by Employer at no cost, Contractor should arrange the distribution of power at his own cost. Water connection will be provided by Employer, Free of Cost, at one-point further distribution cost is in contractor scope including pump; etc.</p>
<p><b>19. Amendment</b></p>
<p class="p14">This work order may not be amended or modified except by an instrument in writing signed by us.</p>
<p><b>20. Arbitration</b><b></b></p>
<p class="p14">The venue of Arbitration shall be at Mumbai in English Language and the Arbitration shall be conducted in accordance with the provisions of the Arbitration and Conciliation Act, 1996 or any statuary modification or re-enactment thereof. The number of arbitrators shall be mutually agreed. The Arbitration award shall be final and binding upon the parties to arbitration. Costs of such arbitration shall be borne by the party that lost the arbitration case.</p>
<p><b>21. Site Clearance / House Keeping</b></p>
<p class="p14">During the execution of the works, you shall keep the site reasonably free from all unnecessary obstructions and shall store or dispose of any equipment and surplus materials. You shall also clear away, and remove from site any wreckage, rubbish or temporary works no longer required. You shall keep the site clean and all the debris generated shall be disposed outside the site.</p>
<p><b>22. Extra Item</b></p>
<p class="p14">The unit rates in the bill of quantities shall determine the valuation of work of similar character executed under similar condition as was priced therein as pr tender condition, otherwise the rate shall be finalized as under; Cost of material as prevailing rates at the time of execution of work actually incorporated in the project. Relevant vouchers are required as per supporting document. Cost of labour, workforce, trade supervisors at prevailing rates at the time of execution of work actually incorporated in the project including all liabilities due to statutory law as per contract condition. 15% towards contractor’s establishment, plant and machinery, electricity and water charges, overhead and profits. Refer Annexure B for template for Rate Analysis.</p>
<p><b>23. Hoisting, Transportation Etc.</b><b></b></p>
<p class="p14">Contractor shall arrange on his own for loading, unloading, lead, lift and shift; etc all complete as necessary.</p>
<p><b>24. Force Majeure</b><b></b></p>
<p class="p14">If at any time, during the continuance of the Contract works the performance in whole or part of any obligation under it shall be prevented or delayed by reason of war, hostility, acts of the public enemy, civil commotion, sabotage, fire, flood, explosion, epidemic, quarantine restriction, strikes, lock-outs or acts of God, etc, the Contractor shall not have any claim for damages against the Employer, in respect of non-performance or delay in performance of the Employer.</p>
<p><b>25. Guarantee/Warrantee</b><b></b></p>
<p class="p14">Guarantee of the work would be for one (1) year from hand over as applicable.<br><br><b>26. Idling of resources</b></p>
<p class="p14">Idling time for all the equipment, plant and labour for whatever reason will be borne by contractor unless and otherwise specified by Project Head.</p>
<p><b>27. Labour Accommodation</b></p>
<p class="p14">No labour or staff accommodation shall be permitted at site. Contractor shall make his own arrangement for the same outside the site &amp; necessary transportation of their labour &amp; staff. </p>
<p><b>28. Suspension of Work</b><b></b></p>
<p class="p14">The Contractor shall, on the written order of the Employer/Employer’s Representative, suspend the progress of the works or any part thereof for such time or times and in such manner as the Employer/Employer’s Representative may consider necessary and shall, during such suspension, properly protect and secure the work, so far as is necessary in the opinion of the Employer/Employer’s Representative.</p>
<p class="p14">The Contractor shall immediately notify the Project Manager of any plant or machinery left idle or unused as a consequence of the above clause, and comply with any instructions given to redeploy on or about the works.</p>
<p><b>29. Termination</b></p>
<p class="p14">Employer reserves the right to terminate the contract if not been executed to the desired quality and in the required time.</p>
<p class="p14">Employer may, at its option, terminate for convenience any of the Work under this contract in whole or, from time to time, in part, at any time by written notice to Contractor. Such notice shall specify the extent to which the performance of the Work is terminated and the effective date of such termination at no compensation. In such event, the Employer shall pay the Contractor such portions of the Work as are due and properly completed.</p>
<p><b>30. Temporary facilities</b><b></b></p>
<p class="p14">Contractor has to make an arrangement for office, labour Toilets, stores, fabrication yard and shuttering yard with adequate access and approach to the work.</p>
<p><b>31. Statutory Requirements</b></p>
<p class="p14">As applicable<br><br><b>32. Safety &amp; Security</b></p>
<ol class="ol2">
<li class="li3">Comply with all applicable safety regulations,</li>
<li class="li3">Take care for the safety of all persons entitled to be on the Site.</li>
<li class="li3">All workers shall wear safety helmet and other safety gadgets as required.</li>
<li class="li3">You shall store all the material properly and shall provide proper security until <span class="Apple-converted-space">  </span>handing over to the Employer.</li>
</ol>
<p class="p14">You shall take appropriate measures to secure your works and handover to us after completing all the works without any damage, scratches etc. to the satisfaction of the Engineer-in-charge. Also refer attached Safety Manual.</p>
<p><b>33. Site office, Godown, Stores, Meeting Room Etc.</b><b></b></p>
<p class="p14">You shall be provided sufficient area at our site for the construction of your facilities required for the project subject to the availability and mutual agreement.</p>
<p class="p14">During the Contract period, if the site office / stores have to be relocated then it shall be at Contractor’s cost.</p>
<p class="p14">Also, demolition and clean-up are to be done by Contractor to full satisfaction of Employer. The same has to be removed and clean–up by Contractor to the satisfaction of Employer after completion and handing over of the project at his own cost.</p>
<p><b>34. Quality</b></p>
<p class="p14">Quality of the work should be acceptable to the Employer, Consultant &amp; Architect. If the work not found suitable, the Contractor shall amend as required and directed. In case the work is not amended by Contractor after instructions, the same shall be carried out by other agencies which will be back charged by Employer with additional overhead of 15% towards Employer costs. Check list to be submitted location wise on timely based / as required by Project Manager. Protection of application in contractors’ scope for the works carried out by other agencies. </p>
<p><b>35. Infrastructure to carry out the works</b><b></b></p>
<p class="p14">The Contractor shall have all the tools, tackles, equipment, machinery etc all required to carry out the works at his own cost.</p>
<p><b>36. Indemnification</b><b></b></p>
<p class="p14">Contractor has to keep Employer indemnify in all matters.</p>
<p class="p13"><b>37. Co-Ordination<br><br></b><span style="font-family: ‘-apple-system’, BlinkMacSystemFont, ‘Segoe UI’, Roboto, Oxygen, Ubuntu, Cantarell, ‘Open Sans’, ‘Helvetica Neue’, sans-serif; font-size: 12pt;">You shall coordinate with the all-other agencies involved in the project and take necessary action to ensure smooth progress of the work.</span></p>
<p><b>38.PROTECTION OF WORKS</b></p>
<p class="p14">The Contractor shall take full responsibility for the proper care and protection of the work carried out in respect of the Project from commencement until completion and handing over of the Project to the Employer’s representatives.<span class="Apple-converted-space">  </span>The Contractor shall protect and preserve the work carried out in respect of the Project in every way from any damage, fire or accident, including by providing temporary roofs, boxing, protection film on glass / aluminum / fins / window / façade works / canopy etc; or other construction as required by the Employer’s representatives. This protection shall be provided for all property on the Site as well as adjacent to the Site. The Contractor shall adequately protect, to the satisfaction of the Employer’s representatives, all the items of finishing work to prevent any chipping, cracking, breaking of edges or any damage of any kind whatsoever and to prevent such work from getting marked or stained or dirty. <span class="Apple-converted-space">  </span>Should the Contractor fail to protect the work in respect of the Project or any part thereof and should any damage be caused<span class="Apple-converted-space">  </span>to<span class="Apple-converted-space">  </span>the<span class="Apple-converted-space">  </span>same,<span class="Apple-converted-space">  </span>the Contractor<span class="Apple-converted-space">  </span>shall<span class="Apple-converted-space">  </span>be<span class="Apple-converted-space">  </span>responsible<span class="Apple-converted-space">  </span>for<span class="Apple-converted-space">  </span>all<span class="Apple-converted-space">  </span>replacement<span class="Apple-converted-space">  </span>and<span class="Apple-converted-space">  </span>rectification,<span class="Apple-converted-space">  </span>as directed by the Employer’s representatives, and all costs and expenses in connection with<span class="Apple-converted-space">  </span>such<span class="Apple-converted-space">  </span>replacement<span class="Apple-converted-space">  </span>and<span class="Apple-converted-space">  </span>rectification<span class="Apple-converted-space">  </span>shall<span class="Apple-converted-space">  </span>be<span class="Apple-converted-space">  </span>to<span class="Apple-converted-space">  </span>the<span class="Apple-converted-space">  </span>account<span class="Apple-converted-space">  </span>of<span class="Apple-converted-space">  </span>the Contractor and shall be borne by contractor.</p>
<p class="p5">You are requested to sign the enclosed duplicate copy of this work order as the token of your acceptance of this contract and return the same to us.</p>
<p class="p25"></p>
<p class="p26"><b>Yours faithfully,</b></p>
<p class="p5"><b>for HSB Projects</b></p>
<p class="p4"><b></b></p>
<p class="p5"><b>Authorized Signatory</b></p>
<p class="p5"></p>
<p class="p5">Encl:<span class="Apple-tab-span"> </span>Annexure – A - Bill of Quantities</p>
<p class="p1"><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span> <span class="Apple-converted-space">     </span></p>
<p class="p27"><b><span class="Apple-tab-span"> ____________________________________</span></b></p>
<p class="p28"><b>ACKNOWLEDGMENT</b></p>
<p class="p29"></p>
<p class="p7">I, ____________________________________ in the capacity of Authorized Signatory to sign for and on behalf of <b><span class="vendor_name"></span>, </b>hereby acknowledge the receipt of your Work Order and confirm acceptance of the terms and conditions stated therein.</p>
<p class="p1"></p>
<p class="p1"></p>
<p class="p1"></p>
<p class="p1"></p>
<p class="p1"><b></b></p>
<p class="p5"><b>Sign &amp; Seal of Contractor<span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span><span class="Apple-tab-span"> </span> <span class="Apple-converted-space">                                                                                                                                                         </span>Date</b></p>
<p class="p4"></p>
<p class="p1"></p>';

    return $val;
}

function get_last_payment_code()
{
    $CI = &get_instance();
    $CI->db->select('payment_code');
    $CI->db->order_by('id', 'DESC');
    $CI->db->limit(1);
    $result = $CI->db->get(db_prefix() . 'record_payment')->row();

    return $result ? $result->payment_code : '';
}

function get_pur_invoices_by_id($id)
{
    $CI = &get_instance();

    $CI->db->select('invoice_number, vendor_invoice_number');
    $CI->db->where('id', $id);

    $result = $CI->db->get(db_prefix() . 'pur_invoices')->row();

    if ($result) {
        return $result->invoice_number . ' - ' . $result->vendor_invoice_number;
    }

    return '';
}
