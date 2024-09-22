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

//id user đăng nhập
$user_id = $_SESSION['user_login'];

//Xử lý cập nhật tác vụ xử lý
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (!empty($_POST['checkitem'])) {

    if (!empty($_POST['action'])) {

      $arrID = $_POST['checkitem'];

      $arrStrID = implode(',', $arrID);

      $action = $_POST['action'];

      switch ($action) {

        case "invalid":
          $updateStatus = update('tbl_users', ['active' => 0], "`id` IN($arrStrID)");
          //Kiểm tra nó câu lệnh sql được thực thi thành công
          if ($updateStatus) {
            $_SESSION['msg'] = "Đã cập nhật thành công!";
            $_SESSION['msg_style'] = "success";
          } else {
            $_SESSION['msg'] = "Đã xảy ra lỗi khi cập nhật!";
            $_SESSION['msg_style'] = "danger";
          }
          break;
        case "activate":
          $updateStatus = update('tbl_users', ['active' => 1], "`id` IN($arrStrID)");
          //Kiểm tra nó câu lệnh sql được thực thi thành công
          if ($updateStatus) {
            $_SESSION['msg'] = "Đã cập nhật thành công!";
            $_SESSION['msg_style'] = "success";
          } else {
            $_SESSION['msg'] = "Đã xảy ra lỗi khi cập nhật!";
            $_SESSION['msg_style'] = "danger";
          }
          break;
        case "delete":
          $deleteSatus = delete('tbl_users', "`id` IN($arrStrID)");
          //Kiểm tra nó câu lệnh sql được thực thi thành công
          if ($deleteSatus) {
            $_SESSION['msg'] = "Đã xoá dữ liệu thành công!";
            $_SESSION['msg_style'] = "success";
          } else {
            $_SESSION['msg'] = "Đã xảy ra lỗi khi xoá!";
            $_SESSION['msg_style'] = "danger";
          }
          break;
      }
    } else {
      $_SESSION['msg'] = "Vui lòng chọn tác vụ xử lý";
      $_SESSION['msg_style'] = "danger";
    }
  } else {
    $_SESSION['msg'] = "Vui lòng chọn đối tượng thao tác!";
    $_SESSION['msg_style'] = "danger";
  }
}

// Xử lý lọc, tìm kiếm
$filter = "WHERE `is_admin` != 1";

$actions = array(
  'invalid' => 'Vô hiệu hoá',
  'activate' => 'Kích hoạt',
);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $body = $_GET;
  //Xử lý lọc theo trạng thái
  $active = "badge badge-primary";
  if (!empty($body['status'])) {
    $status = $body['status'];
    if ($status == 'hoat-dong') {
      $statusSql = 1;
      $actions = array(
        'invalid' => 'Vô hiệu hoá',
      );
    } else {
      $statusSql = 0;
      $actions = array(
        'activate' => 'Kích hoạt',
        'delete' => 'Xoá vĩnh viễn'
      );
    }

    //Kiểm tra chuổi đã có WHERE hay chưa
    if (!empty($filter) && strpos($filter, "WHERE") >= 0) {
      $oparator = "AND";
    } else {
      $oparator = "WHERE";
    }

    $filter .= " $oparator `active` = '$statusSql'";
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

    $filter .= " $oparator `fullname` LIKE '%$keyword%'";
  }
}

// //Xử lý phân trang
$allUserNum = getRows("SELECT id FROM `tbl_users` $filter");

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
  $queryString = str_replace('module=users', '', $queryString);
  $queryString = str_replace("&page=$page", '', $queryString);
  $queryString = trim($queryString, '&');
  $queryString = "&$queryString";
}

$listUser = getRaw("SELECT * FROM `tbl_users` $filter AND `is_admin` != 1 AND `id`!='$user_id' LIMIT $offset, $perPage");

//Đếm trạng thái 
// 1.Đêm tất cả user ngoài trừ user admin
$allUser = countStatus('tbl_users', "WHERE `is_admin` != 1");
// 2. Đếm user có trạng thái kích hoạt active = 1
$userActive = countStatus('tbl_users', "WHERE `active` = 1 AND `is_admin` != 1");
// 3. Đếm user có trạng thái kích hoạt active = 0
$userLocked = countStatus('tbl_users', "WHERE `active` = 0");

//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>

<div id="wp-content">
  <div class="container-fluid py-5">
    <?php echo alert($msg, $msg_style) ?>
    <div class="card">
      <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <h5 class="m-0 ">Danh sách thành viên</h5>
        <div class="form-search form-inline">
          <a href="?module=users&action=add" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
          <form action="#" class="d-flex">
            <input type="hidden" name="module" value="users">
            <input type="search" name="key" value="<?php echo $keyword ?? '' ?>" class="form-control form-search btn-sm" placeholder="Tìm kiếm thông tin...">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tìm kiếm</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="analytic">
          <a href="?module=users" class="<?php echo (empty($body['status']) ? $active : null) ?>">Tất cả<span class=""> (<?php echo $allUser; ?>)</span></a>
          <a href="?module=users&status=hoat-dong" class="<?php echo (!empty($body['status']) && $body['status'] == 'hoat-dong' ? $active : null) ?>">Hoạt động<span class=""> (<?php echo $userActive; ?>)</span></a>
          <a href="?module=users&status=vo-hieu" class="<?php echo (!empty($body['status']) && $body['status'] == 'vo-hieu' ? $active : null) ?>">Vô hiệu hoá<span class=""> (<?php echo $userLocked; ?>)</span></a>
        </div>
        <form action="" method="post">
          <div class="form-action form-inline py-3">
            <select name="action" class="form-control mr-1 btn-sm" id="">
              <option value="null">---Chọn tác vụ xử lý---</option>
              <?php foreach ($actions as $item => $value) : ?>
                <option value="<?php echo $item ?>"><?php echo $value ?></option>
              <?php endforeach; ?>
            </select>
            <input type="submit" name="btn_action" value="Áp dụng" class="btn btn-primary btn-sm">
          </div>
          <table class="table table-striped table-checkall">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" name="checkall">
                </th>
                <th scope="col">#</th>
                <th scope="col">Họ tên</th>
                <th scope="col">Username</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Địa chỉ</th>
                <th scope="col">Số điện thoại</th>
                <th scope="col">Tác vụ</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($listUser) > 0) :
                $temp = $page;
                foreach ($listUser as $item) :
              ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="checkitem[]" value="<?php echo $item['id'] ?>">
                    </td>
                    <th scope="row"><?php echo $temp++ ?></th>
                    <td><?php echo $item['fullname'] ?></td>
                    <td><?php echo $item['username'] ?></td>
                    <td><?php echo !empty($item['active']) && $item['active'] == 1 ? "<span class='badge badge-success'>Kích hoạt</span>" : "<span class='badge badge-warning'>Tạm khoá</span>" ?></td>
                    <td><?php echo $item['address'] ?></td>
                    <td><?php echo "0" . $item['phone'] ?></td>
                    <td>
                      <a href="?module=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                      <a onclick="return confirm('Bạn có chắc chắn xoá dữ liệu!')" href="?module=users&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
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
          <?php echo getPaging($page, 'users', $queryString, $maxPage) ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  layout('footer', $data);
  ?>