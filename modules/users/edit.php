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

//Lấy id user 
$id = isset($_GET['id']) ? $_GET['id'] : null;
$userDetail = firstRaw("SELECT * FROM `tbl_users` WHERE `id`='$id'");

//Xử lý cập nhật
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

  if (!empty($body['new_password'])) {
    if ($body['new_password'] != $body['new_password_cf']) {
      $errors['new_password'] = "Mật khẩu không khớp. Vui lòng nhập lại!";
    }
  }

  if (empty($errors)) {
    //Nếu dữ liệu đầu vào chuẩn
    $data = array(
      'fullname' => $body['fullname'],
      'phone' => $body['phone'],
      'address' => $body['address'],
    );

    if (!empty($body['new_password'])) {
      $data = array(
        'fullname' => $body['fullname'],
        'phone' => $body['phone'],
        'address' => $body['address'],
        'password' => password_hash($body['new_password'], PASSWORD_DEFAULT),
      );
    }

    $condition = "`id` = '$id'";
    $updateStatus = update('tbl_users', $data, $condition);
    if ($updateStatus) {
      //huỷ dữ liệu cũ
      $_SESSION['msg'] = "Đã cập nhật thông tin thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . "?module=users&action=edit&id=$id");
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình thêm người dùng! Thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}

$old_data = $_SESSION['old_data'];
if (empty($old_data)) {
  $old_data = $userDetail;
}
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>
<div id="wp-content">
  <div id="content" class="container-fluid">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold">
        Thông tin người dùng
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
            <input class="form-control" type="text" readonly value="<?php echo old('username', $old_data) ?>" name="username" id="username">
            <?php echo form_error('username', $errors) ?>
          </div>
          <div class="form-group">
            <label for="new_password">Mật khẩu mới</label>
            <input class="form-control" type="text" name="new_password" id="new_password">
            <?php echo form_error('new_password', $errors) ?>
          </div>
          <div class="form-group">
            <label for="new_password_cf">Nhập lại mật khẩu mới</label>
            <input class="form-control" type="text" name="new_password_cf" id="new_password_cf">
            <?php echo form_error('new_password_cf', $errors) ?>
          </div>

          <button type="submit" name="btn_submit" class="btn btn-primary">Thêm mới</button>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>