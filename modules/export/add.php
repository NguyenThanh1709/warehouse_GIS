<?php
//Kiểm tra trạng thái đăng xuất
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Thêm đơn xuất hàng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//Lấy ID người dùng đăng nhập
$user_id = $_SESSION['user_login'];

//Lấy tên module
$module = $_GET['module'];

//Mã đơn hàng
$timeCurrent = time();

//Lấy danh sách dữ liệu nhà cung cấp
$listCustomer = getRaw("SELECT * FROM `tbl_khachhang`");

//Lấy danh sách sản phẩm
$listProduct = getRaw("SELECT * FROM `tbl_sanpham`");

//Lấy danh sách kho
$listWarehouse = getRaw("SELECT * FROM `tbl_khohang`");

//Lấy danh sách giỏ hảng
$listCartProduct = get_list_buy_cart();

//Xử lý thêm
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $body = $_POST;
  $body['code'] = $timeCurrent;
  //Validation form

  // Kiểm tra các trường có được xuất dữ liệu hay không
  if (empty($body['code'])) {
    $errors['code'] = "Vui lòng điền mã đơn xuất!";
  }

  if ($body['warehouse'] == 0) {
    $errors['warehouse'] = "Vui lòng chọn kho hàng!";
  }

  if ($body['customer'] == 0) {
    $errors['customer'] = "Vui lòng chọn khách hàng!";
  }

  if (empty($listCartProduct)) {
    $errors['cart'] = "Vui lòng chọn sản phẩm xuất hàng!";
  }

  if (empty($errors)) {
    //Nếu dữ liệu đầu vào chuẩn
    $data = array(
      'code' => $body['code'],
      'id_warehouse' => $body['warehouse'],
      'id_customer' => $body['customer'],
      'status' => $body['status'],
      'total' => get_total_cart(),
      'id_employee' => $user_id
    );

    //Nếu note được nhập
    if (!empty($body['note'])) {
      $data['note'] = $body['note'];
    }

    $insertStatus = insert('tbl_xuathang', $data);

    //Nếu insert thành thông
    if ($insertStatus) {
      $id_export = insertID();
      foreach ($listCartProduct as $item) {
        //Insert vào bảng chi tiết xuất hàng
        $data = array(
          'id_export' => $id_export,
          'id_product' => $item['id'],
          'quantity' => $item['qty'],
          'sub_total' => $item['sub_total']
        );

        $insert = insert('tbl_chitiet_xuathang', $data);

        //Lấy số lượng sản phẩm của kho hàng
        $checkQty = firstRaw("SELECT `quantity` FROM `tbl_kho_sanpham` WHERE `id_warehouse` = $body[warehouse] AND `id_product` = $item[id]");

        //Cập nhật lại số lượng
        $qty_new = $checkQty['quantity'] - $item['qty'];

        if (!empty($checkQty)) {
          //Thì cập nhật lại số lượng
          update('tbl_kho_sanpham', ['quantity' => $qty_new], "`id_warehouse` = $body[warehouse] AND `id_product` = $item[id]");
        }
      }
      $_SESSION['msg'] = "Đã tạo đơn hàng xuất thành công!";
      $_SESSION['msg_style'] = "success";
      delete_cart();
      redirect(_WEB_HOST_ROOT . '?module=export');
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình tạo đơn hàng! Thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}

//Set thông báp
$old_data = $_SESSION['old_data'] ?? ['code' => "CODE$timeCurrent"];
?>
<div id="wp-content">
  <div id="content" class="container-fluid">
    <div class="card">
      <div class="card-header font-weight-bold">
        Thêm đơn xuất hàng
      </div>
      <div class="card-body">
        <p class="w-100 text-right">Ghi chú (*) - Bắt buộc phải xuất</p>
        <form method="POST">
          <div class="row">
            <div class="col-6 form-group">
              <label for="code">Mã đơn xuất (*)</label>
              <input class="form-control" disabled type="text" value="<?php echo old('code', $old_data) ?>" name="code" id="code">
              <?php echo form_error('code', $errors) ?>
            </div>
            <div class="col-6 form-group">
              <label for="phone">Khách hàng (*)</label>
              <select name="customer" id="" class="form-control">
                <option value="0">----Chọn khách hàng----</option>
                <?php foreach ($listCustomer as $item): ?>
                  <option <?php echo (!empty(old('customer', $old_data)) && old('customer', $old_data) == $item['id']) ? 'selected' : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <?php echo form_error('customer', $errors) ?>
            </div>
            <div class="col-6">
              <label for="status">Trạng thái (*)</label>
              <div class="row d-flex align-items-center">
                <div class="col-6 d-flex">
                  <input type="radio" name="status" checked id="unpaid" value="0" class="form-check">
                  <label for="unpaid" class="m-1">Chưa thanh toán</label>
                </div>
                <div class="col-6 d-flex">
                  <input type="radio" name="status" id="paid" value="1" class="form-check">
                  <label for="paid" class="m-1">Đã thanh toán</label>
                </div>
              </div>
            </div>
            <div class="col-6 form-group">
              <label for="phone">Kho hàng (*)</label>
              <select name="warehouse" id="" class="form-control warehouse-select">
                <option value="0">----Chọn Kho hàng ----</option>
                <?php foreach ($listWarehouse as $item): ?>
                  <option <?php echo (!empty(old('warehouse', $old_data)) && old('warehouse', $old_data) == $item['id']) ? 'selected' : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <?php echo form_error('warehouse', $errors) ?>
            </div>
            <div class="col-12 form-group">
              <label for="address">Ghi chú đơn hàng</label>
              <textarea name="note" class="form-control" id="note" cols="30" rows="1"><?php echo old('note', $old_data) ?></textarea>
            </div>
            <div class="col-12 form-group">
              <label for="phone">Danh sách sản phẩm (*)</label>
              <button type="button" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-outline-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"></path>
                  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"></path>
                </svg>
                Chọn sản phẩm</button>
              <?php echo form_error('cart', $errors) ?>
              <table class="table table-hover table-bordered mt-2">
                <thead>
                  <tr>
                    <th scope="col" width="5%">#</th>
                    <th scope="col" width="12%">Hình ảnh</th>
                    <th scope="col" width="30%">Tên sản phẩm</th>
                    <th scope="col" width="20%">Số lượng</th>
                    <th scope="col" width="15%%">Tổng tiền</th>
                  </tr>
                </thead>
                <tbody class="list-product-cart">
                  <?php
                  $temp = 0;
                  if (!empty($listCartProduct)) :
                    foreach ($listCartProduct as $item) :
                  ?>
                      <tr>
                        <td><strong><?php echo ++$temp; ?></strong></td>
                        <td class="text-center"><img src="<?php echo !empty($item['thumbnail']) ? $item['thumbnail'] : "http://via.placeholder.com/80X80" ?>" width="80px" height="80px" alt=""></td>
                        <td><a href="#"><?php echo $item['name'] ?></a></td>
                        <td><input type="number" class="form-control quantity" min="1" id="qty-<?php echo $item['id'] ?>" value="<?php echo $item['qty'] ?>" data-id="<?php echo $item['id'] ?>"></td>
                        <td><?php echo number_format($item['sub_total'], 0, '', ',') . 'đ' ?></td>
                      </tr>
                    <?php
                    endforeach;
                  else : ?>
                    <tr>
                      <th colspan="5" class="text-center font-weight-light alert-black">Chưa có sản phẩm nào trong giỏ hàng!</th>
                    </tr>
                  <?php
                  endif
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" class="text-right">
                      <a href="?module=import&action=deleteCart" onclick="return confirm('Bạn chắc chắc xoá sản phảm')" class="btn btn-sm btn-outline-danger mb-3" fdprocessedid="6osz9j">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                          <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                        </svg>
                        Xoá giỏ hàng
                      </a><br>Tổng đơn xuất: <strong id="total"><?php echo number_format(get_total_cart(), 0, '', ',') . 'đ' ?></strong>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <button type="submit" name="btn_submit" class="btn btn-primary">Thêm mới</button>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" module="<?php echo $module ?>" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 750px; max-height: 500px;" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Danh sách sản phẩm</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="input-group">
            <input type="text" class="form-control" id="keyword_text" placeholder="Nhập từ khoá tìm kiếm...." aria-label="Nhập từ khoá tìm kiếm...." aria-describedby="basic-addon1" fdprocessedid="g2y3i8">
            <span class="input-group-text btn-search" id="basic-addon1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel-fill" viewBox="0 0 16 16">
                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z" />
              </svg>
            </span>
          </div>
          <hr>
          <table class="table table-hover table-bordered mt-2">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col" width="80px">Hình ảnh</th>
                <th scope="col">Tên sản phẩm</th>
                <th scope="col">Giá xuất</th>
                <th scope="col" width="120px">Số lượng</th>
                <th scope="col">Tác vụ</th>
              </tr>
            </thead>
            <tbody class="list-tr">

            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>