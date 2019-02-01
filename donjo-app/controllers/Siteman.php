<?php

class Siteman extends CI_Controller {

	public function index()
	{
		if ($this->auth->is_loggedin())
		{
			redirect(null);
		}

		!empty($_POST) && $this->auth();
		$this->load->model('header_model');
		$this->load->model('track_model');
		$header = $this->header_model->get_config();
		$this->load->view('siteman', $header);
		$this->track_model->track_desa('siteman');
	}

	private function auth()
	{
		$user = strtolower(trim($_POST['username']));
 		if (!$this->auth->login($user, $_POST['password']))
		{
			return;
		}

		$this->load->model('user_model');
 		$this->user_model->validate_admin_has_changed_password();
		$_SESSION['dari_login'] = '1';
		$uri = $this->session->return_uri;
		strpos($uri, 'siteman') === 0 && $uri = null;
		unset($_SESSION['return_uri']);
 		redirect($uri);
 	}
}
