<?php

class Admin_model extends Ci_model {

    function __construct() {
        parent::__construct();
    }
    //	Authenticate password recovery request
    function authenticate_recovery_request($token) {

        $query = $this->db->query("SELECT * FROM users where token = '" . $token . "' and token_active = 'y' ");
        return $query->num_rows();
    }

    //	Edit Logged user to database
    function update_password($user_pass_hashed) {

        //Insert information into the database
        $this->db->set('user_pass', $user_pass_hashed);
        $this->db->set('token_active', 'n');
        $this->db->where('token', $this->input->post('tokenkey'));
        $this->db->update('users');
        return $this->db->affected_rows();
    }
}
