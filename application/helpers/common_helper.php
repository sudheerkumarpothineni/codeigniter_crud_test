<?php

function debug($value=array()){
echo "<pre>";
print_r($value);
}

function file_upload($field){
	// debug($value);exit('common');
	// var $CI;
	$CI = & get_instance();
	$config['upload_path']          = upload_path;
    $config['allowed_types']        = allowed_types;
    $config['max_size']             = max_size;
    $config['max_width']            = max_width;
    $config['max_height']           = max_height;
    // print_r($config);exit;
    $CI->load->library('upload');
    $CI->upload->initialize($config);

    if ( ! $CI->upload->do_upload($field))
    {
            $data = array('error' => $CI->upload->display_errors());

    }
    else
    {
            $data = array('upload_data' => $CI->upload->data());

    }
    return $data;
}
?>