<div id="content" class="container-fluid">
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Thêm quyền
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="name">Tên quyền</label>
                            <input class="form-control" type="text" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <small class="form-text text-muted pb-2">Ví dụ: posts.add</small>                            
                            <input class="form-control" type="text" name="slug" id="slug">
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control" type="text" name="description" id="description"> </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm mới</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Danh sách quyền
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tên quyền</th>
                                <th scope="col">Slug</th>
                                <!-- <th scope="col">Mô tả</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row"></td>
                                <td><strong>Post</strong></td>
                                <td></td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">1</td>
                                <td>|---Add Post</td>
                                <td>post.add</td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">2</td>
                                <td>|---Edit Post</td>
                                <td>post.edit</td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">3</td>
                                <td>|---Delete Post</td>
                                <td>post.delete</td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row"></td>
                                <td><strong>Product</strong></td>
                                <td></td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">4</td>
                                <td>|---Add Product</td>
                                <td>product.add</td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">5</td>
                                <td>|---Edit Product</td>
                                <td>product.edit</td>
                                <!-- <td></td> -->
                            </tr>
                            <tr>
                                <td scope="row">6</td>
                                <td>|---Delete Product</td>
                                <td>product.delete</td>
                                <!-- <td></td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>