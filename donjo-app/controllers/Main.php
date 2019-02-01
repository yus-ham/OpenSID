<?php

class Main extends CI_Controller {

	/**
	 * Redirects user to user home or group home.
	 *
	 * @see group_home()
	 */
	public function index()
	{
		if (!$home = $this->auth->get_user_var('home'))
		{
 			$home = $this->group_home();
		}
		redirect($home);
	}

	private function group_home()
	{
		$groups = $this->auth->get_user_groups();
		$perms = $this->auth->get_group_perms(current($groups)->id);

		foreach ($perms as $perm)
		{
			if ($pos = strpos($perm->name, ':'))
			{
				return substr($perm->name, $pos+1);
			}
		}
	}
}
