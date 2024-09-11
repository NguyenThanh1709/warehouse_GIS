<?php
//Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
    redirect(_WEB_HOST_ROOT . '?module=auth&action=login');
}

$data = array(
    'pageTitle' => 'Trang chủ'
);
layout('header', $data);
layout('sidebar', $data);

//Lấy danh sách kho hiển thị google map
$listWarehouse = getRaw("SELECT * FROM `tbl_khohang`");

// Chuyển đổi dữ liệu thành JSON
$warehousesJSON = json_encode($listWarehouse);


//Xử lý phân trang
$allWarehouse = count($listWarehouse);

// 1. Xác định số lượng bản ghi 1 trang
$perPage = 25;

// 2. Tính số trang
$maxPage = ceil($allWarehouse / $perPage);

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
    $queryString = str_replace('module=dashboard', '', $queryString);
    $queryString = str_replace("&page=$page", '', $queryString);
    $queryString = trim($queryString, '&');
    $queryString = "&$queryString";
}

$listWarehousePaging = getRaw("SELECT * FROM `tbl_khohang` LIMIT $offset, $perPage");
//Xử lý thêm kho hàng
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $body = $_POST;

    //Validate form
    if (empty($body['name'])) {
        $errors['name'] = "Không được để trống trường tên kho!";
    }
    if (empty($body['address'])) {
        $errors['address'] = "Không được để trống trường địa chỉ!";
    }
    if (empty($body['latitude'])) {
        $errors['latitude'] = "Không được để trống vĩ độ!";
    }
    if (empty($body['longitude'])) {
        $errors['longitude'] = "Không được để trống kinh độ!";
    }

    //Tạo mảng dữ liệu insert
    $data = array(
        'name' => $body['name'],
        'address' => $body['address'],
        'latitude' => $body['latitude'],
        'longitude' => $body['longitude'],
    );

    if (!empty($body['name_contact'])) {
        $data['name_contact'] = $body['name_contact'];
    }

    if (!empty($body['phone_contact'])) {
        $data['phone_contact'] = $body['phone_contact'];
    }

    //Nếu chỉnh sửa
    if (!empty($body['id_warehouse'])) {
        $updateStatus = update('tbl_khohang', $data, "id=$body[id_warehouse]");
        //Kiểm tra thêm thành công hay thất bại
        if ($updateStatus) {
            unset($_SESSION['old_data']);
            $_SESSION['msg'] = "Đã cập nhật dữ liệu thành công!";
            $_SESSION['msg_style'] = "success";
            redirect(_WEB_HOST_ROOT); //Chuyển hướng
        } else {
            $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình thêm người dùng! Thử lại sau!";
            $_SESSION['msg_style'] = "danger";
        }
        exit(); //Dừng code
    }

    $insertStatus = insert('tbl_khohang', $data);

    //Kiểm tra thêm thành công hay thất bại
    if ($insertStatus) {
        unset($_SESSION['old_data']);
        $_SESSION['msg'] = "Đã thêm kho mới thành công!";
        $_SESSION['msg_style'] = "success";
        redirect(_WEB_HOST_ROOT); //Chuyển hướng
    } else {
        $_SESSION['msg'] = "Đã xảy ra lỗi trong quá trình thêm người dùng! Thử lại sau!";
        $_SESSION['msg_style'] = "danger";
    }
}


//THiết lập nội dung, trạng thái thông báo
$msg = $_SESSION['msg'] ?? '';
$msg_style = $_SESSION['msg_style'] ?? '';
?>
<div id="wp-content">
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        GOOGLE MAP
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 500px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- end analytic  -->

        <div class="card">
            <?php echo alert($msg, $msg_style) ?>
            <div class="card-header font-weight-bold">
                DANH SÁCH KHO HÀNG
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Mã kho</th>
                            <th scope="col">Thông tin liên hệ</th>
                            <th scope="col">Tên kho</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Kinh độ - vĩ độ</th>
                            <th scope="col">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $temp = $page;
                        if (count($listWarehousePaging) > 0):
                            foreach ($listWarehousePaging as $item) :
                        ?>
                                <tr>
                                    <td><strong><?php echo $temp++ ?></strong></td>
                                    <td>#CODE_<?php echo $item['id'] ?></td>
                                    <td>
                                        <?php echo $item['name_contact'] ?> <br>
                                        <span class="badge badge-secondary"><?php echo '0' . $item['phone_contact'] ?></span>
                                    </td>
                                    <td><a href="#"><?php echo $item['name'] ?></a></td>
                                    <td><?php echo $item['address'] ?></td>
                                    <td><span class="badge badge-warning"><?php echo $item['longitude'] . '<br>' . ' ----- ' . '<br>' . $item['latitude'] ?></span></td>
                                    <td>
                                        <a class="btn btn-success btn-sm rounded-0 text-white edit-modal" type="button" id="edit-modal" data-toggle="modal" data-id="<?php echo $item['id'] ?>" data-target="#locationModal" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a onclick="return confirm('Bạn có chắc chắn xoá dữ liệu!')" href="?module=dashboard&action=delete&id=<?php echo $item['id'] ?>" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
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

            </div>
            <div class="card-footer">
                <?php echo getPaging($page, 'dashboard', $queryString, $maxPage) ?>
            </div>
        </div>
    </div>
</div>

<!-- Form thêm -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Thêm kho hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="addLocationForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal-lat">Tên kho</label>
                        <input type="text" class="form-control" required id="modal-name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="modal-lat">Địa chỉ</label>
                        <input type="text" class="form-control" required id="modal-address" name="address">
                    </div>
                    <div class="form-group">
                        <label for="modal-lat">Số điện thoại</label>
                        <input type="text" class="form-control" id="modal-phone_contact" name="phone_contact">
                    </div>
                    <div class="form-group">
                        <label for="modal-lat">Người quản lý</label>
                        <input type="text" class="form-control" id="modal-name_contact" name="name_contact">
                    </div>
                    <div class="form-group">
                        <label for="modal-lat">Vĩ độ</label>
                        <input type="text" class="form-control" id="modal-lat" name="latitude" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-lng">Kinh độ</label>
                        <input type="text" class="form-control" id="modal-lng" name="longitude" readonly>
                    </div>
                    <input type="hidden" class="form-control" id="modal-lng" name="id_warehouse" readonly>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary btn-edit-warehouse"><i class="fa fa-push"></i>Thêm mới</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
layout('footer', $data);
?>

<!-- API Key -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWTx7bREpM5B6JKdbzOvMW-RRlhkukmVE&callback=initMap" async defer></script>

<script>
    // Lấy dữ liệu từ PHP
    var warehouses = <?php echo $warehousesJSON; ?>;
</script>