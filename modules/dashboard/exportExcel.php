<?php
require_once  _WEB_PATH_ROOT . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
  'STT',
  'Tên sản phẩm',
  'Số lượng',
];

$data = $_SESSION['listData'] ?? [];
$nameContact = $_SESSION['name_contact'];
$nameWarehouse = $_SESSION['name'];
// Tiêu đề bảng
$sheet->setCellValue([1, 1], "DANH SÁCH SẢN PHẨM TỒN KHO");
$sheet->setCellValue([1, 2], "( $nameWarehouse / $nameContact)");

// Tiêu đề cột (STT, Tên sản phẩm, Số lượng)
foreach ($headers as $index => $value) {
  $sheet->setCellValue([$index + 1, 4], $value);
}

// Dữ liệu sản phẩm
$rows = 4; // Bắt đầu từ dòng 4 vì dòng 3 là tiêu đề
foreach ($data as $key => $item) {
  $cols = 0;
  $rows++; // Tăng dòng mỗi lần lặp

  $sheet->setCellValue([++$cols, $rows], $key + 1);

  $sheet->setCellValue([++$cols, $rows], $item['name_product']);

  $sheet->setCellValue([++$cols, $rows], $item['quantity']);
}

unset($_SESSION['listData']); //Xoá dữ liệu khỏi session
unset($_SESSION['name_contact']);
unset($_SESSION['name']);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="file_' . time() . '.xlsx"');
$writer->save('php://output');
