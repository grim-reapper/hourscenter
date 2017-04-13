<?php

class Admin_model extends Ci_model {

    function __construct() {
        parent::__construct();
    }

    public function getBrandsRoute()
    {
        $q = $this->db->select('slug')->from('brands');
        if($q->num_rows() > 0) {
            return $q->result_array();
        }
        return [];
    }

    public function getStatesRoute()
    {
        $q = $this->db->select('slug')->from('states');
        if($q->num_rows() > 0) {
            return $q->result_array();
        }
        return [];
    }

    public function getCitiesRoute()
    {
        $q = $this->db->select('slug')->from('cities');
        if($q->num_rows() > 0) {
            return $q->result_array();
        }
        return [];
    }

}
