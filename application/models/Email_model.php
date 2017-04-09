<?php

class Email_model extends Ci_model {

    function __construct() {
        parent::__construct();
    }

//    return an array containing email body data and to from fields
    function get_email_data($email_reference) {
        $data = array();
        $query = $this->db->get_where('email', array('email_reference' => $email_reference, 'is_active' => 'y'));
        if ($query->num_rows() > 0) {
            $email = $query->result_array();
        } else {
            $data['body'] = '';
            $data['subject'] = '';
            $data['from_name'] = '';
            $data['from_email'] = '';
            $data['to_email'] = '';
            $data['cc_email'] = '';
            $data['bcc_email'] = '';
            $data['reply_to_email'] = '';
            $data['email_type'] = '';
            $data['message'] = '';
            $data['is_active'] = 'n';
            return $data;
        }

        $data['body'] = $email[0]['body'];
        $data['subject'] = $email[0]['subject'];
        $data['from_name'] = $email[0]['from_name'];
        $data['from_email'] = $email[0]['from_email'];
        $data['to_email'] = trim($email[0]['to_email']);
        $data['cc_email'] = $email[0]['cc_email'];
        $data['bcc_email'] = $email[0]['bcc_email'];
        $data['reply_to_email'] = $email[0]['reply_to_email'];
        $data['email_type'] = $email[0]['email_type'];
        $data['is_active'] = $email[0]['is_active'];
        $data['email_reference'] = $email[0]['email_reference'];
        $domain = site_url();

        $email_data = "<table width='100%' style=\"font-family:arial,helvetica,sans-serif;font-weight:normal;font-size:12px;color:#424141;\">";
        $header_id = $email[0]['email_header_id'];
        $signature_id = $email[0]['email_signature_id'];
        $footer_id = $email[0]['email_footer_id'];

        $header = $this->db->get_where('lookup', array('id' => $header_id));
        $signature = $this->db->get_where('lookup', array('id' => $signature_id));

        $header = $header->result_array();
        $signature = $signature->result_array();

//        $email[0]['body'] = str_replace("#to_name#", $data['to_name'], $email[0]['body']);
        $email[0]['body'] = str_replace("#from_name#", $data['from_name'], $email[0]['body']);
        $email[0]['body'] = str_replace("#domain#", $domain, $email[0]['body']);

//        layout handling of email
        if (isset($header[0]['lookup_desc']) && !empty($header[0]['lookup_desc'])) {
            $email_data .= '<tr><td style="padding-bottom:15px;" colspan="2" width="100%">' . $header[0]['lookup_desc'] . '</td></tr>';
        }
        $email_data .= '<tr><td colspan="2" width="95%">' . $email[0]['body'] . '</td></tr>';

        if (isset($signature[0]['lookup_desc'])) {
            $email_data .= '<tr><td colspan="2" style="padding-top:15px;" width="95%">' . $signature[0]['lookup_desc'] . '</td></tr>';
        }

        $email_data .= "</table>";
        $email_data = str_replace('src="/uploads', 'src="' . ADMIN_COSTOM_PATH . 'uploads', $email_data);
        $data['message'] = $email_data;
        return $data;
    }

//    send email 
    function send_email($email_data) {

        /*if ($email_data['is_active'] == 'n') { // check if an email is not active if so then return as it was sent            
            return true;
        }*/

        $config = array();
        $config['mailtype'] = 'html';
        $this->load->library('email', $config);

        $this->email->from($email_data['from_email'], $email_data['from_name']);
        $email_data['to_email'] = str_replace('\\', '\\\\', $email_data['to_email']);
        $this->email->to(utf8_decode($email_data['to_email']));

        if (!empty($email_data['cc_email'])) {
            $this->email->cc($email_data['cc_email']);
        }
        if (!empty($email_data['bcc_email'])) {
            $this->email->bcc($email_data['bcc_email']);
        }
        if (isset($email_data['attach']) && !empty($email_data['attach'])) {
            foreach ($email_data['attach'] as $attachemnts) {
//                $this->email->attach($email_data['attach']);
                $this->email->attach($attachemnts);
            }
        }
        if (!empty($email_data['reply_to_email'])) {            
            if (isset($email_data['reply_to_name']) && !empty($email_data['reply_to_name'])) {
                $this->email->reply_to($email_data['reply_to_email'], $email_data['reply_to_name']);
            } else {
                if(strstr($email_data['reply_to_email'], '<')){
                    $rep_to_email = strstr($email_data['reply_to_email'], '<');
                    $rep_to_name = substr($email_data['reply_to_email'], 0,strpos($email_data['reply_to_email'], '<'));
                    $this->email->reply_to($rep_to_email, $rep_to_name);
                }else{
                    $this->email->reply_to($email_data['reply_to_email']);
                }                
            }
        } else {
            $this->email->reply_to($email_data['from_email'], $email_data['from_name']);
        }

        $this->email->subject($email_data['subject']);
//        $email_data['message'] = str_replace('\\', '\\\\', $email_data['message']);
        $this->email->message($email_data['message']);
        
        return $this->email->send() ? true : false; 

    }

}

?>
