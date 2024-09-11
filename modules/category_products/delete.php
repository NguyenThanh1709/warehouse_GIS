<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

//Lấy id nhà cung cấp theo phương thức GET
$id = isset($_GET['id']) ? $_GET['id'] : null;

//Gọi hàm delete dữ liệu
$deleteStatus = delete('tbl_danhmuc_sanpham', "`id`='$id'");

//Kiểm tra trạng thái xoá 
if ($deleteStatus) {
  $_SESSION['msg'] = "Đã xoá dữ liệu thành công!";
  $_SESSION['msg_style'] = "success";
} else {
  $_SESSION['msg'] = "Xoá dữ liệu thất bại!";
  $_SESSION['msg_style'] = "danger";
}

//Chuyển hướng
redirect(_WEB_HOST_ROOT . "?module=category_products");
