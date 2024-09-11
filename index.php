<?php
@session_start();
@ob_start();

require_once 'config.php';
require_once 'includes/conn.php';
require_once 'includes/database.php';
require_once 'includes/function.php';

//Khởi tạo biến 
$module = _MODULE_DEFAULT;
$action = _ACTION_DEFAULT;

if (!empty($_GET['module'])) {
    if (is_string($_GET['module'])) {
        $module = trim($_GET['module']);
    }
}

if (!empty($_GET['action'])) {
    if (is_string($_GET['action'])) {
        $action = trim($_GET['action']);
    }
}

// <!-- Điều hướng -->
$path = "modules/$module/$action.php";

if (file_exists($path)) {
    require $path;
} else {
    require 'modules/error/404.php';
}
