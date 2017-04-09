<?php
	 $header = $this->load->view('admin/elements/header', '',  TRUE);	 
	 $menu = $this->load->view('admin/elements/menu', '',  TRUE);	 
	 $content = $this->load->view('admin/elements/content', '',  TRUE);	 
	 $footer = $this->load->view('admin/elements/footer', '',  TRUE);
	 echo $header;
	 echo $menu;
	 echo $content;
	 echo $footer;
	 
?>

