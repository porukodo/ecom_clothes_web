<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminAuth();
$page_title = 'Chi tiết đơn hàng';
$active_page = 'orders';

$order_id = $_GET['id'] ?? 0;

// Xử lý POST (Cập nhật trạng thái + Hoàn kho)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // --- 1. Xử lý Trạng thái Đơn hàng (Bao gồm Duyệt/Hủy/Cập nhật thường) ---
    if ($action === 'update_status') {
        $new_status = $_POST['new_status'];
        
        try {
            // Lấy trạng thái cũ
            $stmt = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id = ?");
            $stmt->execute([$order_id]);
            $old_status = $stmt->fetchColumn();
            
            if ($old_status !== $new_status) {
                $pdo->beginTransaction();

                // *** LOGIC HOÀN KHO (RESTOCK) ***
                // Nếu trạng thái mới là HUY và trạng thái cũ KHÔNG phải HUY
                // Thì tiến hành cộng lại số lượng tồn kho
                if ($new_status === 'HUY' && $old_status !== 'HUY') {
                    // Lấy danh sách sản phẩm trong đơn
                    $stmtItems = $pdo->prepare("SELECT san_pham_id, sku_id, so_luong FROM chi_tiet_don_hang WHERE don_hang_id = ?");
                    $stmtItems->execute([$order_id]);
                    $items = $stmtItems->fetchAll();

                    foreach ($items as $item) {
                        // 1. Cộng lại kho SKU
                        if ($item['sku_id']) {
                            $upSku = $pdo->prepare("UPDATE sku_san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?");
                            $upSku->execute([$item['so_luong'], $item['sku_id']]);
                        }
                        // 2. Cộng lại kho Sản phẩm cha (nếu có quản lý tổng)
                        $upSp = $pdo->prepare("UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?");
                        $upSp->execute([$item['so_luong'], $item['san_pham_id']]);
                    }
                }
                // *** HẾT LOGIC HOÀN KHO ***

                // Cập nhật trạng thái
                $stmt = $pdo->prepare("UPDATE don_hang SET trang_thai = ? WHERE id = ?");
                $stmt->execute([$new_status, $order_id]);
                
                // Ghi log
                $note = isset($_POST['note']) ? $_POST['note'] : null;
                $stmt = $pdo->prepare("INSERT INTO lich_su_trang_thai_don_hang (don_hang_id, tu_trang_thai, den_trang_thai, nguoi_thay_doi_id, ghi_chu) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$order_id, $old_status, $new_status, $_SESSION['admin_id'], $note]);
                
                $pdo->commit();
                showMessage('Cập nhật trạng thái thành công!');
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            showMessage('Lỗi: ' . $e->getMessage(), 'danger');
        }
    }
    
    // --- 2. Cập nhật trạng thái thanh toán ---
    elseif ($action === 'update_payment_status') {
        $new_payment_status = $_POST['payment_status'];
        try {
            // --- LOGIC MỚI: Kiểm tra xem đơn có bị hủy không trước khi update thanh toán ---
            $stmtCheck = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id = ?");
            $stmtCheck->execute([$order_id]);
            $current_status = $stmtCheck->fetchColumn();

            if ($current_status === 'HUY') {
                showMessage('Không thể cập nhật thanh toán cho đơn hàng đã HỦY!', 'danger');
            } else {
                $stmt = $pdo->prepare("UPDATE don_hang SET trang_thai_thanh_toan = ? WHERE id = ?");
                $stmt->execute([$new_payment_status, $order_id]);
                showMessage('Cập nhật trạng thái thanh toán thành công!');
            }
            // -----------------------------------------------------------------------------
        } catch (PDOException $e) {
            showMessage('Lỗi: ' . $e->getMessage(), 'danger');
        }
    }
    
    redirect("orders_detail.php?id=$order_id");
}

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("SELECT dh.*, nd.ho_ten, nd.email, nd.so_dien_thoai 
                        FROM don_hang dh 
                        LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
                        WHERE dh.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

// Lấy chi tiết sản phẩm
$stmt = $pdo->prepare("SELECT * FROM chi_tiet_don_hang WHERE don_hang_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Lấy lịch sử trạng thái
$stmt = $pdo->prepare("SELECT ls.*, nd.ho_ten as nguoi_thay_doi 
                        FROM lich_su_trang_thai_don_hang ls 
                        LEFT JOIN nguoi_dung nd ON ls.nguoi_thay_doi_id = nd.id 
                        WHERE ls.don_hang_id = ? 
                        ORDER BY ls.tao_luc DESC");
$stmt->execute([$order_id]);
$history = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</div>

<div class="d-flex min-vh-100 bg-light">
    
    <div class="d-none d-lg-block border-end bg-white" style="width: 260px; min-width: 260px;">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <main class="flex-grow-1 p-3 p-lg-4" style="overflow-x: hidden;">
    
        <div class="d-lg-none d-flex align-items-center justify-content-between mb-4">
            <button class="btn btn-white border shadow-sm rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <span class="fw-bold fs-5">Sole Studio</span>
            <img src="https://ui-avatars.com/api/?name=Admin+User" class="rounded-circle border" width="36" height="36" alt="Admin">
        </div>

        <?php displayMessage(); ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <a href="orders.php" class="btn btn-sm btn-light border rounded-3 mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
                <h2 class="fw-bold text-dark mb-1">Chi tiết Đơn hàng #<?php echo $order['ma_don_hang']; ?></h2>
                <p class="text-secondary small mb-0">Đặt lúc: <?php echo formatDateTime($order['tao_luc']); ?></p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <?php if($order['trang_thai'] === 'YEU_CAU_HUY'): ?>
                    <span class="badge bg-warning text-dark fs-6">Đang yêu cầu hủy</span>
                <?php else: ?>
                    <?php echo getStatusBadge($order['trang_thai'], 'order'); ?>
                <?php endif; ?>
                
                <button class="btn btn-dark-custom" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> In đơn
                </button>
            </div>
        </div>

        <?php if ($order['trang_thai'] === 'YEU_CAU_HUY'): ?>
            <div class="alert alert-warning border-warning shadow-sm mb-4">
                <h5 class="alert-heading fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Khách hàng yêu cầu hủy đơn</h5>
                <p class="mb-0">Lý do: <strong><?php echo htmlspecialchars($order['ly_do_huy']); ?></strong></p>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Sản phẩm đã đặt</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($order_items)): ?>
                            <p class="text-secondary text-center">Không có sản phẩm</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>SL</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo $item['ten_san_pham']; ?></div>
                                                    <div class="text-secondary small">ID: #<?php echo $item['san_pham_id']; ?></div>
                                                </td>
                                                <td><?php echo formatPrice($item['don_gia']); ?></td>
                                                <td><span class="badge bg-light text-dark border">x<?php echo $item['so_luong']; ?></span></td>
                                                <td class="text-end fw-bold"><?php echo formatPrice($item['thanh_tien']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Tạm tính:</span>
                            <span class="fw-bold"><?php echo formatPrice($order['tam_tinh']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Phí vận chuyển:</span>
                            <span class="fw-bold"><?php echo formatPrice($order['phi_van_chuyen']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Giảm giá:</span>
                            <span class="fw-bold text-danger">-<?php echo formatPrice($order['giam_gia']); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">Tổng cộng:</span>
                            <span class="fw-bold fs-4 text-primary"><?php echo formatPrice($order['tong_tien']); ?></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Họ tên:</div>
                            <div class="fw-bold"><?php echo $order['ho_ten'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Email:</div>
                            <div><?php echo $order['email'] ?? 'N/A'; ?></div>
                        </div>
                        <div>
                            <div class="text-secondary small mb-1">Số điện thoại:</div>
                            <div class="fw-bold"><?php echo $order['so_dien_thoai'] ?? 'N/A'; ?></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Người nhận:</div>
                            <div class="fw-bold"><?php echo $order['nguoi_nhan']; ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">SĐT người nhận:</div>
                            <div class="fw-bold"><?php echo $order['sdt_nguoi_nhan']; ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Địa chỉ:</div>
                            <div><?php echo $order['dia_chi_giao_hang']; ?></div>
                        </div>
                        <?php if ($order['ghi_chu']): ?>
                        <div>
                            <div class="text-secondary small mb-1">Ghi chú:</div>
                            <div class="fst-italic"><?php echo $order['ghi_chu']; ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Thanh toán</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Phương thức:</div>
                            <div class="fw-bold"><?php echo $order['phuong_thuc_thanh_toan']; ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Trạng thái hiện tại:</div>
                            <?php if ($order['trang_thai_thanh_toan'] === 'DA_THANH_TOAN'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Đã thanh toán</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">Chưa thanh toán</span>
                            <?php endif; ?>
                        </div>
                        
                        <hr class="my-3">

                        <div class="d-grid gap-2 mb-3">
                            <?php if ($order['trang_thai'] === 'HUY'): ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-ban me-1"></i> Đơn đã hủy
                                </button>
                            <?php else: ?>
                                <?php if ($order['trang_thai_thanh_toan'] !== 'DA_THANH_TOAN'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_payment_status">
                                    <input type="hidden" name="payment_status" value="DA_THANH_TOAN">
                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                        <i class="fas fa-check me-1"></i> Xác nhận đã thanh toán
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($order['trang_thai_thanh_toan'] !== 'CHUA_THANH_TOAN'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_payment_status">
                                    <input type="hidden" name="payment_status" value="CHUA_THANH_TOAN">
                                    <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                        <i class="fas fa-undo me-1"></i> Đánh dấu chưa thanh toán
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <hr class="my-3">
                        
                        <div class="mb-2 fw-bold small">Cập nhật trạng thái đơn:</div>
                        <?php if ($order['trang_thai'] === 'HUY'): ?>
                            <!-- KHI ĐƠN ĐÃ HỦY: Hiển thị nút bị disabled -->
                            <button class="btn btn-outline-secondary w-100" disabled>
                                <i class="fas fa-ban me-1"></i> Không thể cập nhật đơn đã huỷ
                            </button>
                        <?php else: ?>
                            <!-- KHI ĐƠN CHƯA HỦY: Hiển thị dropdown như bình thường -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    Chọn trạng thái
                                </button>
                                <ul class="dropdown-menu shadow border-0 w-100">
                                    <?php 
                                    $statuses = [
                                        'CHO_XU_LY' => ['label' => 'Chờ xử lý', 'class' => ''],
                                        'DANG_XU_LY' => ['label' => 'Đang xử lý', 'class' => ''],
                                        'HOAN_TAT' => ['label' => 'Hoàn tất', 'class' => 'text-success'],
                                        'HUY' => ['label' => 'Hủy đơn (Hoàn kho)', 'class' => 'text-danger', 'confirm' => true]
                                    ];
                                    foreach($statuses as $key => $val):
                                        if($order['trang_thai'] !== $key && $key !== 'YEU_CAU_HUY'): 
                                    ?>
                                    <li>
                                        <form method="POST" action="" <?php echo isset($val['confirm']) ? "onsubmit=\"return confirm('Xác nhận đổi trạng thái? Nếu chọn HỦY, kho sẽ được hoàn lại.')\"" : ""; ?>>
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="new_status" value="<?php echo $key; ?>">
                                            <button type="submit" class="dropdown-item <?php echo $val['class']; ?>"><?php echo $val['label']; ?></button>
                                        </form>
                                    </li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($history)): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Lịch sử trạng thái</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            <?php foreach ($history as $h): ?>
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="fw-bold small"><?php echo formatDateTime($h['tao_luc']); ?></div>
                                    </div>
                                    <div class="text-secondary small mb-1">
                                        <?php if ($h['tu_trang_thai']): ?>
                                            Từ: <span class="fw-bold"><?php echo $h['tu_trang_thai']; ?></span>
                                        <?php endif; ?>
                                        → Đến: <span class="fw-bold text-primary"><?php echo $h['den_trang_thai']; ?></span>
                                    </div>
                                    <?php if ($h['ghi_chu']): ?>
                                        <div class="text-danger small fst-italic mb-1">Note: <?php echo $h['ghi_chu']; ?></div>
                                    <?php endif; ?>
                                    <?php if ($h['nguoi_thay_doi']): ?>
                                        <div class="text-secondary small">Bởi: <?php echo $h['nguoi_thay_doi']; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>
</div>
<style>
@media print {
    .d-none.d-lg-block { display: none !important; }
    main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    .btn, .offcanvas, aside, .alert { display: none !important; }
}
</style>

<?php include 'includes/footer.php'; ?>
