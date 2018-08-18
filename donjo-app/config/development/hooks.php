<?php

$hook['display_override'] = array(function()
{
	$CI = CI_Controller::get_instance();
	$path = APPPATH .'third_party/ci-debug';
	$CI->load->add_package_path($path);
	require_once $path .'/hooks/debug_toolbar.php';
	(new debug_toolbar)->render();
	$CI->hooks->call_hook('post_system');
});
