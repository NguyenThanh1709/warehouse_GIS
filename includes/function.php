<?php
// Hàm get file trong thư mục layouts
function layout($layout, $data = [])
{
  if (!empty($dir)) {
    $dir = '/' . $dir;
  }
  if (file_exists(_WEB_PATH_TEMPLATE  . "/layouts/$layout.php")) {
    require_once _WEB_PATH_TEMPLATE . "/layouts/$layout.php";
  }
}

//Hàm active module đang truy cập ở sidebar
function activeModuleSideBar($module)
{
  echo !empty($_GET['module']) && $_GET['module'] == $module ? 'active' : null;
}


// Check login
function isLogin()
{
  if (!isset($_SESSION['login'])) {
    return false;
  }
  return true;
}

//Hàm chuyển hướng
function redirect($path = 'index.php')
{
  header("Location: $path");
  exit;
}

// Hàm xuất lỗi
function form_error($fieldName, $errors)
{
  return (!empty($errors[$fieldName])) ? "<span class='text text-danger mt-1'>$errors[$fieldName]</span>" : null;
}

// Hàm set value
function old($fieldName, $odlData)
{
  return (!empty($odlData[$fieldName])) ? $odlData[$fieldName] : null;
}

//show Arr
function showDataArr($data)
{
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}

//Lấy thông tin user
function userDetail($user_id)
{
  $user = firstRaw("SELECT * FROM `tbl_users` WHERE `id` = '$user_id'");
  if (!empty($user)) {
    return $user;
  }
}

//Phân trang
function getPaging($page, $module, $queryString, $maxPage, $path = _WEB_HOST_ROOT)
{
  $web_host_rooot_admin = $path; //Lấy đường dẫn web root
  // Chuổi html
  $str = "<nav aria-label='Page navigation example'> 
          <ul class='pagination pagination-sm m-0 p-0'>";
  // Kiểm tra lớn hơn 1 mới hiển thị
  if ($page > 1) {
    $prevPage = $page - 1;
    $str .= "<li class='page-item'><a class='page-link' href='$web_host_rooot_admin?module=$module" . "$queryString" . "&page=$prevPage''>Trước</a></li>";
  }
  $begin = $page - 4;
  if ($begin < 1) {
    $begin = 1;
  }
  $end = $page + 4;
  if ($end > $maxPage) {
    $end = $maxPage;
  }
  //Lập lấy thanh phân trang 
  for ($index = $begin; $index <= $end; $index++) {
    $active = $index == $page ? 'active' : '';
    $str .= "<li class='page-item $active'><a class='page-link' href='$web_host_rooot_admin?module=$module" . "$queryString" . "&page=$index''>$index</a></li>";
  }
  // Nếu page bằng tổng số trang 
  if ($page < $maxPage) {
    $nextPage = $page + 1;
    if ($page > $maxPage) {
      $page = 1;
    }

    $str .= "<li class='page-item'><a class='page-link' href='$web_host_rooot_admin?module=$module" . "$queryString" . "&page=$nextPage''>Sau</a></li>";
  }
  $str .= "</ul></nav>";

  return $str;
}

//Đếm trạng thái
function countStatus($table, $condition = '')
{
  $result = getRows("SELECT * FROM `$table` $condition");
  return $result;
}

function alert($msg, $msg_style)
{
  if (!empty($msg)) {
    $str = "<div class='alert alert-$msg_style alert-dismissible fade show' role='alert'>
            <strong>Thông báo!</strong> $msg
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
    unset($_SESSION['msg']);
    unset($_SESSION['msg_style']);
    unset($_SESSION['old_data']);
    return $str;
  }
}

// File xử lý giỏ hàng
function add_cart($table, $condition, $id = '', $option = '', $quantity = '')
{
  $get_product_by_id = firstRaw("SELECT * FROM $table WHERE $condition");
  $price = $get_product_by_id[$option];
  // Thêm thông tin vào giỏ hàng
  if (isset($_SESSION['cart']) && array_key_exists($id, $_SESSION['cart']['buy'])) {
    $quantity = $_SESSION['cart']['buy'][$id]['qty'] + $quantity;
  }
  $_SESSION['cart']['buy'][$id] = array(
    'id' => $get_product_by_id['id'],
    'name' => $get_product_by_id['name'],
    'thumbnail' => $get_product_by_id['thumbnail'],
    'qty' => $quantity,
    'sub_total' => $price * $quantity,
  );
  update_info_cart();
}

// add_cart();
// get_num_order_cart();
// $listProductCart = get_list_buy_cart();

function update_info_cart()
{
  if (isset($_SESSION['cart'])) {
    $num_order = 0;
    $total = 0;
    foreach ($_SESSION['cart']['buy'] as $item) {
      $num_order += $item['qty'];
      $total += $item['sub_total'];
    }

    $_SESSION['cart']['info'] = array(
      'num_order' => $num_order,
      'total' => $total
    );
  }
}

function get_list_buy_cart()
{
  if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart']['buy'] as &$item) {
      $item['url_delete_cart'] = "?mod=user&act=delete_cart&id={$item['id']}";
    }
    return $_SESSION['cart']['buy'];
  }
  return false;
}

function get_num_order_cart()
{
  if (isset($_SESSION['cart'])) {
    return $_SESSION['cart']['info']['num_order'];
  }
  return false;
}

function get_total_cart()
{
  if (isset($_SESSION['cart'])) {
    return $_SESSION['cart']['info']['total'];
  }
  return false;
}

function delete_cart($id = '')
{
  if (isset($_SESSION)) {
    if (!empty($id)) {
      unset($_SESSION['cart']['buy'][$id]);
      update_info_cart();
    } else {
      unset($_SESSION['cart']);
    }
  }
}

function update_cart_number($qty)
{
  foreach ($qty as $id => $new_qty) {
    $_SESSION['cart']['buy'][$id]['qty'] = $new_qty;
    $_SESSION['cart']['buy'][$id]['sub_total'] = $new_qty *  $_SESSION['cart']['buy'][$id]['price'];
  }
  update_info_cart();
}
