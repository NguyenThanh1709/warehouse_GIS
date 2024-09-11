<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Danh sách sản phẩm',
  'activeMoudule' => 'active'
);

//Huỷ lưu dữ liệu ở form
unset($_SESSION['old_data']);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

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

    $filter .= " $oparator `tbl_sanpham`.name LIKE '%$keyword%'";
  }
}

// //Xử lý phân trang
$allProductsNum = getRows("SELECT `tbl_sanpham`.*, `tbl_danhmuc_sanpham`.name as name_category
                          FROM `tbl_sanpham` 
                          INNER JOIN `tbl_danhmuc_sanpham` 
                          ON `tbl_sanpham`.id_category = `tbl_danhmuc_sanpham`.id $filter");

// 1. Xác định số lượng bản ghi 1 trang
$perPage = 10;

// 2. Tính số trang
$maxPage = ceil($allProductsNum / $perPage);

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
  $queryString = str_replace('module=products', '', $queryString);
  $queryString = str_replace("&page=$page", '', $queryString);
  $queryString = trim($queryString, '&');
  $queryString = "&$queryString";
}

$listProducts = getRaw("SELECT `tbl_sanpham`.*, `tbl_danhmuc_sanpham`.name as name_category
                        FROM `tbl_sanpham` 
                        INNER JOIN `tbl_danhmuc_sanpham` 
                        ON `tbl_sanpham`.id_category = `tbl_danhmuc_sanpham`.id $filter LIMIT $offset, $perPage");

//1.Đêm tất cả bản ghi
$allProducts = countStatus('tbl_sanpham');


//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div class="container-fluid py-5">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <h5 class="m-0 ">Danh sách sản phẩm</h5>
        <div class="form-search form-inline">
          <a href="?module=products&action=add" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
          <form action="#" class="d-flex">
            <input type="hidden" name="module" value="products">
            <input type="search" name="key" value="<?php echo $keyword ?? '' ?>" class="form-control form-search btn-sm" placeholder="Tìm kiếm thông tin...">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tìm kiếm</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="analytic">
          <a href="?module=products" class="text-primary">Số lượng sản phẩm<span class="text-muted">(<?php echo $allProducts; ?>)</span></a>
        </div>
        <form action="" method="post">
          <div class="form-action form-inline py-3">
          </div>
          <table class="table table-striped table-checkall">
            <thead>
              <tr>
                <th scope="col">
                  <input name="checkall" type="checkbox">
                </th>
                <th scope="col">#</th>
                <th scope="col">Ảnh</th>
                <th scope="col">Tên sản phẩm</th>
                <th scope="col">Giá nhập</th>
                <th scope="col">Giá xuất</th>
                <th scope="col">Danh mục</th>
                <th scope="col">Tác vụ</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($listProducts) > 0) :
                $temp = $page;
                foreach ($listProducts as $item) :
              ?>
                  <tr class="">
                    <td>
                      <input type="checkbox">
                    </td>
                    <td><strong><?php echo $temp++; ?></strong></td>
                    <td><img src="<?php echo !empty($item['thumbnail']) ? $item['thumbnail'] : "http://via.placeholder.com/80X80" ?>" width="80px" height="80px" alt=""></td>
                    <td><a href="#"><?php echo $item['name'] ?></a></td>
                    <td><?php echo number_format($item['import_price'], 0, '', ',') . 'đ / ' . $item['unit']  ?></td>
                    <td><?php echo number_format($item['export_price'], 0, '', ',') . 'đ / ' . $item['unit'] ?></td>
                    <td><span class="badge badge-info"><?php echo $item['name_category'] ?></span></td>
                    <td>
                      <a href="?module=products&action=edit&id=<?php echo $item['id'] ?>" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                      <a onclick="return confirm('Bạn có chắc chắn xoá dữ liệu!')" href="?module=products&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
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
          <?php echo getPaging($page, 'products', $queryString, $maxPage) ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>