<?php
declare(strict_types=1);

session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/ecom_clothes_web',
  'httponly' => true,
  'samesite' => 'Lax',
]);
session_start();

if (!isset($_SESSION['nguoi_dung_id'])) {
  header('Location: login.php');
  exit;
}

$shipping_fee = 30000; 
$current_page = "Hoàn tất đơn hàng";
include 'header.php'; 
?>

<style>
    .form-label { font-weight: 600; font-size: 0.9rem; }
    .required::after { content: " *"; color: #dc3545; }
    
    .product-item { 
        display: flex; 
        align-items: center; 
        padding: 10px 0; 
        border-bottom: 1px dashed #dee2e6; 
    }
    .product-item:last-child { border-bottom: none; }
    .product-name { font-weight: 600; font-size: 0.95rem; line-height: 1.2; }
    .product-meta { font-size: 0.85rem; color: #6c757d; margin-top: 4px; }
    .product-price { font-weight: 600; font-size: 0.95rem; }

    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem; }
    .summary-total { 
        border-top: 2px solid #000; 
        padding-top: 15px; 
        margin-top: 15px; 
        font-weight: 700; 
        font-size: 1.2rem; 
        display: flex; 
        justify-content: space-between; 
    }

    .location-badge {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #000;
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 20px;
    }

    /* --- Tối ưu Modal Sổ địa chỉ chuẩn xác --- */
    #modalAddressBook .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    #modalAddressBook .modal-header {
        border-bottom: 1px solid #f1f1f1;
        padding: 1.25rem 1.5rem;
    }
    #addressBookList {
        max-height: 450px;
        overflow-y: auto;
        overflow-x: hidden; /* Chặn tuyệt đối cuộn ngang */
        padding: 10px 0;
        width: 100%;
    }
    .btn-select-address {
        border: 1px solid #eee !important;
        margin: 8px 15px; /* Giữ khoảng cách với mép Popup */
        border-radius: 12px !important;
        transition: all 0.2s ease;
        cursor: pointer;
        background: #fff;
        display: block;
        padding: 15px;
        text-decoration: none;
        color: inherit;
        /* Quan trọng: Ép chiều dài khung vừa khít với Popup */
        max-width: calc(100% - 30px); 
        box-sizing: border-box;
    }
    .btn-select-address:hover {
        border-color: #000 !important;
        background-color: #f9f9f9 !important;
        transform: translateY(-2px);
    }
    .address-icon {
        min-width: 32px;
        height: 32px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #666;
    }
    .address-detail-text {
        word-break: break-word;
        white-space: normal;
        line-height: 1.5;
        display: block;
        margin-top: 4px;
        font-size: 0.85rem;
        color: #333;
    }

    /* Validation Styles */
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }
</style>

