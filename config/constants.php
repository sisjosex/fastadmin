<?php

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http";
$port = $_SERVER['SERVER_PORT'];

if($port != '80') {
    $port = ':' . $port;
} else {
    $port = '';
}

define('BASE_URL', $protocol . ':' . '//' . $_SERVER['SERVER_NAME'] . $port . '/phpadmin/');
$constants['BASE_URL'] = BASE_URL;

$default_controllers['admin'] = 'login';

define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_FOLDER_CONTROLLER', 'login');
define('DEFAULT_FUNCTION', 'index');
define('AUTH_COOKIE_EXPIRE', 3600*60*24);
define('AUTH_COOKIE_NAME', '_cocinamos');
define('DB_PREFIX', '');

define('MAIL_FROM_TITLE', '¿Cocinamos?');
define('MAIL_FROM_ADDRESS', 'info@cocinamos.com');

define('MAIL_ADMIN_ADDRESS', 'sisjosex@gmail.com');
