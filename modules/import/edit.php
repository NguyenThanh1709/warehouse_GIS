<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Thông tin chi tiết nhập hàng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

$id = "";

$id = $_GET['id'];
//Láy thông tin đơn hàng nhập bảng nhaphang
$importDetail = firstRaw("SELECT `tbl_nhaphang`.*, `tbl_nhacungcap`.name as name_suplier, `tbl_khohang`.name as name_warehouse, `tbl_users`.fullname as name_employee
                            FROM `tbl_nhaphang` 
                            INNER JOIN `tbl_nhacungcap` ON `tbl_nhaphang`.id_suppliers = `tbl_nhacungcap`.id 
                            INNER JOIN `tbl_khohang` ON `tbl_nhaphang`.id_warehouse = `tbl_khohang`.id
                            INNER JOIN `tbl_users` ON `tbl_nhaphang`.id_employee = `tbl_users`.id WHERE `tbl_nhaphang`.id = '$id'");
//Lấy thông tin sản phẩm đơn hàng nhập bảng chitiet_nhaphang
$listCartProduct = getRaw("SELECT `tbl_chitiet_nhaphang`.*, `tbl_sanpham`.name as name_product, `tbl_sanpham`.thumbnail as thumbnail, `tbl_sanpham`.import_price as import_price
                             FROM `tbl_chitiet_nhaphang` INNER JOIN `tbl_sanpham` 
                             ON `tbl_sanpham`.id = `tbl_chitiet_nhaphang`.id_product 
                             WHERE `tbl_chitiet_nhaphang`.id_import = '$id'");

//Xử lý thêm
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $body = $_POST;

  //Validation form
  if (empty($errors)) {
    //Nếu dữ liệu đầu vào chuẩn
    $data = array(
      'status' => $body['status'],
    );

    if (!empty($body['note'])) {
      $data['note'] = $body['note'];
    }

    $updateStatus = update('tbl_nhaphang', $data, "`id`='$id'");
    if ($updateStatus) {
      $_SESSION['msg'] = "Đã cập nhật đơn hàng thành công!";
      $_SESSION['msg_style'] = "success";
      redirect(_WEB_HOST_ROOT . '?module=import&action=edit&id=' . $id);
    } else {
      $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình cập nhật đơn hàng! Thử lại sau!";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['old_data'] = $body;
  }
}
//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>
<div id="wp-content">
  <div id="content" class="container-fluid">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold">
        Chi tiết đơn hàng
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-upc text-success" viewBox="0 0 16 16">
                <path d="M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
              </svg>
              <span class="mx-2"><strong>Mã đơn nhập: </strong><?php echo $importDetail['code'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-bank text-success" viewBox="0 0 16 16">
                <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z" />
              </svg>
              <span class="mx-2"><strong>Kho hàng: </strong><?php echo $importDetail['name_warehouse'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-map text-success" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.5.5 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103M10 1.91l-4-.8v12.98l4 .8zm1 12.98 4-.8V1.11l-4 .8zm-6-.8V1.11l-4 .8v12.98z" />
              </svg>
              <span class="mx-2"><strong>Nhà cung cấp: </strong><?php echo $importDetail['name_suplier'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-person-check text-success" viewBox="0 0 16 16">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
              </svg>
              <span class="mx-2"><strong>Nhân viên: </strong><?php echo $importDetail['name_employee'] ?></span>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-currency-dollar text-success" viewBox="0 0 16 16">
                <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z" />
              </svg>
              <span class="mx-2"><strong>Trạng thái: </strong></span>
              <form method="post" class="mx-2 d-flex align-items-center">
                <span>
                  <select name="status" id="" class="form-control btn-sm w-100">
                    <option <?php echo $importDetail['status'] == 1 ? "selected" : null ?> value="1">Đã thanh toán</option>
                    <option <?php echo $importDetail['status'] == 0 ? "selected" : null ?> value="0">Chưa thanh toán</option>
                  </select>
                </span>
                <button type="submit" class="btn btn-sm btn-primary" width="100px"><i class="fa fa-edit"></i> Cập nhật</button>
              </form>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-alarm text-success" viewBox="0 0 16 16">
                <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z" />
                <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1" />
              </svg>
              <span class="mx-2"><strong>Ngày nhập: </strong> <?php echo $importDetail['create_at'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-card-checklist text-success" viewBox="0 0 16 16">
                <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0M7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0" />
              </svg>
              <span class="mx-2"><strong>Ghi chú: </strong> <?php echo $importDetail['note'] ?? 'Không có' ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header font-weight-bold">
        DANH SÁCH SẢN PHẨM
      </div>
      <div class="card-body">
        <table class="table table-hover table-bordered mt-2">
          <thead>
            <tr>
              <th scope="col" width="5%">#</th>
              <th scope="col" width="12%">Hình ảnh</th>
              <th scope="col" width="30%">Tên sản phẩm</th>
              <th scope="col" width="30%">Giá nhập</th>
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
                  <td><a href="#"><?php echo $item['name_product'] ?></a></td>
                  <td><span><?php echo number_format($item['import_price'], 0, '', ',') . 'đ' ?></span></td>
                  <td><span><?php echo $item['quantity'] ?></span></td>
                  <td><?php echo number_format($item['sub_total'], 0, '', ',') . 'đ' ?></td>
                </tr>
              <?php
              endforeach;
            else : ?>
              <tr>
                <th colspan="6" class="text-center font-weight-light alert-black">Chưa có sản phẩm nào trong giỏ hàng!</th>
              </tr>
            <?php
            endif
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" class="text-right">
                </a><br>Tổng đơn nhập: <strong id="total"><?php echo number_format($importDetail['total'], 0, '', ',') . 'đ' ?></strong>
              </td>
            </tr>
          </tfoot>
        </table>
        <a href="?module=import" class="btn btn-danger">Quay lại</a>
      </div>
    </div>
  </div>

  <?php
  layout('footer', $data);
  ?>