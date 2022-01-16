<?php

$config['volume']  = 'default';
$config['theme']    = 'default';
// parametr konfiguracji stronicowania
$config['page']     = 'page-';
$config['sort']     = 'sort-';

// ścieżki do plików
$config['path.root'] =  SYSTEM_ROOT_PATH; 
$config['path.base'] = $config['path.root'];
$config['path.log']  = $config['path.root'].DIRECTORY_SEPARATOR.'log';

$config['path.subway'] = '';   // '/crm';  - w przypadku podkatalogu na localhost

// konfiguracja domeny głównej
$config['domain.type'] = 'http'; // option: 'http', 'https';
$config['domain.www']  = 'remain'; // option: 'add', 'remove', 'remain' ;
$config['domain.domain'] = $_SERVER['SERVER_NAME'].$config['path.subway'];

$config['url.base']    = $config['domain.type'] . '://' . $_SERVER['SERVER_NAME'] . $config['path.subway'];
$config['url.libs']    = $config['url.base'].'/lib';
$config['url.public']  = $config['url.base'].'/public';
$config['url.theme']   = $config['url.base'].'/theme';

// ścieżki względne
$config['path.module'] = $config['path.root'].DIRECTORY_SEPARATOR.'module';
$config['path.modules'][] = $config['path.root'].DIRECTORY_SEPARATOR.'module';


$config['path.core']   = $config['path.root'].DIRECTORY_SEPARATOR.'core';
$config['path.css']    = $config['path.base'].DIRECTORY_SEPARATOR.'css';
$config['path.js']     = $config['path.base'].DIRECTORY_SEPARATOR.'js';
$config['path.theme']  = $config['path.root'].DIRECTORY_SEPARATOR.'theme';
$config['path.file']   = $config['path.root'].DIRECTORY_SEPARATOR.'file';
$config['path.public'] = $config['path.root'].DIRECTORY_SEPARATOR.'public';

// upload
$config['url.upload']  = $config['url.base'].'/upload';
$config['alias.upload'] = 'upload://';
$config['path.upload'] = $config['path.root'].DIRECTORY_SEPARATOR.'upload';

$config['url.upload2']  = $config['url.base'].'/upload';

$config['alias.private'] = 'private://';
$config['path.private'] = $config['path.root'].DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR;

$config['path.libs']   = 'lib';

$config['theme.default'] = 'front';
$config['theme.admin.default'] = 'admin';

// images
$config['path.images']  = $config['path.upload'].DIRECTORY_SEPARATOR.'images';
$config['url.images']   = $config['url.upload'].'/images';
$config['alias.images'] = 'upload://images';

$config['url.images2']   = $config['url.upload2'].'/images';

// konfiguracja języka
$config['lang'] = 'pl'; // domyślny język
$config['lang.show.default'] = false; // pokazuj w adresie domyślny język
$config['lang.label'] = 'polski';

$config['domain.current.short'] = false;

$config['hash'] = 'jkasdhuianys72y3nhsjkh32yiuqhqw73uhwjdjashg7ey237';

$config['sql.connect']['name'] = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'name' => 'struktury',
    'user' => 'root',
    'pass' => '',
    'prefix' => '',
];



return $config;
