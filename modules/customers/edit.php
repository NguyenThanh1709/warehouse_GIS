<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Cập nhật thông tin khách hàng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//Lấy id user 
$id = isset($_GET['id']) ? $_GET['id'] : null;
$customerList = firstRaw("SELECT * FROM `tbl_khachhang` WHERE `id`='$id'");

//Xử lý cập nhật
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $body = $_POST;
  //Validation form

  // Kiểm tra các trường có được nhập dữ liệu hay không
  if (empty($body['name'])) {
    $errors['name'] = "Vui lòng điền họ tên của bạn!";
  }

  if (empty($errors)) {
    //Nếu dữ liệu đầu vào chuẩn
    $data = array(
      'name' => $body['name'],
      'address' => $body['address'],
      'email' => $body['email'],
    );

    if (!empty($body['phone'])) {
      $data['phone'] = $body['phone'];
    }

    if (!empty($body['id_card'])) {
      $data['id_card'] = $body['id_card'];
    }


    $condition = "id ='$id'";

    $updateStatus = update('tbl_khachhang', $data, $condition);
    if ($updateStatus) {
      //huỷ dữ liệu cũ
      $_SESSION['msg'] = "Đã cập nhật thông tin thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . "?module=customers&action=edit&id=$id");
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi! Vui lòng thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}

$old_data = $_SESSION['old_data'];
if (empty($old_data)) {
  $old_data = $customerList;
}
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>
<div id="wp-content">
  <div id="content" class="container-fluid">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold">
        Thông tin khách hàng
      </div>
      <div class="card-body">
        <p class="w-100 text-right">Ghi chú (*) - Bắt buộc phải nhập</p>
        <form method="POST">
          <div class="form-group">
            <label for="name">Tên khách hàng (*)</label>
            <input class="form-control" type="text" value="<?php echo old('name', $old_data) ?>" name="name" id="name">
            <?php echo form_error('name', $errors) ?>
          </div>
          <div class="form-group">
            <label for="phone">Số điện thoại </label>
            <input class="form-control" type="text" value="<?php echo old('phone', $old_data) ?>" name="phone" id="phone">
            <?php echo form_error('phone', $errors) ?>
          </div>
          <div class="form-group">
            <label for="address">Địa chỉ</label>
            <textarea name="address" class="form-control" id="address" cols="30" rows="2"><?php echo old('address', $old_data) ?></textarea>
            <?php echo form_error('address', $errors) ?>
          </div>
          <div class="form-group">
            <label for="id_card">Chứng minh/Căn cước công dân</label>
            <input class="form-control" type="text" value="<?php echo old('id_card', $old_data) ?>" name="id_card" id="id_card">
            <?php echo form_error('id_card', $errors) ?>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="text" value="<?php echo old('email', $old_data) ?>" name="email" id="email">
            <?php echo form_error('email', $errors) ?>
          </div>

          <button type="submit" name="btn_submit" class="btn btn-primary">Cập nhật</button>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>