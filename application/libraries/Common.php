<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common {

    //        my own created function        
    function __construct() {
        $this->CI = & get_instance();
    }

    function checkEmailAddress($user_email = '') {
        if ($user_email == '')
            return false;
        //Check against user table
        $this->CI->db->where('user_email', $user_email);
        //$this->CI->db->where('is_active', 'y'); 
        $query = $this->CI->db->get_where('users');


        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
//  function returns date format for standardization of uk
    function long_date($date) {
        $date_exp = explode('-', $date);
        if ($date_exp[0] == '0000') {
            return '';
        }
        $date_exp = explode(' ', $date); // bcz strtotime() return false for 00:00:00
        $temp_date = date('d-M-Y', strtotime($date_exp[0]));
        return $temp_date . ' ' . $date_exp[1];
    }

    function short_date($date) {
        $date_exp = explode('-', $date);
        if ($date_exp[0] == '0000') {
            return '';
        }
        return date('d F Y', strtotime($date));
    }

    function short_date_with_time($date) {
        $date_exp = explode('-', $date);
        if ($date_exp[0] == '0000') {
            return '';
        }
        return date('d F Y H:i:s', strtotime($date));
    }

//    function to check either any user is logged in or not
    function is_logged_in() {
        return ($this->CI->session->userdata('logged_in')) ? true : false;
    }

//    function to return page not found page contects
    function get_page_not_found_data() {
        $ref = '/page-not-found';
        $query = $this->CI->db->query("SELECT * FROM static_pages WHERE jury_id ='" . JURY_ID . "' and new_url = '" . $ref . "'");
        if ($query->num_rows() > 0) {
            $data['page_content'] = $query->result_array();
            $data['meta_title'] = $data['page_content'][0]['meta_title'];
            $data['meta_keywords'] = $data['page_content'][0]['meta_keywords'];
            $data['meta_description'] = $data['page_content'][0]['meta_description'];

            $data['lookup_ldc'] = $this->getldcform(JURY_ID);
            $data['lookup_article'] = $this->get_lookup_detail(JURY_ID, 'articles');
            $data['lookup_form'] = $this->get_lookup_detail(JURY_ID, 'form');

            $data['template_type'] = 'P';
            header("HTTP/1.0 404 Not Found");
            $this->CI->template->write_view('content', 'pages/404', $data, true);
            $this->CI->template->render();
            return;
        }
        return array();
    }

//    function to check either Control panel is accessible or not
    function is_cp_accessible() {
        $user = $this->CI->session->userdata('user');
        if (isset($user['is_cp_accessible']) && $user['is_cp_accessible'] == 'y') {
            return true;
        }
        redirect('/');
//        return false;
    }

//    function getTax($jury_id) {
//
//        $query = $this->CI->db->query("SELECT * FROM jury_tax where jury_id = " . $jury_id . " ");
//        if ($query->num_rows() > 0) {
//            return $query->result_array();
//        }
//        return array();
//    }

    function get_lookup_position($jury_id, $type) {

        $query = $this->CI->db->query("SELECT * FROM lookup where jury_id = " . $jury_id . " and  lookup_desc like '$type%' and is_active = 'y' and  lookup_type = 'header_search' ");
        return $query->row()->sort_order;
    }

    function get_lookup_detail($jury_id, $type) {

        $query = $this->CI->db->query("SELECT * FROM lookup where jury_id = " . $jury_id . " and  lookup_desc like '$type%' and is_active = 'y' and  lookup_type = 'header_search' ");
        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }

    function getldcform($jury_id) {
        $query = $this->CI->db->query("SELECT * FROM lookup where jury_id = " . $jury_id . " and lookup_name = 'Legal documents' and is_active = 'y' ");
        $data = $query->row();
        if ($query->num_rows() > 0) {
            $params = explode('||', $data->lookup_desc);
            if (isset($params[3])) {
                $url_ref = $params[3];
                $query = $this->CI->db->query("SELECT new_url FROM urls_v WHERE url_ref = '$url_ref'");
                if ($query->num_rows() > 0) {
                    $new_url_data = $query->row_array();
                    $params[3] = $new_url_data['new_url'];
                }
                $data->lookup_desc = implode('||', $params);
            }
        }
        return $data;
    }

    function get_url_by_ref($url_ref) {
        if (empty($url_ref)) {
            return '/';
        }
        $query = $this->CI->db->query("SELECT new_url FROM urls_v WHERE url_ref = '$url_ref'");
        if ($query->num_rows() > 0) {
            $new_url_data = $query->row_array();
            return $new_url_data['new_url'];
        }
        return '/';
    }

    function getCurrencySymbol($jury_id) {

        $query = $this->CI->db->query("SELECT * FROM jury where id = " . $jury_id . " ");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function removeSpecialCharacter($value) {

        $first_char = substr(html_entity_decode($value), 0, 1);
        if (preg_match('#([a-z])#', $first_char)) {
            $title = ucfirst(stripslashes($value));
        } else {
            $title = stripslashes($value);
        }

        return $title;
    }

    function starRating($stars) {
        $stars = $this->roundStarRating($stars) * 2;
        return '<span class="star_full_' . $stars . '" style="display: block;"></span>';
    }

    function roundStarRating($stars) {
        if ($stars == 0) {
            return '0';
        } elseif ($stars > 0 && $stars <= 0.5) {
            return '0.5';
        } elseif ($stars > 0.5 && $stars <= 1) {
            return '1';
        } elseif ($stars > 1 && $stars <= 1.5) {
            return '1.5';
        } elseif ($stars > 1.5 && $stars <= 2) {
            return '2';
        } elseif ($stars > 2 && $stars <= 2.5) {
            return '2.5';
        } elseif ($stars > 2.5 && $stars <= 3) {
            return '3';
        } elseif ($stars > 3 && $stars <= 3.5) {
            return '3.5';
        } elseif ($stars > 3.5 && $stars <= 4) {
            return '4';
        } elseif ($stars > 4 && $stars <= 4.5) {
            return '4.5';
        } elseif ($stars > 4.5 && $stars <= 5) {
            return '5';
        }
    }


//    function refine/replace variables included in content area
    function refine_contents($data, $meta_keywords = '') {
        $data = $this->removeSpecialCharacter($data);
        $data = str_replace('src="/uploads', 'src="' . ADMIN_COSTOM_PATH . 'uploads', $data);
        $data = str_replace('href="/uploads/ckfinder', 'href="' . ADMIN_COSTOM_PATH . 'uploads/ckfinder', $data);

        $data = str_replace('#draft_button#', $this->drafting_button(), $data);
        $data = str_replace('#legal_advice_button#', $this->legal_advice_button(), $data);

//        replace keywords for landing page contents
        $req_url = $_SERVER['REQUEST_URI'];
        $req_url = str_replace('/lp/', '', $req_url);

        $replace_keyword = '';

        if (!empty($meta_keywords)) {
            $meta_keywords = explode(',', $meta_keywords);
            foreach ($meta_keywords as $keyword) {
                $keyword = trim($keyword);
                $orig_keyword = $keyword;
                $keyword = url_title($keyword, 'dash', $lowercase = true);
                if (!empty($keyword) && $keyword == $req_url) {
                    $replace_keyword = $orig_keyword;
                    break;
                }
            }
        }

        if (!empty($replace_keyword)) {
            $data = str_replace('#keyword#', $replace_keyword, $data);
        } else {
            $req_url = str_replace('-', ' ', $req_url);
            $data = str_replace('#keyword#', $req_url, $data);
        }
//        end meta keyword replacing

        $referer = '/';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        $data = str_replace('#referrer_link#', $referer, $data);
        $data = str_replace('#domain#', site_url(), $data);

        $url_chars_replace = array('http://', 'https://', '/', 'www.');
        $data = str_replace('#domain_name#', str_replace($url_chars_replace, '', site_url()), $data);
//        search for url reference in content and replace with proper url
        if (strstr($data, 'href="#')) {
            $content_parts = explode('href="#', $data);
            $data = $content_parts[0];

            foreach ($content_parts as $index => $message) {
                if ($index != 0) {
                    if (strstr($message, '#"') || strstr($message, '##') || strstr($message, '#?')) { // check for inline links                        
                        $url_pos = strpos($message, '#');
                        $url_ref = substr($message, 0, $url_pos);

                        $query = $this->CI->db->query("SELECT new_url FROM all_urls_v where url_ref = '" . $url_ref . "' and jury_id =" . JURY_ID . " ");
                        if ($query->num_rows() > 0) {
                            $res = $query->row_array();
                            $new_url = site_url() . substr($res['new_url'], 1);
                            $message = 'href="#' . $message;
                            $message = str_replace('#' . $url_ref . '#', $new_url, $message);
                        }
                    } else {
                        $message = 'href="#' . $message;
                    }
                    $data .= $message;
                }
            }
        }
        $data = str_replace('style="list-style-type: square;"', 'class="square_bulit"', $data);
        $data = str_replace('style="list-style-type: circle;"', 'class="circle_bulit"', $data);

        return $data;
    }

//    return extention of a file name
    function get_extension($file_path) {
        $path_parts = pathinfo($file_path);
        if (isset($path_parts['extension'])) {
            return '.' . $path_parts['extension'];
        } else {
            return false;
        }
    }

    //    return filename of given path, pathinfo() do not run on php version less than 5.2.0
    function get_filename($file_path) {
        $file_path = pathinfo($file_path);
        $file_parts = explode('.', $file_path['basename']);
        $file_name = '';
        for ($i = 0; $i < count($file_parts) - 1; $i++) {
            $file_name[] = $file_parts[$i];
        }
        return implode('.', $file_name);
    }

//    function to return either a logged in user in from wtwm or not
    function is_wtwm_user() {
        $user = $this->CI->session->userdata('user');
        if (isset($user['email']) && (strstr($user['email'], '@wtwm.com') || strstr($user['email'], '@netlawman.co.uk'))) {
            return true;
        }
        return false;
    }

//Delete folder function
    function delete_directory($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        $handle = opendir($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!$this->delete_directory($dir . DIRECTORY_SEPARATOR . $item))
                return false;
        }
        closedir($handle);
        return rmdir($dir);
    }

//    function to delete temp downloadable session files
    function del_sess_files() {
        if ($this->CI->session->userdata('to_del_files')) {
            foreach ($this->CI->session->userdata('to_del_files') as $file_name) {
                $del_file = SERVER_ABS_PATH . '/uploads/temp/' . $file_name;
                $this->CI->session->set_userdata('to_del_files', '');
                if (strstr($del_file, '.')) {
                    if (file_exists($del_file)) {
                        unlink($del_file);
                    }
                } else {
                    $this->delete_directory($del_file);
                }
            }
        }
        return;
    }

    function get_page_type_by_link($url) {
        $query = $this->CI->db->query("SELECT page_type_id FROM `pages` where new_url = '" . $url . "' and jury_id = '" . JURY_ID . "' ");
        return $query->row();
    }

    function last_array_with_and($array, $seperator = ', ', $last_text = ' and ') {
        $last_val = array_pop($array);
        if (count($array) > 0) {
            return implode($seperator, $array) . $last_text . $last_val;
        }
        return $last_val;
    }

    function used_optimizely($type, $url) {
        if (JURY_ID != 3) {
            return false;
        }
        if (JURY_ID == 3 && (!$this->CI->session->userdata('crazyegg_js') || !$this->CI->session->userdata('optimzely_js'))) {
            $this->CI->load->helper('array');
            $query = $this->CI->db->select('exp_type, exp_url')
                    ->get_where('experiments', array(
                'jury_id' => JURY_ID,
                'exp_type' => $type,
                'is_active' => 'y'
            ));
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                //Load crazy egg
                if (!$this->CI->session->userdata('crazyegg_js') && $type == 'crazy_egg') {
                    $this->CI->session->set_userdata('crazyegg_js', multi_to_single($result, 'exp_url'));
                }
                //Load Optimizely
                if (!$this->CI->session->userdata('optimzely_js') && $type == 'optimizely') {
                    $this->CI->session->set_userdata('optimzely_js', multi_to_single($result, 'exp_url'));
                }
            }
        }
        if ($this->CI->session->userdata('crazyegg_js') && $type == 'crazy_egg') {
            if (in_array($url, $this->CI->session->userdata('crazyegg_js'))) {
                return true;
            }
        }
        if ($this->CI->session->userdata('optimzely_js') && $type == 'optimizely') {
            if (in_array($url, $this->CI->session->userdata('optimzely_js'))) {
                return true;
            }
        }
        return false;
    }

    //Function creates new captcha and insert it into database

    function captcha_exp_time() {
        return time() + 1800; //Set for half hour
    }

    function create_captcha($url = '') {
        $this->CI->load->helper('captcha');
        $this->CI->load->library('preload');
        $data['ip_address'] = $_SERVER['REMOTE_ADDR']? : ($_SERVER['HTTP_X_FORWARDED_FOR']? : $_SERVER['HTTP_CLIENT_IP']);
        if ($url == '')
            $data['page_url'] = current_url();
        else
            $data['page_url'] = $url;
        $data['exp_time'] = $this->captcha_exp_time();
        $data['captcha_val'] = $this->CI->preload->generateCode_404(5);
        $data['session_id'] = $this->CI->session->userdata('session_id');

        $this->CI->session->set_userdata('captchaword', $data['captcha_val']);
        if (!$this->is_pointto_img()) { //if url is not pointing to image
            $this->delete_captcha($data['captcha_val']);
        }
        $this->CI->db->insert('captcha', $data);
        return $data['captcha_val'];
    }

    function delete_captcha($cap_val) {
        $current_time = time();
        $this->CI->db->delete('captcha', array(
            'exp_time <' => $current_time,
                //'captcha_val <>' => $cap_val,
                //'session_id' => $this->CI->session->userdata('session_id')
        ));
    }

    function delete_used_captcha($captcha_val) {
        $this->CI->db->delete('captcha', array(
            'captcha_val' => $captcha_val
        ));
    }

    function is_captcha_valid() {
        $current_time = time();
        $query = $this->CI->db->get_where('captcha', array(
                    'session_id' => $this->CI->session->userdata('session_id'),
                    'exp_time >' => $current_time,
                    'captcha_val' => $this->CI->input->post('security_code')
                ))->num_rows();
        if ($query > 0)
            return true;
        return false;
    }

    function is_page_exist($url) {

        $num_rows = $this->CI->db->select('jury_id')
                        ->where('jury_id', JURY_ID)
                        ->where('old_url', $url)
                        ->or_where('new_url', $url)
                        ->get('urls_v')->num_rows();
        if ($num_rows > 0)
            return true;
        return false;
    }

    //404 handling for broken images
    function is_pointto_img() {
        $url = explode('.', current_url());
        $url = end($url);
        if ($url == 'png' || $url == 'jpg' || $url == 'gif' || $url == 'jpeg')
            return true;
        return false;
    }

    function is_mobile() {
//        if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPod'))
//            return true;
//        return true;
    }

    function minifyCss($array_of_files, $used_as_linktag = false) {
        $styleSheetContent = "";
        foreach ($array_of_files as $row) {
            if ($used_as_linktag == false) {
                $styleSheetContent .= $this->get_file_data(CDN_PATH . $row);
            } else {
                $styleSheetContent .= '<link rel="stylesheet" href="' . CDN_PATH . $row . '">';
            }
        }
        $styleSheetContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styleSheetContent);
        $styleSheetContent = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '     '], '', $styleSheetContent);
        $styleSheetContent = preg_replace(['(( )+{)', '({( )+)'], '{', $styleSheetContent);
        $styleSheetContent = preg_replace(['(( )+})', '(}( )+)', '(;( )*})'], '}', $styleSheetContent);
        $styleSheetContent = preg_replace(['(;( )+)', '(( )+;)'], ';', $styleSheetContent);
        if ($used_as_linktag == false) {
            $styleSheetContent .= str_replace("../m_images/", CDN_PATH . '/layout/m_images/', $styleSheetContent);
            $styleSheetContent .= str_replace("../img/", CDN_PATH . '/layout/img/', $styleSheetContent);
            $styleSheetContent .= str_replace("../images/", CDN_PATH . '/layout/images/', $styleSheetContent);
            $styleSheetContent .= str_replace("../fonts/", CDN_PATH . '/layout/fonts/', $styleSheetContent);
            return '<style type="text/css">' . $styleSheetContent . '</style>';
        }
        return $styleSheetContent;
    }

    function minifyJs($array_of_files, $used_as_linktag = false) {
        $jsContent = "";
        foreach ($array_of_files as $row) {
            if ($used_as_linktag == false) {
                $jsContent .= $this->get_file_data(CDN_PATH . $row);
            } else {
                $jsContent .= '<script type="text/javascript" src="' . CDN_PATH . $row . '"></script>';
            }
        }
        if ($used_as_linktag == false) {
            /* remove comments */
            // $jsContent = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", '', $jsContent);
            /* remove tabs, spaces, newlines, etc. */
            $jsContent = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), '', $jsContent);
            /* remove other spaces before/after ) */
            $jsContent = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $jsContent);
            return '<script type="text/javascript">' . $jsContent . '</script>';
        }
        return $jsContent;
    }

    //Getting file content using curl
    function get_file_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    //Getting file from URL using curl
    function get_file_from_url($URL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    function remove_special_chars($string) {
        $specialChars = array("#", "$", "%", "^", "\\", "/", "!");
        $string = str_replace(' ', '_', $string);
        $string = str_replace("&", 'and', $string);
        $string = str_replace($specialChars, '', $string);
        return $string;
    }

    function crawling_meta($array) {
        if (!is_array($array) || empty($array)) {
            return "";
        }
        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $val = (array) $val;
            }
            if (is_array($val)) {
                foreach ($val as $row) {
                    if (is_array($row)) {
                        if (array_key_exists("is_crawlable", $row)) {
                            $uri_string = "/" . uri_string();

                            if ($row["is_crawlable"] == "n" && $row["new_url"] == $uri_string) {

                                if ($row["url_ref"][0] == "S") {
                                    return '<meta name="robots" content="noindex, nofollow" />';
                                } else {

                                    return '<meta name="robots" content="noindex, follow" />';
                                }
                            }
                        }
                    }
                }
            }
        }

        //Since court pages have different format of array
        //For court list page and its a static page
        if (isset($array["courts_type"][0]["is_crawlable"]) && $array["courts_type"][0]["is_crawlable"] == "n") {
            return '<meta name="robots" content="noindex, nofollow" />';
        }
        //For court detail page
        if (isset($array["courts_detail"]->is_crawlable) && $array["courts_detail"]->is_crawlable == "n") {
            return '<meta name="robots" content="noindex, follow" />';
        }
        return "";
    }

    function is_string_contain($array, $stringtocheck) {
        foreach ($array as $name) {
            if (stripos($stringtocheck, $name) !== FALSE) {
                return true;
            }
        }
    }

    function is_410_url() {
        $curent_url = $_SERVER['REQUEST_URI'];
        $curent_url = explode('?', $curent_url);
        $curent_url = $curent_url[0];

        $num_rows = $this->CI->db->select('url')
                        ->where('url', $curent_url)
                        ->where('jury_id', JURY_ID)
                        ->get('410_urls')->num_rows();
        if ($num_rows > 0)
            return true;
        return false;
    }

    function is_https() {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)
            return true;
        else
            return false;
    }

}
