<?php

date_default_timezone_set('Europe/Rome');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application
 * /var/www/evolutionZf/application/includescostants.php
 *  */
require_once ('includes/constants.php');
//require_once 'includes/s1_constants.php';
require_once 'includes/image.php';
require_once 'includes/data.php';
require_once 'includes/research.php';
require_once 'includes/troopers.php';
require_once 'includes/gods.php';
require_once 'includes/functions.php';
require_once 'Zend/Application.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
