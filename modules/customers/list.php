<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Danh sách khách hàng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

//id user đăng nhập
$user_id = $_SESSION['user_login'];

// Xử lý lọc, tìm kiếm
$filter = "";

$actions = array(
  'invalid' => 'Vô hiệu hoá',
  'activate' => 'Kích hoạt',
);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $body = $_GET;

  // Xử lý lọc theo từ khoá
  if (!empty($body['key'])) {
    $keyword = $body['key'];
    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `fullname` LIKE '%$keyword%'";
  }
}

// //Xử lý phân trang
$allUserNum = getRows("SELECT id FROM `tbl_khachhang` $filter");

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
  $queryString = str_replace('module=customers', '', $queryString);
  $queryString = str_replace("&page=$page", '', $queryString);
  $queryString = trim($queryString, '&');
  $queryString = "&$queryString";
}

$listCustomer = getRaw("SELECT * FROM `tbl_khachhang` $filter LIMIT $offset, $perPage");

//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div class="container-fluid py-5">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <h5 class="m-0 ">Danh sách khách hàng</h5>
        <div class="form-search form-inline">
          <a href="?module=customers&action=add" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
          <form action="#" class="d-flex">
            <input type="hidden" name="module" value="customers">
            <input type="search" name="key" value="<?php echo $keyword ?? '' ?>" class="form-control form-search btn-sm" placeholder="Tìm kiếm thông tin...">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tìm kiếm</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="analytic">

        </div>
        <form action="" method="post">
          <div class="form-action form-inline py-3">

          </div>
          <div class="row">
            <?php
            if (!empty($listCustomer)) :
              foreach ($listCustomer as $item) :
            ?>
                <div class="col-sm-4 mb-2">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title"><strong></strong> <?php echo $item['name'] ?></h5>
                      <p class="card-text"><strong>Địa chỉ:</strong> <?php echo $item['address'] ?></p>
                      <p class="card-text"><strong>Số điện thoại:</strong> 0<?php echo $item['phone'] ?></p>
                      <p class="card-text"><strong>CMND:</strong> <?php echo $item['id_card'] ?? '<span class="text-danger">Chưa cập nhật</span>' ?></p>
                      <p class="card-text"><strong>Email:</strong> <?php echo $item['email'] ?? '<span class="text-danger">Chưa cập nhật</span>' ?></p>
                      <a href="?module=customers&action=edit&id=<?php echo $item['id'] ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                      <a onclick="return confirm('Chắc chắn xoá khách hàng ra khỏi hệ thống')" href="?module=customers&action=delete&id=<?php echo $item['id'] ?>" class="btn btn-danger btn-sm">Xoá</a>
                    </div>
                  </div>
                </div>
            <?php
              endforeach;
            endif;
            ?>
          </div>
        </form>
        <div class="card-footer">
          <?php echo getPaging($page, 'customers', $queryString, $maxPage) ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>