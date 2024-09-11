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
      'name' => $body['name']
    );
    $insertStatus = insert('tbl_nhacungcap', $data);

    if ($insertStatus) {
      $_SESSION['msg'] = "Đã thêm nhà cung cấp thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . '?module=suppliers');
    } else {
      $_SESSION['msg'] = "Lỗi đã xảy ra trong quá trình thêm! Vui lòng thử lại sao!";
      $_SESSION['msg_style'] = "danger";
    }
  }
}

?>
<form method="post">
  <div class="form-group">
    <label for="name">Tên nhà cung cấp (*)</label>
    <input class="form-control" type="text" name="name" id="name">
    <?php echo form_error('name', $errors) ?>
  </div>
  <button type="submit" class="btn btn-sm btn-primary">Thêm mới</button>
</form>