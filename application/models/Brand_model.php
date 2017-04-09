<?php

class Admin_model extends Ci_model {

    function __construct() {
        parent::__construct();
    }

    //	Check for brand already exists
    function isBrandExist($brand_name)
    {
      $query = $this->db->get_where('brands', array('name' => $brand_name));
      if($query->num_rows() > 1){
        return true;
      }
      return false;
    }
    function saveBrand($logo_name) {
        $brand_name = $this->input->post('brand_name');
        $page_heading = $this->input->post('page_heading');
        $data = ['name' => $brand_name, 'heading' => $page_heading,'logo' => $logo_name];
        if($this->db->insert('brands',$data)){
          return true;
        }
        return false;
    }

}
