<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

$deleteStatus = delete('tbl_xuathang', "`id`='$id'");

if ($deleteStatus) {
  $_SESSION['msg'] = "Đã xoá dữ liệu thành công!";
  $_SESSION['msg_style'] = "success";
} else {
  $_SESSION['msg'] = "Xoá dữ liệu thất bại!";
  $_SESSION['msg_style'] = "danger";
}

redirect(_WEB_HOST_ROOT . "?module=export");
