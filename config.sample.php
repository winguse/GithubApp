<?php
define('GITHUB_CLIENT_ID', '');
define('GITHUB_CLIENT_SECRET', '');
define('GITHUB_API_BASE_URL', 'https://api.github.com');
define('GITHUB_AUTH_URL', 'https://github.com/login/oauth/authorize');
define('GITHUB_ACCESS_TOKEN_URL', 'https://github.com/login/oauth/access_token');
define('GITHUB_USER_URL', GITHUB_API_BASE_URL.'/user');

define('APP_NAME','winguse\'s github app');
define('APP_DOMAIN', 'winguse.com');
define('APP_BASE_PATH','/app/github/');
define('APP_SECRET_KEY', '');
define('APP_AUTH_SCOPE', 'repo');
define('APP_DB_USER', '');
define('APP_DB_PASSWORD', '');
define('APP_DB_NAME', '');

define('ROLE_NONE', 0);
define('ROLE_ADMIN', 1);
define('ROLE_STUDENT', 2);
define('ROLE_TEACHER', 3);