<main class="container py-5">
    <div class="row">
        <div class="col-12 mb-4 text-center">
            <h2 class="fw-bold text-uppercase">Hoàn tất đơn hàng</h2>
        </div>
    </div>

    <div id="alertSuccess" class="alert alert-success shadow-sm" style="display:none;"></div>
    <div id="alertError" class="alert alert-danger shadow-sm" style="display:none;"></div>

    <form id="checkoutForm">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2"></i>Thông tin vận chuyển</h5>
                            <button type="button" class="btn btn-sm btn-dark px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAddressBook">
                                <i class="fas fa-address-book me-1"></i> Chọn từ Sổ địa chỉ
                            </button>
                        </div>
                        
                        <div class="location-badge" id="location-badge">
                            <i class="fas fa-info-circle me-1"></i> Vui lòng chọn khu vực giao hàng bên dưới
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Họ và tên</label>
                                <input type="text" class="form-control required" name="fullname" placeholder="Nhập họ tên" required>
                                <div class="invalid-feedback">Họ tên không được chứa số hoặc ký tự đặc biệt.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Số điện thoại</label>
                                <input type="tel" class="form-control required phone-check" name="phone" placeholder="Nhập số điện thoại" required>
                                <div class="invalid-feedback">Số điện thoại phải bắt đầu bằng 0 và có 10-11 chữ số.</div>
                            </div>
                            
                            <input type="hidden" name="province_text" id="province_text">
                            <input type="hidden" name="district_text" id="district_text">
                            <input type="hidden" name="ward_text" id="ward_text">

                            <div class="col-md-4">
                                <label class="form-label required">Tỉnh / Thành</label>
                                <select class="form-select" id="province" required>
                                    <option value="">Chọn Tỉnh/Thành</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Quận / Huyện</label>
                                <select class="form-select" id="district" required disabled>
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Phường / Xã</label>
                                <select class="form-select" id="ward" required disabled>
                                    <option value="">Chọn Phường/Xã</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label required">Địa chỉ cụ thể</label>
                                <input type="text" class="form-control required" name="address" placeholder="Số nhà, tên đường..." required>
                                <div class="invalid-feedback">Vui lòng nhập địa chỉ cụ thể.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ghi chú đơn hàng</label>
                                <textarea class="form-control" name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>
                        <div class="form-check p-3 border rounded mb-2 bg-light">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="cod" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i>Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Đơn hàng của bạn (<span id="orderCount">0</span>)</h5>
                        <div id="orderItems" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-center text-muted my-3">Đang tải giỏ hàng...</p>
                        </div>

                        <div class="input-group mb-4">
                            <input type="text" class="form-control" id="discountInput" placeholder="Mã giảm giá">
                            <button class="btn btn-dark" type="button" id="applyDiscount">Áp dụng</button>
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-bold" id="subtotalText">0₫</span>
                        </div>
                        <div class="summary-row text-success" id="discountRow" style="display: none;">
                            <span><i class="fas fa-ticket-alt me-1"></i>Giảm giá</span>
                            <span class="fw-bold" id="discountText">-0₫</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Phí vận chuyển</span>
                            <span class="fw-bold" id="shippingText"><?php echo number_format($shipping_fee, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="summary-total">
                            <span>Tổng cộng</span>
                            <span class="text-danger" id="final-total">0₫</span>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-3 mt-4 fw-bold text-uppercase" id="btnPlaceOrder" disabled>
                            Đặt hàng ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<div class="modal fade" id="modalAddressBook" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-address-book me-2"></i>Địa chỉ đã lưu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="addressBookList" class="list-group list-group-flush">
                    <div class="p-5 text-center text-muted small">Đang tải sổ địa chỉ...</div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light w-100 rounded-pill" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const API_BASE = 'http://localhost/ecom_clothes_web/public';
    const DEFAULT_SHIPPING = <?php echo (int)$shipping_fee; ?>;

    let cart = null;           
    let subtotal = 0;
    let shippingFee = DEFAULT_SHIPPING;
    let discountAmount = 0;    
    let discountCodeApplied = '';
    
    // Biến cho chế độ Mua ngay
    let buyNowMode = false;
    let buyNowInfo = {};

    function vnd(n) { return Number(n || 0).toLocaleString('vi-VN') + '₫'; }

    // === 1. VALIDATION UTILS ===
    function isValidVietnameseName(name) {
        // Tên phải có ít nhất 2 ký tự và không chứa ký tự đặc biệt lạ
        return name.length >= 2 && !/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(name);
    }

    function isValidPhone(phone) {
        return /^0\d{9,10}$/.test(phone);
    }

    /**
     * Hàm kiểm tra form
     * @param {boolean} isSilent - Nếu true: Chỉ kiểm tra để bật/tắt nút Submit, KHÔNG hiện lỗi đỏ (Dùng khi mới load trang)
     */
    function validateCheckoutForm(isSilent = false) {
        const inputs = document.querySelectorAll('#checkoutForm .form-control.required');
        let allValid = true;
        
        // 1. Kiểm tra các ô input text
        inputs.forEach(input => {
            const val = input.value ? input.value.trim() : '';
            let isValid = true;

            if (val === '') {
                isValid = false;
            } else if (input.name === 'fullname' && !isValidVietnameseName(val)) {
                isValid = false;
            } else if (input.classList.contains('phone-check') && !isValidPhone(val)) {
                isValid = false;
            }

            // Nếu KHÔNG phải kiểm tra ngầm (isSilent = false) thì mới hiện màu đỏ/xanh
            if (!isSilent) {
                if (!isValid) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            }

            if (!isValid) allValid = false;
        });
        
        // 2. Kiểm tra Select địa chỉ
        const province = $('#province').val();
        const district = $('#district').val();
        const ward = $('#ward').val();
        
        if (!province || !district || !ward) {
            allValid = false;
        }
        
        // 3. Cập nhật nút Submit
        const btn = document.getElementById('btnPlaceOrder');
        // Nút chỉ sáng khi: Form Valid VÀ (Có giỏ hàng HOẶC Đang mua ngay)
        if (allValid && (cart?.items?.length > 0 || buyNowMode)) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
        
        return allValid;
    }

    // === 2. KHỞI TẠO PAGE ===
    $(document).ready(function() {
        console.log('🚀 Ready...');
        
        // Load Tỉnh/Thành
        $.getJSON('https://provinces.open-api.vn/api/?depth=1', function(data) {
            $.each(data, function(k, v) {
                $('#province').append(`<option value="${v.code}" data-name="${v.name}">${v.name}</option>`);
            });
            initLocationEvents(); 
        }).fail(function() {
            console.error('Lỗi API địa chỉ');
        });

        // SỰ KIỆN VALIDATION:
        // - Blur (rời chuột): Kiểm tra và hiện lỗi đỏ nếu sai
        $('#checkoutForm .form-control.required').on('blur', function() {
            validateCheckoutForm(false); 
        });
        // - Input (đang gõ): Kiểm tra ngầm để mở nút submit, xóa lỗi đỏ nếu user đang sửa
        $('#checkoutForm .form-control.required').on('input', function() {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid'); // Xóa đỏ ngay khi gõ lại
            }
            validateCheckoutForm(true); // Check ngầm
        });
        
        checkBuyNowMode();
        if (buyNowMode) {
            loadBuyNowProduct();
        } else {
            loadNormalCart();
        }
    });

    // === 3. XỬ LÝ ĐỊA CHỈ (PROVINCE API) ===
    function initLocationEvents() {
        $('#province').on('change', function() {
            const code = $(this).val();
            const name = $(this).find('option:selected').data('name');
            
            // Reset
            $('#district').html('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
            $('#ward').html('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            $('#province_text').val(name || ''); // Update text hidden field if needed logic
            
            if (code) {
                $.getJSON(`https://provinces.open-api.vn/api/p/${code}?depth=2`, function(data) {
                    if (data.districts) {
                        $.each(data.districts, function(k, v) {
                            $('#district').append(`<option value="${v.code}" data-name="${v.name}">${v.name}</option>`);
                        });
                        $('#district').prop('disabled', false);
                    }
                });
                $('#location-badge').text(`Đang chọn: ${name}`);
            }
            validateCheckoutForm(true); // Check ngầm khi đổi địa chỉ
        });

        $('#district').on('change', function() {
            const code = $(this).val();
            $('#ward').html('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            
            if (code) {
                $.getJSON(`https://provinces.open-api.vn/api/d/${code}?depth=2`, function(data) {
                    if (data.wards) {
                        $.each(data.wards, function(k, v) {
                            $('#ward').append(`<option value="${v.code}" data-name="${v.name}">${v.name}</option>`);
                        });
                        $('#ward').prop('disabled', false);
                    }
                });
            }
            validateCheckoutForm(true);
        });

        $('#ward').on('change', function() {
            const name = $(this).find('option:selected').data('name');
            if(name) {
                const p = $('#province option:selected').data('name');
                const d = $('#district option:selected').data('name');
                $('#location-badge')
                    .removeClass('text-danger')
                    .addClass('text-success')
                    .html(`<i class="fas fa-check-circle"></i> Giao đến: ${name}, ${d}, ${p}`);
            }
            validateCheckoutForm(true);
        });
    }

    // === 4. LOGIC GIỎ HÀNG / MUA NGAY ===
    function checkBuyNowMode() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('buy_now') === '1') {
            buyNowMode = true;
            buyNowInfo = {
                sku_id: parseInt(urlParams.get('sku_id')),
                quantity: parseInt(urlParams.get('quantity')) || 1,
                product_id: parseInt(urlParams.get('product_id')),
                product_name: decodeURIComponent(urlParams.get('product_name') || ''),
                price: parseFloat(urlParams.get('price')) || 0,
                size: decodeURIComponent(urlParams.get('size_name') || ''),
                color: decodeURIComponent(urlParams.get('color_name') || '')
            };
            $('h2').text('THANH TOÁN MUA NGAY');
        }
    }

    function updateUI() {
        $('#subtotalText').text(vnd(subtotal));
        $('#shippingText').text(vnd(shippingFee));
        if (discountAmount > 0) {
            $('#discountRow').show();
            $('#discountText').text('-' + vnd(discountAmount));
        } else {
            $('#discountRow').hide();
        }
        $('#final-total').text(vnd(subtotal + shippingFee - discountAmount));
    }

    function loadBuyNowProduct() {
        const total = buyNowInfo.price * buyNowInfo.quantity;
        subtotal = total;
        $('#orderCount').text(1);
        $('#orderItems').html(`
            <div class="product-item">
                <div class="flex-grow-1">
                    <div class="product-name">${buyNowInfo.product_name} <span class="text-muted">x${buyNowInfo.quantity}</span></div>
                    <div class="product-meta small text-secondary">
                        ${buyNowInfo.size ? 'Size: ' + buyNowInfo.size : ''} ${buyNowInfo.color ? '| Màu: ' + buyNowInfo.color : ''}
                    </div>
                </div>
                <div class="product-price">${vnd(total)}</div>
            </div>
        `);
        updateUI();
        setTimeout(() => validateCheckoutForm(true), 500); // Check ngầm khi load xong
    }

    async function loadNormalCart() {
        try {
            const res = await fetch(`${API_BASE}/api/gio-hang`, { credentials: 'include' });
            if (res.status === 401) { window.location.href = 'login.php'; return; }
            const data = await res.json();
            
            if (data.ok) {
                cart = data;
                subtotal = Number(data.tam_tinh || 0);
                $('#orderCount').text(data.items.length);
                $('#orderItems').html(data.items.map(it => `
                    <div class="product-item">
                        <div class="flex-grow-1">
                            <div class="product-name">${it.ten_san_pham} <span class="text-muted">x${it.so_luong}</span></div>
                            <div class="product-meta small text-secondary">${it.ten_kich_co || ''} ${it.ten_mau ? '| ' + it.ten_mau : ''}</div>
                        </div>
                        <div class="product-price">${vnd(it.thanh_tien)}</div>
                    </div>
                `).join(''));
                updateUI();
            }
        } catch(e) { console.error(e); }
        setTimeout(() => validateCheckoutForm(true), 500); // Check ngầm khi load xong
    }

    // === 5. SỔ ĐỊA CHỈ (Đã khôi phục) ===
    $('#modalAddressBook').on('show.bs.modal', async function () {
        const wrap = document.getElementById('addressBookList');
        wrap.innerHTML = '<div class="p-5 text-center text-muted">Đang tải...</div>';
        try {
            const res = await fetch(`${API_BASE}/api/dia-chi`, { credentials: 'include' });
            const list = await res.json();

            if (!list || list.length === 0) {
                wrap.innerHTML = '<div class="p-5 text-center">Bạn chưa lưu địa chỉ nào.</div>';
                return;
            }

            wrap.innerHTML = list.map(a => `
                <div class="list-group-item list-group-item-action btn-select-address pointer" 
                   style="cursor:pointer;"
                   data-full="${a.ten_nguoi_nhan}" data-phone="${a.so_dien_thoai}"
                   data-tinh="${a.tinh_thanh}" data-quan="${a.quan_huyen}"
                   data-xa="${a.phuong_xa}" data-detail="${a.dia_chi_cu_the}">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 fw-bold">${a.ten_nguoi_nhan} ${a.mac_dinh ? '<span class="badge bg-secondary">Mặc định</span>' : ''}</h6>
                    </div>
                    <p class="mb-1 small">${a.dia_chi_cu_the}, ${a.phuong_xa}, ${a.quan_huyen}, ${a.tinh_thanh}</p>
                    <small class="text-muted">SĐT: ${a.so_dien_thoai}</small>
                </div>
            `).join('');

            // Gắn sự kiện click chọn địa chỉ
            $('.btn-select-address').on('click', function() {
                const d = $(this).data();
                $('[name="fullname"]').val(d.full);
                $('[name="phone"]').val(d.phone);
                $('[name="address"]').val(d.detail);
                
                // Điền Tỉnh -> Quận -> Huyện tự động
                fillLocationByText(d.tinh, d.quan, d.xa);
                
                // Đóng modal
                bootstrap.Modal.getInstance(document.getElementById('modalAddressBook')).hide();
            });
        } catch (e) {
            wrap.innerHTML = '<div class="p-4 text-center text-danger">Lỗi tải sổ địa chỉ</div>';
        }
    });

    // Hàm điền địa chỉ tự động (Đệ quy đợi load xong từng cấp)
    function fillLocationByText(tinh, quan, xa) {
        // 1. Tìm và chọn Tỉnh
        const pOpt = $(`#province option`).filter(function() { return $(this).text().trim() === tinh.trim(); });
        if (pOpt.length) {
            $('#province').val(pOpt.val()).trigger('change');
            
            // Đợi 1 chút để API Quận load xong
            setTimeout(() => {
                // 2. Tìm và chọn Quận
                const dOpt = $(`#district option`).filter(function() { return $(this).text().trim() === quan.trim(); });
                if (dOpt.length) {
                    $('#district').val(dOpt.val()).trigger('change');
                    
                    // Đợi 1 chút để API Xã load xong
                    setTimeout(() => {
                        // 3. Tìm và chọn Xã
                        const wOpt = $(`#ward option`).filter(function() { return $(this).text().trim() === xa.trim(); });
                        if (wOpt.length) {
                            $('#ward').val(wOpt.val()).trigger('change');
                        }
                        validateCheckoutForm(false); // Validate lại lần cuối (hiện xanh)
                    }, 800);
                }
            }, 800);
        }
    }

    // === 6. MÃ GIẢM GIÁ ===
    document.getElementById('applyDiscount').addEventListener('click', async function() {
        const code = (document.getElementById('discountInput').value || '').trim().toUpperCase();
        if (!code) { alert('Vui lòng nhập mã'); return; }
        
        const btn = this;
        btn.disabled = true;
        btn.innerText = 'Checking...';
        
        try {
            const res = await fetch('check_voucher.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: code, total: subtotal })
            });
            const data = await res.json();
            
            if (data.status) {
                discountAmount = Number(data.discount);
                discountCodeApplied = data.code;
                alert('Áp dụng thành công!');
                updateUI();
            } else {
                alert(data.message);
                discountAmount = 0;
                discountCodeApplied = '';
                updateUI();
            }
        } catch (e) { alert('Lỗi kiểm tra voucher'); }
        finally { btn.disabled = false; btn.innerText = 'Áp dụng'; }
    });

    // === 7. SUBMIT FORM ===
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate lần cuối (isSilent = false để hiện lỗi đỏ nếu có)
        if (!validateCheckoutForm(false)) {
            const errorInput = document.querySelector('.is-invalid');
            if (errorInput) errorInput.focus();
            else alert('Vui lòng điền đầy đủ thông tin giao hàng!');
            return;
        }

        const btn = document.getElementById('btnPlaceOrder');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

        const payload = {
            nguoi_nhan: $('input[name="fullname"]').val().trim(),
            sdt_nguoi_nhan: $('input[name="phone"]').val().trim(),
            dia_chi_giao_hang: `${$('input[name="address"]').val().trim()}, ${$('#ward option:selected').text()}, ${$('#district option:selected').text()}, ${$('#province option:selected').text()}`,
            ghi_chu: $('textarea[name="note"]').val().trim(),
            phi_van_chuyen: shippingFee,
            giam_gia: discountAmount,
            ma_khuyen_mai: discountCodeApplied
        };

        let endpoint = `${API_BASE}/api/don-hang`;
        if (buyNowMode) {
            endpoint = `${API_BASE}/api/don-hang/buy-now`;
            payload.sku_id = buyNowInfo.sku_id;
            payload.so_luong = buyNowInfo.quantity;
        }

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.ok) {
                $('#checkoutForm').hide();
                $('#alertSuccess').html(`
                    <div class="text-center py-5">
                        <h3 class="fw-bold text-success mb-3"><i class="fas fa-check-circle"></i> Đặt hàng thành công!</h3>
                        <p>Mã đơn hàng: <strong>${data.ma_don_hang}</strong></p>
                        <p>Tổng tiền: <strong>${vnd(data.tong_tien)}</strong></p>
                        <div class="mt-4">
                            <a href="shop.php" class="btn btn-dark rounded-pill px-4">Tiếp tục mua sắm</a>
                        </div>
                    </div>
                `).show();
                window.scrollTo(0,0);
            } else {
                alert(data.error || 'Lỗi đặt hàng');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (err) {
            console.error(err);
            alert('Lỗi kết nối server');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
<?php include 'footer.php'; ?>
