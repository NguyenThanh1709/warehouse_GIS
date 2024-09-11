<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
  redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
  'pageTitle' => 'Danh sách người dùng',
  'activeMoudule' => 'active'
);

layout('header', $data); //Header
layout('sidebar', $data); //Sidebar

$view = 'add.php';

if (!empty($_GET['view'])) {
  $view = $_GET['view'];
}

if (!empty($_GET['id'])) {
  $id = $_GET['id'];
}

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

    $filter .= " $oparator `name` LIKE '%$keyword%'";
  }
}

// //Xử lý phân trang
$allUserNum = getRows("SELECT id FROM `tbl_nhacungcap` $filter");

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
  $queryString = str_replace('module=suppliers', '', $queryString);
  $queryString = str_replace("&page=$page", '', $queryString);
  $queryString = trim($queryString, '&');
  $queryString = "&$queryString";
}

$listSupiliers = getRaw("SELECT * FROM `tbl_nhacungcap` $filter LIMIT $offset, $perPage");

//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div id="content" class="container-fluid">
    <div class="row">
      <div class="col-4">
        <div class="card">
          <div class="card-header font-weight-bold">
            Thêm nhà cung cấp
          </div>
          <div class="card-body">
            <?php
            if (!empty($view) && !empty($id)) {
              require $view . '.php';
            } else {
              require $view;
            }
            ?>
          </div>
        </div>
      </div>
      <div class="col-8">
        <?php echo alert($msg, $msg_style) ?>
        <div class="card">
          <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            Danh sách nhà cung cấp
            <div class="form-search form-inline">
              <form action="#" class="d-flex">
                <input type="hidden" name="module" value="suppliers">
                <input type="search" name="key" value="<?php echo $keyword ?? '' ?>" class="btn-sm form-control form-search" placeholder="Tìm kiếm thông tin...">
                <button type="submit" class="btn btn-sm btn-primary "><i class="fa fa-search"></i> Tìm kiếm</button>
              </form>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Tên nhà cung cấp</th>
                  <th scope="col">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (count($listSupiliers) > 0) :
                  $temp = $page;
                  foreach ($listSupiliers as $item) :
                ?>
                    <tr>
                      <td scope="row"><strong><?php echo $temp++; ?></strong></td>
                      <td><?php echo $item['name'] ?></td>
                      <td>
                        <a href="?module=suppliers&view=edit&id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                        <a onclick="return confirm('Bạn có chắc chắn xoá dữ liệu!')" href="?module=suppliers&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                  <?php
                  endforeach;
                else : ?>
                  <tr>
                    <th colspan="8" class="text-center font-weight-light alert-danger">Không có dữ liệu !</th>
                  </tr>
                <?php
                endif;
                ?>
              </tbody>
            </table>
          </div>
          <div class="card-footer">
            <?php echo getPaging($page, 'suppliers', $queryString, $maxPage) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>