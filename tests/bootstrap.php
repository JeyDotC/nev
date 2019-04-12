<?php

/**
 * @author Win 10
 */
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

// put your code here

$loader = require_once '../vendor/autoload.php';
$loader->set('Nev\\Tests', __DIR__);
define('DATA_DIR', __DIR__ . '/data');