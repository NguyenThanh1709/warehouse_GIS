//Google map
function initMap(warehouses) {
  var latLng = {
    lat: 21.0168864,
    lng: 105.7855574,
  };

  var map = new google.maps.Map(document.getElementById("map"), {
    center: latLng,
    zoom: 4,
  });

  var markers = [];

  var infowindow = new google.maps.InfoWindow(); // Tạo InfoWindow cho tất cả các marker

  // Hiển thị các marker từ cơ sở dữ liệu
  warehouses.forEach((warehouse) => {
    var marker = new google.maps.Marker({
      position: {
        lat: parseFloat(warehouse.latitude),
        lng: parseFloat(warehouse.longitude),
      },
      map: map,
      title: warehouse.name,
    });

    marker.addListener("click", function () {
      // Hiển thị tọa độ trong modal khi click vào marker
      document.getElementById("modal-lat").value = warehouse.latitude;
      document.getElementById("modal-lng").value = warehouse.longitude;
      // document.getElementById("82F9D570-1A1F-4AAD-8255-82048A410FEA").innerHTML = `<h4>${warehouse.name}</h4>`;

      // Nội dung chi tiết sẽ hiển thị khi click vào marker
      var contentString =
        "<div>" +
        "<strong>Tên kho : " +
        warehouse.name +
        "</strong> " +
        "<br>" +
        "<b>Địa chỉ :</b> " +
        warehouse.address +
        "<br>" +
        "<b>Kinh độ :</b> " +
        warehouse.latitude +
        "<br>" +
        "<b>Vĩ độ :</b> " +
        warehouse.longitude +
        "</div>";

      // Hiển thị nội dung chi tiết từng
      infowindow.setContent(contentString);
      infowindow.open(map, marker);
    });
  });

  map.addListener("click", function (e) {
    // Xóa tất cả các marker cũ sau khi nhấp chuột
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }
    markers = [];

    // Tạo marker mới tại vị trí e.latLng
    var marker = new google.maps.Marker({
      position: e.latLng,
      map: map,
    });

    // Lấy tọa độ của marker
    var latitude = e.latLng.lat();
    var longitude = e.latLng.lng();

    // Gán giá trị kinh độ và vĩ độ vào các trường trong modal
    document.getElementById("modal-lat").value = latitude;
    document.getElementById("modal-lng").value = longitude;

    //Thiết lập lại nội dung cho Modal
    $("#locationModalLabel").text("Thêm mới");
    $(".btn-edit-warehouse").text("Thêm mới");
    $(".btn-edit-warehouse").attr("type", "submit");

    //Làm sách trường modal
    $("input[name=name]").val("");
    $("input[name=address]").val("");
    $("input[name=phone_contact]").val("");
    $("input[name=name_contact]").val("");

    // Hiển thị modal
    $("#locationModal").modal("show");

    map.panTo(marker.position); // Đặt điểm mới làm trung tâm bản đồ

    markers.push(marker);
  });
}

$(document).ready(function () {
  $(".nav-link.active .sub-menu").slideDown();
  // $("p").slideUp();

  $("#sidebar-menu .arrow").click(function () {
    $(this).parents("li").children(".sub-menu").slideToggle();
    $(this).toggleClass("fa-angle-right fa-angle-down");
  });

  $("input[name='checkall']").click(function () {
    var checked = $(this).is(":checked");
    $(".table-checkall tbody tr td input:checkbox").prop("checked", checked);
  });

  //Xử lý lọc dữ liệu modal
  $("#keyword_text").keyup(function () {
    var keyword = $(this).val(); // Lấy dữ liệu người dùng đang nhập
    var module = $("#exampleModal").attr("module");
    var id_warehouse = $(".warehouse-select").val() ?? "";
    console.log(id_warehouse);
    var data = { keyword: keyword, module: module, id_warehouse: id_warehouse };
    $.ajax({
      url: "?module=servers&action=server", //url xử lý
      method: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        //Lấy dữ liệu trả về gán vào input
        console.log(response);
        $(".list-tr").html(response.str);
      },
    });
  });

  //Xử lý thêm giỏ hàng
  $(document).on("click", ".btn-add-cart", function () {
    var id_product = $(this).data("id"); //Lấy id sản phẩm
    var qty = $("#qty-" + id_product).val(); //Lấy số lượng
    var module = $("#exampleModal").attr("module");
    var data = { qty: qty, id_product: id_product, module: module }; //khởi tạo object
    // console.log(data);
    $.ajax({
      url: "?module=servers&action=server", //url xử lý
      method: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        //Lấy dữ liệu trả về gán vào input
        console.log(response);
        $(".list-product-cart").html(response.str);
        $("#total").html(response.total);
      },
    });
  });
});

//Xử lý cập nhật thay đổi số lượng
$(document).on("change", ".quantity", function () {
  var id_product = $(this).data("id"); //Lấy id sản phẩm
  var qty = $("#qty-" + id_product).val(); //Lấy số lượng
  var module = $("#exampleModal").attr("module");
  var data = { qty: qty, id_product: id_product, module: module }; //khởi tạo object
  $.ajax({
    url: "?module=servers&action=server", //url xử lý
    method: "POST",
    data: data,
    dataType: "json",
    success: function (response) {
      //Lấy dữ liệu trả về gán vào input
      // console.log(response);
      $(".list-product-cart").html(response.str); //nối chuổi html
      $("#total").html(response.total); //nối chuổi html
    },
  });
});

//Xử lý modal vị trí toạ độ
$(document).ready(function () {
  initMap(warehouses);

  $(".edit-modal").click(function () {
    //Lấy id
    var id = $(this).data("id");
    var data = { id_warehouse: id };

    //Thiết lập nội dung form modal
    $("#locationModalLabel").text("Thông tin chi tiết địa điểm kho");
    $(".btn-edit-warehouse").text("Cập nhật");
    $(".btn-edit-warehouse").attr("type", "submit");

    $.ajax({
      url: "?module=dashboard&action=server",
      method: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        //Lấy dữ liệu trả về gán vào input
        $("input[name=name]").val(response.name);
        $("input[name=address]").val(response.address);
        $("input[name=phone_contact]").val(response.phone_contact);
        $("input[name=name_contact]").val(response.name_contact);
        $("input[name=latitude]").val(response.latitude);
        $("input[name=longitude]").val(response.longitude);
        $("input[name=id_warehouse]").val(response.id);
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
        console.log(thrownError);
      },
    });
  });
});
