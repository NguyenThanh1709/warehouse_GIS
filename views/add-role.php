<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0 ">Thêm mới vai trò</h5>
            <div class="form-search form-inline">
                <form action="#">
                    <input type="" class="form-control form-search" placeholder="Tìm kiếm">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary">
                </form>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">

                <div class="form-group">
                    <label class="text-strong" for="name">Tên vai trò</label>
                    <input class="form-control" type="text" name="name" id="name">
                </div>
                <div class="form-group">
                    <label class="text-strong" for="description">Mô tả</label>
                    <textarea class="form-control" type="text" name="description" id="description"></textarea>
                </div>
                <strong>Vai trò này có quyền gì?</strong>
                <small class="form-text text-muted pb-2">Check vào module hoặc các hành động bên dưới để chọn quyền.</small>
                <!-- List Permission  -->

                <div class="card my-4 border">
                    <div class="card-header">
                        <input type="checkbox" class="check-all" name="" id="post">
                        <label for="post" class="m-0">Module Post</label>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="2" name="permission_id[]" id="post.add">
                                <label for="post.add">Add Post</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="3" name="permission_id[]" id="post.edit">
                                <label for="post.edit">Edit Post</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="4" name="permission_id[]" id="post.delete">
                                <label for="post.delete">Delete Post</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="5" name="permission_id[]" id="post.list">
                                <label for="post.edit">List Post</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card my-4 border">
                    <div class="card-header">
                        <input type="checkbox" class="check-all" name="" id="product">
                        <label for="product" class="m-0">Module Product</label>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="2" name="permission_id[]" id="product.add">
                                <label for="product.add">Add Product</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="3" name="permission_id[]" id="product.edit">
                                <label for="product.edit">Edit Product</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="4" name="permission_id[]" id="product.delete">
                                <label for="product.delete">Delete Product</label>
                            </div>
                            <div class="col-md-3">
                                <input type="checkbox" class="permission" value="5" name="permission_id[]" id="product.list">
                                <label for="product.add">List Product</label>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" name="btn-add" class="btn btn-primary" value="Thêm mới">
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $('.check-all').click(function () {
        $(this).closest('.card').find('.permission').prop('checked', this.checked)
      })
</script>