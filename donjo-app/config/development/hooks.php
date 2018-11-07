<?php

$hooks_php = __DIR__ .'/../production/hooks.php';

if (is_file($hooks_php)) {
	require $hooks_php;
}

$hook['pre_controller'][] = function()
{
	session_start();
	log_message('debug', 'Session: '. session_id() ."\n". print_r($_SESSION,8));
};

$hook['display_override'] = array(function()
{
	$CI = CI_Controller::get_instance();
	$path = 'vendor/ci-debug';
	$CI->load->add_package_path($path);
	require_once $path .'/hooks/debug_toolbar.php';
	(new debug_toolbar)->render();
	$CI->hooks->call_hook('post_system');
});
