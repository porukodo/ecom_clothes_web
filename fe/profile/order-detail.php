<?php
// profile/order-detail.php
if (session_status() === PHP_SESSION_NONE) session_start();
$order_id = (int)($_GET['id'] ?? 0);
$API_BASE = 'http://localhost/PTUD_Final/public';
?>

<?php include '../header.php'; ?>

<main class="bg-light py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="orders.php">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0 rounded-3" id="detailContent" style="display:none;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Đơn hàng #<span id="orderIdText"></span></h5>
                <span id="orderStatusBadge" class="badge"></span>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5 g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Thông tin người nhận</h6>
                        <p class="mb-1 fw-bold" id="receiverName"></p>
                        <p class="mb-1 text-muted" id="receiverPhone"></p>
                        <p class="mb-0 text-muted" id="receiverAddress"></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Thanh toán & Giao hàng</h6>
                        <p class="mb-1">Phương thức: <span class="fw-bold">COD (Tiền mặt)</span></p>
                        <p class="mb-0">Trạng thái thanh toán: <span id="paymentStatusText" class="fw-bold"></span></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsBody"></tbody>
                        <tfoot class="border-top-0">
                            <tr>
                                <td colspan="3" class="text-end text-muted">Tạm tính:</td>
                                <td id="subtotalText" class="text-end"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Phí vận chuyển:</td>
                                <td id="shippingFeeText" class="text-end"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Giảm giá:</td>
                                <td id="discountText" class="text-end text-success"></td>
                            </tr>
                            <tr class="fw-bold fs-5">
                                <td colspan="3" class="text-end text-dark">Tổng cộng:</td>
                                <td id="totalAmountText" class="text-end text-danger"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div id="detailLoading" class="text-center py-5">
            <div class="spinner-border" role="status"></div>
            <p class="mt-2 text-muted">Đang tải chi tiết đơn hàng...</p>
        </div>
    </div>
</main>

<script>
function formatPrice(price) {
    return parseInt(price).toLocaleString('vi-VN') + '₫';
}

async function loadOrderDetail() {
    const orderId = <?php echo $order_id; ?>;
    const statusMap = {
        'CHO_XU_LY':  { text: 'Chờ xử lý',   class: 'bg-warning text-dark' },
        'DANG_XU_LY': { text: 'Đã xác nhận',  class: 'bg-info text-white' },
        'HOAN_TAT':   { text: 'Hoàn thành',  class: 'bg-success text-white' },
        'HUY':        { text: 'Đã hủy',      class: 'bg-danger text-white' }
    };

    try {
        const response = await fetch(`<?php echo $API_BASE; ?>/api/don-hang/${orderId}`, { credentials: 'include' });
        const result = await response.json();

        if(!result.ok) {
            alert(result.error || 'Không tìm thấy đơn hàng');
            window.location.href = 'orders.php';
            return;
        }

        const dh = result.don_hang;
        const items = result.chi_tiet;

        document.getElementById('orderIdText').innerText = dh.ma_don_hang;
        document.getElementById('receiverName').innerText = dh.nguoi_nhan;
        document.getElementById('receiverPhone').innerText = dh.sdt_nguoi_nhan;
        document.getElementById('receiverAddress').innerText = dh.dia_chi_giao_hang;
        document.getElementById('paymentStatusText').innerText = dh.trang_thai_thanh_toan === 'DA_THANH_TOAN' ? 'Đã thanh toán' : 'Chưa thanh toán';
        
        const statusBadge = document.getElementById('orderStatusBadge');
        const s = statusMap[dh.trang_thai] || { text: dh.trang_thai, class: 'bg-secondary' };
        statusBadge.innerText = s.text;
        statusBadge.className = 'badge ' + s.class;

        document.getElementById('subtotalText').innerText = formatPrice(dh.tam_tinh);
        document.getElementById('shippingFeeText').innerText = formatPrice(dh.phi_van_chuyen);
        document.getElementById('discountText').innerText = '-' + formatPrice(dh.giam_gia);
        document.getElementById('totalAmountText').innerText = formatPrice(dh.tong_tien);

        const itemsBody = document.getElementById('orderItemsBody');
        itemsBody.innerHTML = items.map(item => `
            <tr>
                <td><div class="fw-bold">${item.ten_san_pham}</div></td>
                <td class="text-center">${item.so_luong}</td>
                <td class="text-end">${formatPrice(item.don_gia)}</td>
                <td class="text-end fw-bold">${formatPrice(item.thanh_tien)}</td>
            </tr>`).join('');

        document.getElementById('detailLoading').style.display = 'none';
        document.getElementById('detailContent').style.display = 'block';

    } catch (e) {
        alert('Lỗi kết nối máy chủ');
    }
}
document.addEventListener('DOMContentLoaded', loadOrderDetail);
</script>
<?php include '../footer.php'; ?>