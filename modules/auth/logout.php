<?php
if (isLogin()) {
  session_destroy();
  redirect('?module=auth&action=login');
}
