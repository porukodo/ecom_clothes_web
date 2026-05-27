<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminAuth();
$page_title = 'Quản lý Khuyến mãi';
$active_page = 'khuyenmai';

// Xử lý Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $id = $_POST['id'] ?? null;
        
        $ma_khuyen_mai = strtoupper(sanitize($_POST['ma_khuyen_mai']));
        $ten_chuong_trinh = sanitize($_POST['ten_chuong_trinh']);
        
        // [CỐ ĐỊNH] Luôn là voucher
        $loai_hinh = 'voucher'; 
        
        // Dữ liệu chiết khấu
        $loai_giam_gia = $_POST['loai_giam_gia'];
        $gia_tri_giam = (float)str_replace([',', '.'], '', $_POST['gia_tri_giam']);
        $giam_toi_da = (float)str_replace([',', '.'], '', $_POST['giam_toi_da']);
        $don_toi_thieu = (float)str_replace([',', '.'], '', $_POST['don_toi_thieu']);

        $ngay_bat_dau = $_POST['ngay_bat_dau'];
        $gio_bat_dau = $_POST['gio_bat_dau'];
        $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
        $gio_ket_thuc = $_POST['gio_ket_thuc'];
        $trang_thai = $_POST['trang_thai'] ?? 'active';
        
        // --- VALIDATE DỮ LIỆU ---
        
        // 1. Validate Mã Code
        if (!preg_match('/^[A-Z0-9]{1,20}$/', $ma_khuyen_mai)) {
            showMessage('Mã không hợp lệ! Chỉ dùng chữ in hoa và số.', 'danger');
            redirect('promotion.php');
        }

        // 2. [MỚI] Validate Số âm
        if ($gia_tri_giam < 0 || $giam_toi_da < 0 || $don_toi_thieu < 0) {
            showMessage('Vui lòng không nhập số âm!', 'danger');
            redirect('promotion.php');
        }

        // 3. Validate Phần trăm > 100
        if ($loai_giam_gia === 'percent' && $gia_tri_giam > 100) {
            showMessage('Giảm giá phần trăm không được quá 100%!', 'danger');
            redirect('promotion.php');
        }
        
        try {
            if ($action === 'add') {
                $sql = "INSERT INTO khuyen_mai 
                        (ma_khuyen_mai, ten_chuong_trinh, loai_hinh, loai_giam_gia, gia_tri_giam, giam_toi_da, don_toi_thieu, ngay_bat_dau, gio_bat_dau, ngay_ket_thuc, gio_ket_thuc, trang_thai) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$ma_khuyen_mai, $ten_chuong_trinh, $loai_hinh, $loai_giam_gia, $gia_tri_giam, $giam_toi_da, $don_toi_thieu, $ngay_bat_dau, $gio_bat_dau, $ngay_ket_thuc, $gio_ket_thuc, $trang_thai]);
                showMessage('Đã tạo mã khuyến mãi mới!');
            } else {
                $sql = "UPDATE khuyen_mai SET 
                        ten_chuong_trinh = ?, loai_hinh = ?, loai_giam_gia = ?, gia_tri_giam = ?, giam_toi_da = ?, don_toi_thieu = ?, 
                        ngay_bat_dau = ?, gio_bat_dau = ?, ngay_ket_thuc = ?, gio_ket_thuc = ?, trang_thai = ? 
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$ten_chuong_trinh, $loai_hinh, $loai_giam_gia, $gia_tri_giam, $giam_toi_da, $don_toi_thieu, $ngay_bat_dau, $gio_bat_dau, $ngay_ket_thuc, $gio_ket_thuc, $trang_thai, $id]);
                showMessage('Cập nhật mã khuyến mãi thành công!');
            }
            redirect('promotion.php');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                showMessage('Mã khuyến mãi này đã tồn tại!', 'danger');
            } else {
                showMessage('Lỗi: ' . $e->getMessage(), 'danger');
            }
        }
    }
    
    // Xóa
    if ($action === 'delete') {
        $id = $_POST['id'];
        try {
            $pdo->prepare("DELETE FROM khuyen_mai WHERE id = ?")->execute([$id]);
            showMessage('Xóa thành công!');
        } catch (PDOException $e) { showMessage('Lỗi xóa!', 'danger'); }
        redirect('promotion.php');
    }
    
    // Status
    if ($action === 'update_status') {
        $id = $_POST['id']; $status = $_POST['status'];
        $pdo->prepare("UPDATE khuyen_mai SET trang_thai = ? WHERE id = ?")->execute([$status, $id]);
        showMessage('Cập nhật trạng thái thành công!');
        redirect('promotion.php');
    }
}

