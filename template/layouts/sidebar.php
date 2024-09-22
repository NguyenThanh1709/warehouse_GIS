<div id="sidebar" class="bg-white">
    <ul id="sidebar-menu">
        <li class="nav-link <?php activeModuleSideBar('dashboard'); ?>">
            <a href="?view=dashboard">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Vị trí kho
            </a>
            <i class="arrow fas fa-angle-right"></i>
        </li>
        <li class="nav-link <?php activeModuleSideBar('import'); ?>">
            <a href="?module=import">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Nhập hàng
            </a>
            <i class="arrow fas fa-angle-right"></i>

            <ul class="sub-menu">
                <li><a href="?module=import&action=add">Thêm mới</a></li>
                <li><a href="?module=import">Danh sách</a></li>
            </ul>
        </li>
        <li class="nav-link <?php activeModuleSideBar('export'); ?>">
            <a href="?module=export">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Xuất hàng
            </a>
            <i class="arrow fas fa-angle-right"></i>
            <ul class="sub-menu">
                <li><a href="?module=export&action=add">Thêm mới</a></li>
                <li><a href="?module=export">Danh sách</a></li>
            </ul>
        </li>
        <li class="nav-link <?php activeModuleSideBar('products'); ?><?php activeModuleSideBar('category_products') ?>">
            <a href="?module=products">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Sản phẩm
            </a>
            <i class="arrow fas fa-angle-down"></i>
            <ul class="sub-menu">
                <li><a href="?module=products&action=add">Thêm mới</a></li>
                <li><a href="?module=products">Danh sách</a></li>
                <li><a href="?module=category_products">Danh mục</a></li>
            </ul>
        </li>
        <li class="nav-link <?php activeModuleSideBar('suppliers') ?>">
            <a href="?module=suppliers">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Nhà cung cấp
            </a>
        </li>
        <li class="nav-link <?php activeModuleSideBar('customers') ?>">
            <a href="?module=customers">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Khách hàng
            </a>
        </li>
        <li class="nav-link <?php activeModuleSideBar('users') ?>">
            <a href="?module=users">
                <div class="nav-link-icon d-inline-flex">
                    <i class="far fa-folder"></i>
                </div>
                Users
            </a>
            <i class="arrow fas fa-angle-right"></i>
            <ul class="sub-menu">
                <li><a href="?module=users&action=add">Thêm mới</a></li>
                <li><a href="?module=users">Danh sách</a></li>
            </ul>
        </li>
    </ul>
</div>