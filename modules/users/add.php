<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Thêm người dùng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//Xử lý thêm
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $body = $_POST;
  //Validation form

  // Kiểm tra các trường có được nhập dữ liệu hay không
  if (empty($body['fullname'])) {
    $errors['fullname'] = "Vui lòng điền họ tên của bạn!";
  }

  if (empty($body['phone'])) {
    $errors['phone'] = "Vui lòng điền số điện thoại của bạn!";
  }

  if (empty($body['address'])) {
    $errors['address'] = "Vui lòng điền số địa chỉ của bạn!";
  }

  if (empty($body['username'])) {
    $errors['username'] = "Vui lòng điền địa chỉ của bạn!";
  } else {
    $username = $body['username'];
    $sql = "SELECT `id` FROM `tbl_users` WHERE `username`='$username'";
    if (getRows($sql) > 0) {
      $errors['username'] = "Tài khoản đã tồn tại trong hệ thống!";
    }
  }

  if (empty($body['password'])) {
    $errors['password'] = "Vui lòng điền mật khẩu của bạn!";
  }

  if (empty($errors)) {
    //Nếu dữ liệu đầu vào chuẩn
    $data = array(
      'fullname' => $body['fullname'],
      'phone' => $body['phone'],
      'address' => $body['address'],
      'username' => $body['username'],
      'password' => password_hash($body['password'], PASSWORD_DEFAULT),
    );
    $insertStatus = insert('tbl_users', $data);
    if ($insertStatus) {
      unset($_SESSION['old_data']);
      $_SESSION['msg'] = "Đã thêm người dùng mới thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . '?module=users');
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình thêm người dùng! Thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}

$old_data = $_SESSION['old_data'];
?>
<div id="wp-content">
  <div id="content" class="container-fluid">
    <div class="card">
      <div class="card-header font-weight-bold">
        Thêm người dùng
      </div>
      <div class="card-body">
        <p class="w-100 text-right">Ghi chú (*) - Bắt buộc phải nhập</p>
        <form method="POST">
          <div class="form-group">
            <label for="fullname">Tên người dùng (*)</label>
            <input class="form-control" type="text" value="<?php echo old('fullname', $old_data) ?>" name="fullname" id="fullname">
            <?php echo form_error('fullname', $errors) ?>
          </div>
          <div class="form-group">
            <label for="phone">Số điện thoại (*)</label>
            <input class="form-control" type="text" value="<?php echo old('phone', $old_data) ?>" name="phone" id="phone">
            <?php echo form_error('phone', $errors) ?>
          </div>
          <div class="form-group">
            <label for="address">Địa chỉ (*)</label>
            <textarea name="address" class="form-control" id="address" cols="30" rows="2"><?php echo old('address', $old_data) ?></textarea>
            <?php echo form_error('address', $errors) ?>
          </div>
          <div class="form-group">
            <label for="username">Tên đăng nhập (*)</label>
            <input class="form-control" type="text" value="<?php echo old('username', $old_data) ?>" name="username" id="username">
            <?php echo form_error('username', $errors) ?>
          </div>
          <div class="form-group">
            <label for="password">Mật khẩu (*)</label>
            <input class="form-control" type="password" name="password" id="password">
            <?php echo form_error('password', $errors) ?>
          </div>

          <button type="submit" name="btn_submit" class="btn btn-primary">Thêm mới</button>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>