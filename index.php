<?php
if (getcwd() == '/') {
  define('SYSTEM_ROOT_PATH', '');
}
else {
  define('SYSTEM_ROOT_PATH', getcwd());
}
require(__DIR__."/core/boot.php");
