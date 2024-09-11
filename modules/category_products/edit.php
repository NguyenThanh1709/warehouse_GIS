<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$id = $_GET['id'];
$categoryDetail = firstRaw("SELECT * FROM `tbl_danhmuc_sanpham` WHERE `id` = '$id'");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $body = $_POST;

  //validation
  $errors = array();
  if (empty($body['name'])) {
    $errors['name'] = "Không được để trống trường này!";
  }

  if (empty($errors)) {
    //Dữ liệu cần update
    $data = array(
      'name' => $body['name'],
      'dscription' => $body['dscription']
    );

    //Câu lệnh update
    $updateStatus = update('tbl_danhmuc_sanpham', $data, "`id`='$id'");

    //Kiểm tra trạng thái update thành công hay thất bại
    if ($updateStatus) {
      $_SESSION['msg'] = "Đã cập nhật dữ liệu danh mục thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . "?module=category_products&view=edit&id=$id");
    } else {
      $_SESSION['msg'] = "Lỗi đã xảy ra trong quá trình cập nhật! Vui lòng thử lại sao!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}
$old_data = $_SESSION['old_data'] ?? '';
if (empty($old_data)) {
  $old_data = $categoryDetail;
}
?>
<form method="post">
  <div class="form-group">
    <label for="name">Tên danh mục sản phẩm (*)</label>
    <input class="form-control" type="text" value="<?php echo old('name', $old_data) ?>" name="name" id="name">
  </div>
  <div class="form-group">
    <label for="dscription">Mô tả</label>
    <textarea class="form-control" type="text" name="dscription" id="dscription"><?php echo old('dscription', $old_data) ?></textarea>
  </div>
  <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
</form>