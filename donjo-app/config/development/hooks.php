<?php

is_file(__DIR__ .'/../production/hooks.php') && require __DIR__ .'/../production/hooks.php';

$hook['pre_controller'][] = function()
{
		if (config_item('no_auth'))
		{
   error_reporting(30711);
   session_start();
    $_SESSION['sesi'] = 'a8d4080245664ed2049c1b2ded7cac30';
				$_SESSION['siteman'] = 1;
				
				$_SESSION['user'] = 1;
				$_SESSION['grup'] = 1;
				$_SESSION['per_page'] = 10;
		}
};

$hook['display_override...'] = array(function()
{
	$CI = CI_Controller::get_instance();
	$path = FCPATH.'vendor/ci-debug';
	$CI->load->add_package_path($path);
	require_once $path .'/hooks/debug_toolbar.php';
	(new debug_toolbar)->render();
	$CI->hooks->call_hook('post_system');
});

$hook['post_controller_constructor'][] = function()
{
	log_message('debug', 'Call uac::run()');
};
