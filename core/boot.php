<?php

/**
 * Sprawdzenie wersji PHP.
 */     
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    echo 'Aplikacja wymaga PHP w wersji <strong>7.4</strong>! Serwer korzysta z PHP w wersji <strong>'.PHP_VERSION.'</strong>! Zaktualizuj wersję PHP na serwerze!';
    exit;
}

// =====================================================================================================================

setlocale(LC_TIME, 'pl_PL.UTF8');
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Warsaw');
set_include_path('.' . PATH_SEPARATOR . '.' . PATH_SEPARATOR . get_include_path());
define('DS', DIRECTORY_SEPARATOR);
session_start();
require SYSTEM_ROOT_PATH . DS.'core'.DS.'classes'.DS.'folder'.DS.'qFolderScan.php';
require SYSTEM_ROOT_PATH . DS.'core'.DS.'classes'.DS.'qLoader.php';
spl_autoload_register(array('qLoader', 'autoload'));
qTimeExec::init();

$cfgPath = __DIR__.DS.'config.php';
qConfig::add($cfgPath);

$domain = $_SERVER['SERVER_NAME'];
$core_path = __DIR__;
set_error_handler(array('qErrorHandler', 'error_handler'));
qConfig::setSys('http_host', $_SERVER['HTTP_HOST']);
qConfig::setSys('server_name', $_SERVER['SERVER_NAME']);
qConfig::setSys('domain', $domain);
qConfig::setSys('file_name', dirname($_SERVER['SCRIPT_FILENAME']));
qConfig::setSys('base_path', qConfig::get('url.base', ''));
qConfig::setSys('oryginal', $_SERVER['PHP_SELF']);
if (isset($_SERVER['HTTP_REFERER'])) {
    qConfig::setSys('back', $_SERVER['HTTP_REFERER']);
}
// Set base_url
qConfig::setSys('base_url', qCtrl::baseUrl());

//konfiguracja ścieżek do mixed

//$install = new qInstall();
//if (!$install->check()) {
//    echo $install->html();
//    exit;
//}

require __DIR__.'/core.php';
