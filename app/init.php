<?php

// Start session
session_start();

// Root path for inclusion.
define('INC_ROOT', dirname(__DIR__));

// Parameters
define('PARAMS', INC_ROOT.'/config.inc.php');


// Require composer autoloader
require_once INC_ROOT . '/vendor/autoload.php';
// Require core files
require_once INC_ROOT . '/app/core/App.php';
require_once INC_ROOT . '/app/core/Controller.php';

// Require project main controller
require_once INC_ROOT . '/app/controllers/Projector.php';

//Root URL
define('HTTP_ROOT',
    'https://'.$_SERVER['HTTP_HOST'].
    str_replace(
        $_SERVER['DOCUMENT_ROOT'],
        '',
        str_replace('\\', '/', INC_ROOT).'/web'
    )
);

// Root path for assets
define('ASSET_ROOT',
    'https://'.$_SERVER['HTTP_HOST'].
    str_replace(
        $_SERVER['DOCUMENT_ROOT'],
        '',
        str_replace('\\', '/', INC_ROOT).'/web/assets'
    )
);