// Lấy danh sách (Đã bỏ lọc theo Type)
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM khuyen_mai WHERE 1=1";
$params = [];

// Mặc định chỉ lấy voucher (nếu trong DB cũ có san-pham thì nó sẽ ẩn đi)
$sql .= " AND loai_hinh = 'voucher'";

if ($status_filter && $status_filter !== 'all') { 
    $sql .= " AND trang_thai = ?"; 
    $params[] = $status_filter; 
}

$sql .= " ORDER BY tao_luc DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$promotions = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="modal fade" id="promoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalTitle">Tạo mã khuyến mãi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body px-4 pb-4">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="promoId">
                    <input type="hidden" name="loai_hinh" value="voucher">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-secondary">Mã khuyến mãi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-ticket"></i></span>
                                <input type="text" class="form-control fw-bold" name="ma_khuyen_mai" id="promoCode" 
                                       placeholder="VD: SALEHE2025" required maxlength="20" style="text-transform: uppercase; letter-spacing: 1px;">
                            </div>
                            <div class="form-text text-xs text-nowrap overflow-hidden">Khách nhập khi thanh toán.</div>
                        </div>
                        
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-secondary">Tên chương trình <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ten_chuong_trinh" id="promoName" placeholder="Ví dụ: Siêu sale mùa hè" required>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 rounded-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calculator me-1"></i> Cấu hình giảm giá</h6>
                            
                            <div class="mb-3 d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loai_giam_gia" id="discPercent" value="percent" checked onchange="toggleDiscountType()">
                                    <label class="form-check-label fw-bold" for="discPercent">Theo phần trăm (%)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loai_giam_gia" id="discFixed" value="fixed" onchange="toggleDiscountType()">
                                    <label class="form-check-label fw-bold" for="discFixed">Theo số tiền (VNĐ)</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Mức giảm</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control fw-bold text-primary" name="gia_tri_giam" id="discValue" required placeholder="0" min="0">
                                        <span class="input-group-text fw-bold" id="discUnit">%</span>
                                    </div>
                                    <div class="form-text text-xs" id="discHint">Nhập số % (VD: 10)</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Giảm tối đa</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="giam_toi_da" id="maxDisc" placeholder="0" min="0">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    <div class="form-text text-xs">0 = Không giới hạn</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Đơn tối thiểu</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="don_toi_thieu" id="minOrder" placeholder="0" min="0">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    <div class="form-text text-xs">Giá trị đơn hàng tối thiểu</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Bắt đầu</label>
                            <input type="date" class="form-control mb-1" name="ngay_bat_dau" id="startDate" required>
                            <input type="time" class="form-control" name="gio_bat_dau" id="startTime" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Kết thúc</label>
                            <input type="date" class="form-control mb-1" name="ngay_ket_thuc" id="endDate" required>
                            <input type="time" class="form-control" name="gio_ket_thuc" id="endTime" required>
                        </div>
                    </div>

                    <input type="hidden" name="trang_thai" id="promoStatus" value="active">
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light flex-grow-1 fw-bold py-2 rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-dark-custom flex-grow-1 shadow">Lưu mã khuyến mãi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="d-flex min-vh-100 bg-light">
    <div class="d-none d-lg-block border-end bg-white" style="width: 260px; min-width: 260px;">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <main class="flex-grow-1 p-3 p-lg-4" style="overflow-x: hidden;">
        <div class="d-lg-none d-flex align-items-center justify-content-between mb-4">
             <button class="btn btn-white border shadow-sm rounded-3"><i class="fa-solid fa-bars"></i></button>
             <span class="fw-bold">Admin</span>
        </div>

        <?php displayMessage(); ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-1">Quản lý mã khuyến mãi</h2>
            </div>
            <button onclick="openModal()" class="btn btn-dark-custom d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Thêm mã khuyến mãi
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-10">
                        <select name="status" class="form-select">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Đang hoạt động</option>
                            <option value="disabled" <?php echo $status_filter === 'disabled' ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0 align-middle">
                    <thead class="bg-light text-secondary small">
                        <tr>
                            <th class="ps-4">Tên / Mã Code</th>
                            <th>Nội dung giảm giá</th>
                            <th>Thời gian áp dụng</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <?php if (empty($promotions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Chưa có mã khuyến mãi nào được tạo</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promotions as $promo): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($promo['ten_chuong_trinh']); ?></div>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-primary border border-primary-subtle fw-bold font-monospace px-2 py-1">
                                                <i class="fa-solid fa-ticket me-1"></i><?php echo htmlspecialchars($promo['ma_khuyen_mai']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="fw-bold text-primary">
                                            <?php if ($promo['loai_giam_gia'] === 'percent'): ?>
                                                Giảm <?php echo number_format($promo['gia_tri_giam']); ?>%
                                                <?php if ($promo['giam_toi_da'] > 0): ?>
                                                    <span class="text-secondary fw-normal small">
                                                        (Tối đa <?php echo number_format($promo['giam_toi_da']); ?>đ)
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                Giảm <?php echo number_format($promo['gia_tri_giam']); ?>đ
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-secondary mt-1">
                                            Đơn tối thiểu: 
                                            <?php echo ($promo['don_toi_thieu'] > 0) ? '<b>'.number_format($promo['don_toi_thieu']).'đ</b>' : '0đ'; ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="small fw-bold"><?php echo date('d/m/Y', strtotime($promo['ngay_bat_dau'])); ?></div>
                                        <div class="small text-muted">đến <?php echo date('d/m/Y', strtotime($promo['ngay_ket_thuc'])); ?></div>
                                    </td>
                                    
                                    <td>
                                        <?php if ($promo['trang_thai'] === 'active'): ?>
                                            <span class="badge bg-success-subtle text-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">Đã tắt</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-end pe-4">
                                        <button onclick='editPromo(<?php echo json_encode($promo); ?>)' class="btn btn-sm btn-light border me-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa mã này?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                                            <button class="btn btn-sm btn-light border text-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
let promoModal;

document.addEventListener('DOMContentLoaded', function() {
    promoModal = new bootstrap.Modal(document.getElementById('promoModal'));
    
    // Auto format mã code
    document.getElementById('promoCode').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, "").slice(0, 20);
    });
});

