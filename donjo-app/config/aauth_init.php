<?php

		$ci = get_instance();
		$ci->load->dbforge();

		## Create Table aauth_group_to_group
		$ci->dbforge->add_field(array(
			'group_id' => array(
				'type' => 'INT',
				'constraint' => 9,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'subgroup_id' => array(
				'type' => 'INT',
				'constraint' => 9,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
		));

		$ci->dbforge->add_key("group_id",true);
		$ci->dbforge->add_key("subgroup_id",true);
		$ci->dbforge->create_table("aauth_group_to_group", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_group_to_group` ENGINE = InnoDB');

		## Create Table aauth_groups
		$ci->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 9,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => TRUE,
			),
			'definition' => array(
 				'type' => 'TEXT',
				'null' => TRUE,
			)
		));

		$ci->dbforge->add_key("name");
 		$ci->dbforge->add_key("id", true);
		$ci->dbforge->create_table("aauth_groups", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_groups` ENGINE = InnoDB');

		## Create Table aauth_login_attempts
		$ci->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'ip_address' => array(
				'type' => 'VARCHAR',
				'constraint' => 39,
				'null' => TRUE,
				'default' => '0',
			),
			'timestamp' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'count' => array(
				'type' => 'TINYINT',
				'constraint' => 2,
				'null' => TRUE,
				'default' => '0',
			),
		));

		$ci->dbforge->add_key("id",true);
		$ci->dbforge->create_table("aauth_login_attempts", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_login_attempts` ENGINE = InnoDB');

		## Create Table aauth_perm_to_group
		$ci->dbforge->add_field(array(
			'perm_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'group_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
		));

		$ci->dbforge->add_key("perm_id",true);
		$ci->dbforge->add_key("group_id",true);
		$ci->dbforge->create_table("aauth_perm_to_group", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_perm_to_group` ENGINE = InnoDB');

		## Create Table aauth_perm_to_user
		$ci->dbforge->add_field(array(
			'perm_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
		));

		$ci->dbforge->add_key("perm_id", true);
		$ci->dbforge->add_key("user_id", true);
		$ci->dbforge->create_table("aauth_perm_to_user", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_perm_to_user` ENGINE = InnoDB');

		## Create Table aauth_perms
		$ci->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => TRUE,
			),
			'definition' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));

		$ci->dbforge->add_key("name");
		$ci->dbforge->add_key("id",true);
		$ci->dbforge->create_table("aauth_perms", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_perms` ENGINE = InnoDB');

		## Create Table aauth_user_to_group
		$ci->dbforge->add_field(array(
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'group_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
		));

		$ci->dbforge->add_key("user_id",true);
		$ci->dbforge->add_key("group_id",true);
		$ci->dbforge->create_table("aauth_user_to_group", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_user_to_group` ENGINE = InnoDB');

		## Create Table aauth_user_variables
		$ci->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'data_key' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE,
			),
			'value' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));

		$ci->dbforge->add_key("id",true);
		$ci->dbforge->create_table("aauth_user_variables", TRUE);
		$ci->db->query('ALTER TABLE  `aauth_user_variables` ENGINE = InnoDB');

		## Create Table aauth_users
		$ci->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE,
			),
			'pass' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => FALSE,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => TRUE,
			),
			'banned' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => '0',
			),
			'last_login' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'last_activity' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'date_created' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'forgot_exp' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'remember_time' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'remember_exp' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'verification_code' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'totp_secret' => array(
				'type' => 'VARCHAR',
				'constraint' => 16,
				'null' => TRUE,
			),
			'ip_address' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));

		$ci->dbforge->add_key("id",true);
		$ci->dbforge->add_key("username");
 		$ci->dbforge->create_table("aauth_users", TRUE);
		$ci->db->query( 'ALTER TABLE  `aauth_users` ENGINE = InnoDB');

		require __DIR__ .'/aauth_data.php';
		$ci->load->library('aauth', $ci->config->item('aauth_override'), 'auth');

		$ci->auth->create_group('public', 'Grup pengguna publik');
		$ci->auth->create_group('warga', 'Grup pengguna untuk pengguna baru');

		foreach ($ci->db->get('user_grup')->result() as $old_grup) 
		{
			$grup = strtolower($old_grup->nama);

			if (!$id = $ci->auth->get_group_id($grup))
			{
				$id = $ci->auth->create_group($grup, ucfirst($grup));
			}

			$group_ids[$old_grup->id] = $id;
			$grup == 'public' OR $ci->auth->add_subgroup($grup, 'public');
		}

		foreach ($ci->db->get('user')->result() as $old_user)
		{
			if (!$new_user = $ci->auth->get_user_id($old_user->email))
			{
				$ci->db->insert('aauth_users', array(
						'email' => $old_user->email,
						'username' => strtolower($old_user->username),
						'pass' => $old_user->password
				));

				$new_user = $ci->db->insert_id();
			}

			if (isset($group_ids[$old_user->id_grup]))
			{
				$old_user->id == $ci->session->user && $admin = $new_user;
				$ci->auth->add_member($new_user, $group_ids[$old_user->id_grup]);
			}

			$old_user->active OR $ci->auth->ban_user($new_user);

			$ci->auth->set_user_vars(array(
					'nama' => $old_user->nama,
					'foto' => $old_user->foto, 
			), $new_user);
		}

		foreach ($all_perms as $perm)
		{
			$perm = get_perm($perm);

			if (!$ci->auth->get_perm_id($perm))
			{
				$def = explode('/',str_replace('_',' ',$perm));
				$def = ucfirst($def[1]) .' '. $def[0];
				$ci->auth->create_perm($perm, $def);
			}
		}

		foreach ($public_perms as $perm)
		{
			$ci->auth->allow_group('public', get_perm($perm));
		}

		foreach ($groups_main_perms as $group => $perm)
		{
			$perm = get_perm('main_perm:'. $perm);
			$ci->auth->create_perm($perm);
			$ci->auth->allow_group($group, $perm);
		}

		$ci->db->delete('setting_aplikasi', array('key'=>'auth'));
		$ci->db->insert('setting_aplikasi', array('key'=>'auth', 'value'=>1, 'jenis'=>'hidden'));

		// migrate down old auth tables
		// ....

		$ci->auth->login_fast($admin);
