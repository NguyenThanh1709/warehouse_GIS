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

//Lấy thông tin kho
$id = $_GET['id'];
$warehouseDetail = firstRaw("SELECT * FROM `tbl_khohang` WHERE `id` = '$id'");

//Lấy thông tin sản phẩm
$listDetailProduct = getRaw("SELECT `tbl_kho_sanpham`.*, `tbl_sanpham`.name as name_product, `tbl_sanpham`.thumbnail, `tbl_khohang`.name as name_warehouse
FROM `tbl_kho_sanpham` 
INNER JOIN `tbl_sanpham` ON `tbl_sanpham`.id = `tbl_kho_sanpham`.id_product
INNER JOIN `tbl_khohang` ON `tbl_khohang`.id = `tbl_kho_sanpham`.id_warehouse 
WHERE `tbl_kho_sanpham`.id_warehouse = '$id'");

$_SESSION['listData'] = $listDetailProduct;
$_SESSION['name_contact'] = $warehouseDetail['name_contact'];
$_SESSION['name'] = $warehouseDetail['name'];
// showDataArr($_SESSION['listData']);
?>

<!-- Giao diện html -->
<div id="wp-content">
  <div id="content" class="container-fluid">
    <div class="card">
      <div class="card-header font-weight-bold">
        Thông tin kho
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-upc text-success" viewBox="0 0 16 16">
                <path d="M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
              </svg>
              <span class="mx-2"><strong>Mã kho: </strong><?php echo '#CODE_' . $warehouseDetail['id'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-bank text-success" viewBox="0 0 16 16">
                <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z" />
              </svg>
              <span class="mx-2"><strong>Tên kho: </strong><?php echo $warehouseDetail['name'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-map text-success" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.5.5 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103M10 1.91l-4-.8v12.98l4 .8zm1 12.98 4-.8V1.11l-4 .8zm-6-.8V1.11l-4 .8v12.98z" />
              </svg>
              <span class="mx-2"><strong>Địa chỉ: </strong><?php echo $warehouseDetail['address'] ?></span>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-person-check text-success" viewBox="0 0 16 16">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
              </svg>
              <span class="mx-2"><strong>Nhân viên: </strong><?php echo $warehouseDetail['name_contact'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-telephone text-success" viewBox="0 0 16 16">
                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
              </svg>
              <span class="mx-2"><strong>Số điện thoại: </strong><?php echo '0' . $warehouseDetail['phone_contact'] ?></span>
            </div>
            <div class="form-group d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" fill="currentColor" class="bi bi-globe text-success" viewBox="0 0 16 16">
                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z" />
              </svg>
              <span class="mx-2"><strong>Vị trí: </strong><?php echo $warehouseDetail['longitude'] . '--' . $warehouseDetail['latitude'] ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header font-weight-bold d-flex justify-content-between">
        DANH SÁCH TỒN KHO
        <a  href="?module=dashboard&action=exportExcel" type="button" class="btn btn-primary btn-sm" fdprocessedid="vc1xlr">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" style="margin-top: -3px;" height="16" fill="currentColor" class="bi bi-file-excel" viewBox="0 0 16 16">
            <path d="M5.18 4.616a.5.5 0 0 1 .704.064L8 7.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 8l2.233 2.68a.5.5 0 0 1-.768.64L8 8.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 8 5.116 5.32a.5.5 0 0 1 .064-.704"></path>
            <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"></path>
          </svg>
          Xuất Excel
        </a>
      </div>
      <div class="card-body">
        <table class="table table-hover table-bordered mt-2">
          <thead>
            <tr>
              <th scope="col" width="5%">#</th>
              <th scope="col" width="12%">Hình ảnh</th>
              <th scope="col" width="30%">Tên sản phẩm</th>
              <th scope="col" width="20%">Số lượng trong kho</th>
              <!-- <th scope="col" width="20%">Nhập hàng</th> -->
            </tr>
          </thead>
          <tbody class="list-product-cart">
            <?php
            $temp = 0;
            foreach ($listDetailProduct as $item) :
            ?>
              <tr>
                <td><strong><?php echo ++$temp ?></strong></td>
                <td class="text-center"><img src="<?php echo $item['thumbnail'] ?>" width="80px" height="80px" alt=""></td>
                <td><a href="#"><?php echo $item['name_product'] ?></a></td>
                <td><span><?php echo $item['quantity'] ?></span></td>
              </tr>
            <?php endforeach; ?>

          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" class="text-right">
              </td>
            </tr>
          </tfoot>
        </table>
        <a href="?module=dashboard" class="btn btn-danger">Quay lại</a>
      </div>
    </div>
  </div>

  <?php
  layout('footer', $data);
  ?>