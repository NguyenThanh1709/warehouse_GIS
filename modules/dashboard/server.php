<?php
if (isset($_POST['id_warehouse'])) {
  $id = $_POST['id_warehouse'];
  $warehouseDetail = firstRaw("SELECT * FROM `tbl_khohang` WHERE `id` = '$id'");
  echo json_encode($warehouseDetail);
}
