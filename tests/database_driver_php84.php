<?php

define('BASEPATH', dirname(__DIR__).'/system/');
define('ENVIRONMENT', 'testing');

function log_message($level, $message)
{
    // Stub logging agar constructor database dapat diuji secara terisolasi.
}

require BASEPATH.'database/DB_driver.php';
require BASEPATH.'database/DB_query_builder.php';

class CI_DB extends CI_DB_query_builder {}

require BASEPATH.'database/drivers/mysqli/mysqli_driver.php';

$params = array(
    'dsn' => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'zyacbt',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => FALSE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE,
);

$deprecations = array();
set_error_handler(function ($severity, $message) use (&$deprecations) {
    if ($severity === E_DEPRECATED) {
        $deprecations[] = $message;
    }

    return TRUE;
});

$driver = new CI_DB_mysqli_driver($params);
restore_error_handler();

if (!property_exists($driver, 'failover')) {
    fwrite(STDERR, "Property failover belum dideklarasikan.\n");
    exit(1);
}

if ($deprecations !== array()) {
    fwrite(STDERR, implode("\n", $deprecations)."\n");
    exit(1);
}

echo "Database driver PHP 8.4 regression check passed.\n";
