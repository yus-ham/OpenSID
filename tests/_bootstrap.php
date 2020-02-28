<?php

define('ENVIRONMENT', 'testing');
define('APP_URL', getenv('APP_BASE_URL') ?: 'http://localhost');
define('BASEPATH', codecept_root_dir('system'));
define('FCPATH', codecept_root_dir());
