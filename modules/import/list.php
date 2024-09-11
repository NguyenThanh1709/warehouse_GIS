<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Danh sách đơn hàng nhập',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//Danh sách kho hàng
$listWarehouse = getRaw("SELECT * FROM `tbl_khohang`");

//Danh sách nhà cung cấp
$listSupplier = getRaw("SELECT * FROM `tbl_nhacungcap`");

//Xử lý cập nhật tác vụ xử lý
$errors = array();

// Xử lý lọc, tìm kiếm
$filter = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $body = $_GET;

  //Xử lý lọc theo trạng thái
  $active = "badge badge-primary";
  if (!empty($body['status'])) {
    if ($body['status'] == 'thanh-toan') {
      $status = 1;
    } else {
      $status = 0;
    }

    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `tbl_nhaphang`.status = '$status'";
  }

  // Xử lý lọc theo kho hàng
  $warehouse = '';
  if (!empty($body['warehouse'])) {
    $warehouse = $body['warehouse'];
    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `tbl_nhaphang`.id_warehouse = '$warehouse'";
  }

  // Xử lý lọc nhà cung cấp
  $supliers = '';
  if (!empty($body['supliers'])) {
    $supliers = $body['supliers'];
    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `tbl_nhaphang`.id_suppliers = '$supliers'";
  }


  // Xử lý lọc theo từ khoá
  if (!empty($body['key'])) {
    $keyword = $body['key'];
    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `tbl_nhaphang`.code LIKE '%$keyword%' OR `tbl_users`.fullname LIKE '%$keyword%'";
  }
}

//Xử lý phân trang
$allUserNum = getRows("SELECT id FROM `tbl_nhaphang` $filter");

// 1. Xác định số lượng bản ghi 1 trang
$perPage = 10;

// 2. Tính số trang
$maxPage = ceil($allUserNum / $perPage);

// 3. Xử lý số trang dựa vào phương thức GET
if (!empty($_GET['page'])) {
  $page = $_GET['page'];
  if ($page < 1 || $page > $maxPage) {
    $page = 1;
  }
} else {
  $page = 1;
}

//4. Tính offset
/*
$page = 1 => offset = 0 => ($page-1) * $perPage 
$page = 2 => offset = 3
$page = 3 => offset = 6
*/
$offset = ($page - 1) * $perPage;

//Xử lý tìm kiếm phân trang
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
  $queryString = $_SERVER['QUERY_STRING'];
  $queryString = str_replace('module=import', '', $queryString);
  $queryString = str_replace("&page=$page", '', $queryString);
  $queryString = trim($queryString, '&');
  $queryString = "&$queryString";
}

$listImport = getRaw("SELECT `tbl_nhaphang`.*, `tbl_nhacungcap`.name as name_suplier, `tbl_khohang`.name as name_warehouse , `tbl_users`.fullname as fullname
FROM `tbl_nhaphang` INNER JOIN `tbl_nhacungcap` 
ON `tbl_nhaphang`.id_suppliers = `tbl_nhacungcap`.id 
INNER JOIN `tbl_khohang` ON `tbl_nhaphang`.id_warehouse = `tbl_khohang`.id 
INNER JOIN `tbl_users` ON `tbl_nhaphang`.id_employee = `tbl_users`.id $filter ORDER BY `tbl_nhaphang`.create_at DESC LIMIT $offset, $perPage");

//Đếm trạng thái
// 1.Đêm tất cả đơn hàng
$allOrderImport = countStatus('tbl_nhaphang');
// 2. Đếm đơn hàng nhập đã thanh toán
$paymentSucess = countStatus('tbl_nhaphang', "WHERE `status` = 1");
// 3. Đếm user có trạng thái kích hoạt active = 0
$paymentWarning = countStatus('tbl_nhaphang', "WHERE `status` = 0");

//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div class="container-fluid py-5">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <h5 class="m-0 ">Danh sách nhập hàng</h5>

        <div class="form-search form-inline">
          <a href="?module=import&action=add" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
          <form action="#" class="d-flex">
            <input type="hidden" name="module" value="import">
            <select name="warehouse" id="" class="form-control btn-sm">
              <option value="">---Chọn kho hàng---</option>
              <option value="">---Tất cả---</option>
              <?php foreach ($listWarehouse as $item) : ?>
                <option <?php echo (!empty($warehouse) && $warehouse == $item['id']) ? 'selected' : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
              <?php endforeach ?>
            </select>
            <select name="supliers" id="" class="form-control btn-sm">
              <option value="">---Chọn nhà cung cấp---</option>
              <option value="">---Tất cả---</option>
              <?php foreach ($listSupplier as $item) : ?>
                <option <?php echo (!empty($supliers) && $supliers == $item['id']) ? 'selected' : null ?> value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
              <?php endforeach ?>
            </select>
            <input type="search" name="key" value="<?php echo $keyword ?? '' ?>" class="form-control form-search btn-sm" placeholder="Tìm kiếm thông tin...">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tìm kiếm</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="analytic">
          <a href="?module=import" class="<?php echo (empty($body['status']) ? $active : null) ?>">Tất cả <span class=""> (<?php echo $allOrderImport ?>)</span></a>
          <a href="?module=import&status=thanh-toan" class="<?php echo (!empty($body['status']) && $body['status'] == 'thanh-toan' ? $active : null) ?>">Đã thanh toán<span class=""> (<?php echo $paymentSucess ?>)</span></a>
          <a href="?module=import&status=chua-thanh-toan" class="<?php echo (!empty($body['status']) && $body['status'] == 'chua-thanh-toan' ? $active : null) ?>">Chưa thanh toán<span class=""> (<?php echo $paymentWarning ?>)</span></a>
        </div>
        <form action="" method="post">
          <div class="form-action form-inline py-3">
          </div>
          <table class="table table-striped table-checkall">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" name="checkall">
                </th>
                <th scope="col">#</th>
                <th scope="col">Mã đơn nhập</th>
                <th scope="col">Nhà cung cấp</th>
                <th scope="col">Kho hàng</th>
                <th scope="col">Giá trị</th>
                <th scope="col">Ngày nhập hàng</th>
                <th scope="col">Tác vụ</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($listImport) > 0) :
                $temp = $page;
                foreach ($listImport as $item) :
              ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="checkitem[]" value="<?php echo $item['id'] ?>">
                    </td>
                    <td scope="row"><strong><?php echo $temp++ ?></strong></td>
                    <td>
                      <?php echo $item['code'] ?><br>
                      <span class="badge badge-info"><?php echo userDetail($item['id_employee'])['fullname'] ?></span>
                    </td>
                    <td><?php echo $item['name_suplier'] ?></td>
                    <td><?php echo $item['name_warehouse'] ?></td>
                    <td>
                      <?php echo number_format($item['total'], 0, '', ',') . 'đ' ?><br>
                      <?php
                      echo $item['status'] == 0 ? "<span class='badge badge-danger'>Chưa thanh toán</span>" : "<span class='badge badge-success'>Đã thanh toán</span>"
                      ?>
                    </td>
                    <td><?php echo $item['create_at'] ?></td>
                    <td>
                      <a href="?module=import&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                      <a onclick="return confirm('Bạn có chắc chắn xoá dữ liệu!')" href="?module=import&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
                <?php
                endforeach;
              else : ?>
                <tr>
                  <th colspan="8" class="text-center font-weight-light alert-danger">Không có dữ liệu !</th>
                </tr>
              <?php
              endif
              ?>
            </tbody>
          </table>
        </form>
        <div class="card-footer">
          <?php echo getPaging($page, 'import', $queryString, $maxPage) ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>