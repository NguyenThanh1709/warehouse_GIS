<?php
//Thiết lặp hằng số mặc định
const _MODULE_DEFAULT = 'dashboard';
const _ACTION_DEFAULT = 'list';

//Thiết lập tên dự án
const _NAME_PROJECT = 'Hệ thống thông tin Địa Lý';

// Thiết lập HOST
define('_WEB_HOST_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/warehouse_httdl'); //Địa chỉ trang chủ

define('_WEB_HOST_TEMPLATE', _WEB_HOST_ROOT . '/template');

// echo _WEB_HOST_TEMPLATE_ADMIN;
//Thiết lập Path
define('_WEB_PATH_ROOT', __DIR__);
define('_WEB_PATH_TEMPLATE', _WEB_PATH_ROOT . '/template');