function toggleDiscountType() {
    const isPercent = document.getElementById('discPercent').checked;
    const discUnit = document.getElementById('discUnit');
    const discHint = document.getElementById('discHint');
    const maxDiscInput = document.getElementById('maxDisc');
    const discValueInput = document.getElementById('discValue');
    
    if (isPercent) {
        discUnit.innerText = '%';
        discHint.innerText = 'Nhập số % (VD: 10)';
        
        // [MỚI] Thêm ràng buộc Max = 100 khi chọn %
        discValueInput.setAttribute('max', '100'); 
        
        maxDiscInput.removeAttribute('disabled'); 
        maxDiscInput.placeholder = 'VD: 50000';
    } else {
        discUnit.innerText = 'đ';
        discHint.innerText = 'Nhập số tiền VNĐ (VD: 20000)';
        
        // [MỚI] Xóa ràng buộc Max khi chọn tiền mặt
        discValueInput.removeAttribute('max');
        
        maxDiscInput.setAttribute('disabled', true);
        maxDiscInput.value = '';
        maxDiscInput.placeholder = 'Không dùng';
    }
}

function openModal() {
    document.getElementById('formAction').value = 'add';
    document.getElementById('modalTitle').innerText = 'Tạo mã khuyến mãi mới';
    document.getElementById('promoId').value = '';
    
    // Reset Form
    document.getElementById('promoCode').value = '';
    document.getElementById('promoCode').removeAttribute('readonly');
    document.getElementById('promoName').value = '';
    
    // Reset Discount Logic
    document.getElementById('discPercent').checked = true;
    document.getElementById('discValue').value = '';
    document.getElementById('maxDisc').value = '';
    document.getElementById('minOrder').value = '';
    
    // Reset Time
    document.getElementById('startDate').value = '';
    document.getElementById('startTime').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('endTime').value = '';
    
    toggleDiscountType(); 
    promoModal.show();
}

function editPromo(promo) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('modalTitle').innerText = 'Sửa mã khuyến mãi: ' + promo.ma_khuyen_mai;
    document.getElementById('promoId').value = promo.id;
    
    document.getElementById('promoCode').value = promo.ma_khuyen_mai;
    document.getElementById('promoCode').setAttribute('readonly', true);
    document.getElementById('promoName').value = promo.ten_chuong_trinh;
    
    if (promo.loai_giam_gia === 'percent') {
        document.getElementById('discPercent').checked = true;
    } else {
        document.getElementById('discFixed').checked = true;
    }
    document.getElementById('discValue').value = parseFloat(promo.gia_tri_giam);
    document.getElementById('maxDisc').value = parseFloat(promo.giam_toi_da) || '';
    document.getElementById('minOrder').value = parseFloat(promo.don_toi_thieu) || '';
    
    document.getElementById('startDate').value = promo.ngay_bat_dau;
    document.getElementById('startTime').value = promo.gio_bat_dau;
    document.getElementById('endDate').value = promo.ngay_ket_thuc;
    document.getElementById('endTime').value = promo.gio_ket_thuc;

    toggleDiscountType();
    promoModal.show();
}
</script>

<?php include 'includes/footer.php'; ?>