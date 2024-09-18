<?php

if (isset($_POST['keyword'])) {
  $keyword = $_POST['keyword'];
  $module = $_POST['module'] . '_price';
  $id_warehouse = $_POST['id_warehouse'];
  if ($module == 'import_price') {
    $listProduct = getRaw("SELECT * FROM `tbl_sanpham` WHERE `name` LIKE '%$keyword%'");
  } else {
    $listProduct = getRaw("SELECT `tbl_kho_sanpham`.id_product as id, `tbl_kho_sanpham`.id_warehouse, `tbL_kho_sanpham`.quantity, `tbl_sanpham`.`thumbnail`, `tbl_sanpham`.name, `tbl_sanpham`.unit, `tbl_sanpham`.export_price 
    FROM `tbl_kho_sanpham` 
    INNER JOIN `tbl_sanpham` ON `tbl_sanpham`.id = `tbl_kho_sanpham`.`id_product` 
    WHERE `tbl_kho_sanpham`.id_warehouse = '$id_warehouse' AND `tbl_sanpham`.name LIKE '%$keyword%'");
  }

  if (!empty($listProduct)) {
    $strHTML = '';
    $temp = 0;
    foreach ($listProduct as $item) {
      $strHTML .= '<tr>';
      $strHTML .= '<td><strong>' . ++$temp . '</strong></td>';
      $strHTML .= '<td><img src="' . (!empty($item['thumbnail']) ? $item['thumbnail'] : "http://via.placeholder.com/80X80") . '" width="80px" height="80px" alt=""></td>';
      $strHTML .= '<td><a href="#">' . $item['name'] . '</a></td>';
      $strHTML .= '<td>' . number_format($item[$module], 0, '', ',') . 'đ / ' . $item['unit'] . '</td>';
      $strHTML .= '<td><span class="text-info text-center">' . (!empty($item['quantity']) ? "Max:" . $item['quantity'] : null) . '</span><input type="number" class="form-control" min="1" max="' . (!empty($item['quantity']) ? $item['quantity'] : null) . '" value="1" id="qty-' . $item['id'] . '"></td>';
      $strHTML .= '<td class="text-center">';
      $strHTML .= '<button type="button" class="btn btn-sm text-center text-success btn-add-cart" data-id="' . $item['id'] . '">';
      $strHTML .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">';
      $strHTML .= '<path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z"></path>';
      $strHTML .= '<path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path>';
      $strHTML .= '</svg>';
      $strHTML .= '</button>';
      $strHTML .= '</td>';
      $strHTML .= '</tr>';
    }
    echo json_encode(['str' => $strHTML]);
  } else {
    echo json_decode("Không có dữ liệu!");
  }
}

//Thêm giỏ hàng
if (isset($_POST['id_product'])) {
  $id = $_POST['id_product'];
  $qty = $_POST['qty'];
  //Lấy tên module
  $module = $_POST['module'] . '_price';

  add_cart('tbl_sanpham', "`id`='$id'", $id, $module, $qty);

  $listProductCart = get_list_buy_cart();
  $str = '';
  $temp = 0;
  foreach ($listProductCart as $item) {
    $str .= '<tr>';
    $str .= '<td><strong>' . ++$temp . '</strong></td>';
    $str .= '<td><img src="' . (!empty($item['thumbnail']) ? $item['thumbnail'] : "https://via.placeholder.com/80X80") . '" width="80px" height="80px" alt=""></td>';
    $str .= '<td><a href="#">' . $item['name'] . '</a></td>';
    $str .= '<td><input type="number" class="form-control quantity" min="1" value="' . $item['qty'] . '" id="qty-' . $item['id'] . '" data-id="' . $item['id'] . '"></td>';
    $str .= '<td>' . number_format($item['sub_total'], 0, '', ',') . 'đ</td>';
    $str .= '</tr>';
  }
  $total = number_format(get_total_cart(), 0, '', ',') . 'đ';
  // echo json_encode($module);
  // die();
  echo json_encode(['str' => $str, 'total' => $total]);
}
