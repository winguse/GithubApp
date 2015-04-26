<?php
define('GITHUB_CLIENT_ID', '');
define('GITHUB_CLIENT_SECRET', '');
define('GITHUB_API_BASE_URL', 'https://api.github.com');
define('GITHUB_AUTH_URL', 'https://github.com/login/oauth/authorize');
define('GITHUB_ACCESS_TOKEN_URL', 'https://github.com/login/oauth/access_token');
define('GITHUB_USER_URL', GITHUB_API_BASE_URL.'/user');

define('APP_NAME','Winguse\'s Github App');
define('APP_DOMAIN', 'winguse.com');
define('APP_BASE_PATH','/app/github');
define('APP_SECRET_KEY', '');
define('APP_AUTH_SCOPE', 'repo');
define('APP_DB_USER', 'winguse_github');
define('APP_DB_PASSWORD', '');
define('APP_DB_NAME', 'winguse_github_app');
define('APP_DB_HOST', 'localhost');
define('APP_REAL_NAME_REGEX', "/^[\x7f-\xff]{2,}$/");
define('APP_GRADE_MIN', 2012);
define('APP_GRADE_MAX', 2015);
define('APP_STUDENT_ID_LENGTH', 9);


define('PERMISSION_NONE', 0);
define('PERMISSION_USER', 1);
define('PERMISSION_STUDENT', 2);
define('PERMISSION_TEACHER', 4);
define('PERMISSION_ADMIN', 8);