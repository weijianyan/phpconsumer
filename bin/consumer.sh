#! /usr/bin/php
<?php
define('PATH', dirname(__DIR__));
include PATH . '/Core/autoload.php';
$main_pro = new \Process\MainPro();
echo "main pid: {$main_pro->getPid()}\n";