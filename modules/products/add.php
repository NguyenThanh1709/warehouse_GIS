<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Thêm sản phẩm',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//Lấy dữ liệu hiển thị ra select
$listCategorys = getRaw("SELECT * FROM `tbl_danhmuc_sanpham`");
$listSuppliers = getRaw("SELECT * FROM `tbl_nhacungcap`");

//Xử lý cập nhật tác vụ xử lý
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $body = $_POST;

  //Validation
  if (empty($body['name'])) {
    $errors['name'] = "Vui lòng điền tên sản phẩm";
  }

  if (empty($body['import_price'])) {
    $errors['import_price'] = "Vui lòng điền giá nhập của sản phẩm";
  }

  if (empty($body['export_price'])) {
    $errors['export_price'] = "Vui lòng điền giá xuất của sản phẩm";
  }

  if (empty($body['unit'])) {
    $errors['unit'] = "Vui lòng điền đơn vị tính của sản phẩm";
  }

  if (empty($body['id_supplier'])) {
    $errors['id_supplier'] = "Vui lòng chọn nhà cung cấp sản phẩm";
  }

  if (empty($body['id_category'])) {
    $errors['id_category'] = "Vui lòng chọn danh mục sản phẩm";
  }

  // Xử lý tải lên hình ảnh
  $thumbnail = "";
  if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])) {
    $upload_dir = 'uploads/';
    // Đường dẫn của file sau khi upload
    $thumbnail = $upload_dir . $_FILES['file']['name'];
    if (move_uploaded_file($_FILES['file']['tmp_name'], $thumbnail)) {
      // Đoạn code sau khi tải lên file thành công
    } else {
      // Xử lý lỗi khi tải lên file
    }
  }

  if (empty($errors)) {
    $data = array(
      'name' => $body['name'],
      'import_price' => $body['import_price'],
      'export_price' => $body['export_price'],
      'unit' => $body['unit'],
      'id_supplier' => $body['id_supplier'],
      'id_category' => $body['id_category'],
    );

    //Nếu có ghi chú
    if (!empty($body['note'])) {
      $data['note'] = $body['note'];
    }

    //Nếu có ảnh kèm theo
    if (!empty($thumbnail)) {
      $data['thumbnail'] = $thumbnail;
    }

    //Thực hiên thêm vào cơ sở dữ liệu
    $insertStatus = insert('tbl_sanpham', $data);

    //Kiểm tra thêm thành công hay thất bại
    if ($insertStatus) {
      unset($_SESSION['old_data']);
      $_SESSION['msg'] = "Đã thêm sản phẩm mới thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . '?module=products'); //Chuyển hướng
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình thêm người dùng! Thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body; //Lưu dữ liệu cũ hiển thị vào input đã nhập
  }
}

$old_data = $_SESSION['old_data']; //Gán dữ liệu vào biên $old_data để hiển thị lỗi 

//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div id="content" class="container-fluid">
    <div class="card">
      <div class="card-header font-weight-bold">
        Thêm sản phẩm
      </div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="name">Tên sản phẩm (*)</label>
                <input class="form-control" type="text" value="<?php echo old('name', $old_data) ?>" name="name" id="name">
                <?php echo form_error('name', $errors) ?>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label for="import_price">Giá nhập (*)</label>
                    <input class="form-control" type="text" value="<?php echo old('import_price', $old_data) ?>" name="import_price" id="import_price">
                    <?php echo form_error('import_price', $errors) ?>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group">
                    <label for="export_price">Giá xuất (*)</label>
                    <input class="form-control" type="text" value="<?php echo old('export_price', $old_data) ?>" name="export_price" id="export_price">
                    <?php echo form_error('export_price', $errors) ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <label for="">Danh mục sản phẩm (*)</label>
                <select name="id_category" class="form-control" id="">
                  <option value="0">----Chọn danh mục---</option>
                  <?php foreach ($listCategorys as $item) : ?>
                    <option <?php echo !empty(old('id_category', $old_data)) && old('id_category', $old_data) == $item['id'] ? "selected" : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <?php echo form_error('id_category', $errors) ?>
              </div>
              <div class="form-group">
                <label for="">Nhà cung cấp (*)</label>
                <select name="id_supplier" class="form-control" id="">
                  <option value="0">---Chọn nhà cung cấp---</option>
                  <?php foreach ($listSuppliers as $item) : ?>
                    <option <?php echo !empty(old('id_supplier', $old_data)) && old('id_supplier', $old_data) == $item['id'] ? "selected" : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <?php echo form_error('id_supplier', $errors) ?>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="intro">Ghi chú sản phẩm</label>
            <textarea name="note" class="form-control" id="intro" cols="30" rows="5"></textarea>
          </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label for="unit">Đơn vị tính (*)</label>
                <input class="form-control" type="text" name="unit" id="unit">
                <?php echo form_error('unit', $errors) ?>
              </div>
            </div>
            <div class="col-9">
              <div class="form-group">
                <label for="thumbnail">Ảnh sản phẩm </label>
                <input type="file" name="file" id="file" class="form-control form-file">
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Thêm mới</button>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>