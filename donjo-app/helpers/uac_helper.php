<?php
/**
 * Runs user access controll
 */
function run_uac()
{
	$ci = get_instance();

	$ci->config->load('aauth');
	$ci->config->load('aauth_override', $section=false, $failgrace=true);
	$config = array_merge($ci->config->item('aauth'), (array)$ci->config->item('aauth_override'));

	if ($ci->db->table_exists($config['users']))
	{
		$ci->load->library('aauth', $config, 'auth');
	}
	else
	{
		$ci->config->set_item('aauth', $config);
		$ci->config->set_item('aauth_override', null);
		$ci->config->load('aauth_init', $section=false, $failgrace=true);
	}
	$ci->auth->control(strtolower(implode('/',$ci->uri->rsegments)));
}

function get_perm($perm)
{
	$perm = strtolower(trim($perm, '/'));
	strpos($perm, '/') OR $perm .= '/index';
	return $perm;
}
