<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

//Khởi tạo mảng 
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $body = $_POST;

  //validation

  if (empty($body['name'])) {
    $errors['name'] = "Không được để trống trường này!";
  }

  if (empty($errors)) {
    $data = array(
      'name' => $body['name'],
      'dscription' => $body['dscription']
    );
    $insertStatus = insert('tbl_danhmuc_sanpham', $data);

    if ($insertStatus) {
      $_SESSION['msg'] = "Đã thêm danh mục sản phẩm thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . '?module=category_products');
    } else {
      $_SESSION['msg'] = "Lỗi đã xảy ra trong quá trình thêm! Vui lòng thử lại sao!";
      $_SESSION['msg_style'] = "danger";
    }
  }
}

?>
<form method="post">
  <div class="form-group">
    <label for="name">Tên danh mục sản phẩm (*)</label>
    <input class="form-control" type="text" name="name" id="name">
    <?php echo form_error('name', $errors) ?>
  </div>
  <div class="form-group">
    <label for="dscription">Mô tả</label>
    <textarea class="form-control" type="text" name="dscription" id="dscription"></textarea>
  </div>
  <button type="submit" class="btn btn-sm btn-primary">Thêm mới</button>
</form>