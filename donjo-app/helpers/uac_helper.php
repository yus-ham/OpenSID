<?php
/**
 * Runs user access controll
 */
function run_uac()
{
	$ci = get_instance();
	$ci->config->load('aauth_override', $section=false, $failgrace=true);
	$ci->load->library('aauth', $ci->config->item('aauth_override'), 'auth');
	$ci->auth->control(strtolower(implode('/',$ci->uri->rsegments)));
}

function get_perm($perm)
{
	$perm = strtolower(trim($perm, '/'));
	strpos($perm, '/') OR $perm .= '/index';
	return $perm;
}
