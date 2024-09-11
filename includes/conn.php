<?php
// Kết nối CSDL bằng PDO

const _HOST = 'localhost';
const _USER = 'root';
const _PASS = '';
const _DB = 'db_kho';
const _DRIVER = 'mysql';

try {
  // Kiểm tra class PDO đã bật hay chưa
  if (class_exists('PDO')) {
    $dsn = _DRIVER . ':dbname=' . _DB . ';host=' . _HOST;
    $option = [
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAME utf8', //SET UTF cho cơ sở dữ liệu
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION  //Đẩy lỗi vào ngoại lệ khi truy vấn
    ];
    $conn = new PDO($dsn, _USER, _PASS);
  }
} catch (Exception $exception) {
  require_once 'modules/error/database.php';
  die();
}
