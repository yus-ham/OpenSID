<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Log extends CI_Log
{
	public $logs = array();

	public function __construct()
	{
		parent::__construct();

		if (ENVIRONMENT === 'development')
		{
			session_start();
			$ajax = strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' ? ' (AJAX)' : null;
			$message = "\n================================================================================================\n";
			$message .= sprintf("Request%s: %s %s\n", $ajax, $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
			$message .= "\nSession: ". session_id() ."\n". print_r($_SESSION,8);
			$this->write_log('debug', $message);
		}
	}

	public function _format_line($level, $date, $message)
	{
		return str_pad("[$level]", 7) . " $date --- $message\n";
	}

	public function write_log($level = 'error', $msg = '', $php_error = FALSE)
	{
		parent::write_log($level, $msg, $php_error);

		if (ENVIRONMENT === 'development')
		{
			//$memory	 = (!function_exists('memory_get_usage')) ? '0' : memory_get_usage();
			$b = load_class('Benchmark', 'core');
			$b->mark($msg);
			$this->logs[] = array(date('Y-m-d H:i:s P'), $level, $msg);
		}
	}
}
