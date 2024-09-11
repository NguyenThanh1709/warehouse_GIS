<?php
$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $body = $_POST;

  //Validation
  //Bắt buộc phải nhâp - required
  if (empty(trim($body['username']))) {
    $errors['username'] = "Vui lòng nhập thông tin vào trường tài khoản!";
  }

  if (empty(trim($body['password']))) {
    $errors['password'] = "Vui lòng nhập thông tin vào trường mật khẩu!";
  }

  //Nếu có dữ liệu đầu vào
  if (empty($errors)) {
    //Truy vấn đến cơ sở dữ liệu
    $userQuery = firstRaw("SELECT `id` , `password` FROM `tbl_users` WHERE `username`='$body[username]'");
    //Nêys có dữ liệu là tài khoản vừa điền
    if (!empty($userQuery)) {
      //Tiến hành kiểm tra passHash
      $passwordHash = $userQuery['password'];
      $password = $body['password'];
      if (password_verify($password, $passwordHash)) {
        $_SESSION['login'] = true;
        $_SESSION['user_login'] = $userQuery['id'];
        redirect(_WEB_HOST_ROOT);
      } else {
        $errors['checkLogin'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE ?>/assets/css/login.css">
  <title>Đăng nhập hệ thống</title>
</head>

<body>
  <div id="wp-content">
    <div class="form-login container py-4 bg-white" style="width: 665px; margin-top: 10%;">
      <h3 class="title text-center">Đăng nhập hệ thống</h3>
      <form action="" method="post">
        <div class="form-group">
          <label for="username" class="form-label">Tên đăng nhập</label>
          <input type="text" name="username" id="username" placeholder="Tên đăng nhập..." class="form-control">
          <?php echo form_error('username', $errors) ?>
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Mật khẩu</label>
          <input type="password" name="password" id="password" placeholder="Mật khẩu..." class="form-control">
          <?php
          echo form_error('password', $errors);
          echo form_error('checkLogin', $errors);
          ?>
        </div>
        <div class="form-group mt-2">
          <hr>
          <input type="submit" name="btn_login" value="Đăng nhập" class="w-100 btn btn-success">
        </div>
      </form>
    </div>
  </div>
</body>

</html>