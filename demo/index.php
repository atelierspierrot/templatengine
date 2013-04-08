<?php
// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL ^ E_NOTICE);

// set a default timezone to avoid PHP5 warnings
$dtmz = date_default_timezone_get();
date_default_timezone_set( !empty($dtmz) ? $dtmz:'Europe/Paris' );

// the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// the controller
require 'Controller.php';

$ctrl = new Controller;
$ctrl->distribute();


exit('ERROR IN RENDERING !');
// Endfile