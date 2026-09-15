<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Work_order_to_approver_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact firstname',
                'key'       => '{contact_firstname}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'Contact lastname',
                'key'       => '{contact_lastname}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'Wo id',
                'key'       => '{wo_id}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'WO name',
                'key'       => '{wo_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'Project name',
                'key'       => '{project_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'Work order link',
                'key'       => '{work_order_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
            [
                'name'      => 'Work order title',
                'key'       => '{work_order_title}',
                'available' => [
                    
                ],
                'templates' => [
                    'work-order-to-approver',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($data)
    {
        $wo_id = $data->wo_id;
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $this->ci->db->where('id', $wo_id);

        $wo = $this->ci->db->get(db_prefix() . 'wo_orders')->row();


        if (!$wo) {
            return $fields;
        }

        $fields['{contact_firstname}'] =  $data->contact_firstname;
        $fields['{contact_lastname}'] =  $data->contact_lastname;
        $fields['{wo_id}'] =  $wo->wo_order_number;
        $fields['{wo_name}'] =  $wo->wo_order_name;
        $fields['{project_name}'] =  get_project_name_by_id($wo->project);
        $fields['{work_order_title}'] = site_url('purchase/vendors_portal/wo_order/' . $wo->id.'/'.$wo->hash);
        $fields['{work_order_link}'] = site_url('purchase/vendors_portal/wo_order/' . $wo->id.'/'.$wo->hash);

        return $fields;
    }
}
