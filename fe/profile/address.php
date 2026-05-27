<?php
// profile/address.php - Trang quản lý địa chỉ hoàn chỉnh
if (session_status() === PHP_SESSION_NONE) session_start();

$API_BASE = 'http://localhost/PTUD_Final/public'; // Đảm bảo URL này đúng với project của bạn
$cookie = session_name() . '=' . session_id();
session_write_close();

// --- 1. CHECK AUTH ---
$ch = curl_init($API_BASE . '/api/auth/me');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_COOKIE => $cookie,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CONNECTTIMEOUT => 3,
]);

$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($res ?: '', true);

if ($http !== 200 || !($data['ok'] ?? false) || !($data['authenticated'] ?? false)) {
    header('Location: ../login.php');
    exit();
}
// ---------------------

$user = $data['nguoi_dung'];
?>

<?php include '../header.php'; ?>
<link rel="stylesheet" href="../assets/css/profile.css">

<main class="bg-light py-4 py-lg-5">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <?php include 'sidebar.php'; ?>
            </div>
            
            <div class="col-lg-9">
                <div class="profile-card">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h4 class="mb-0">Sổ địa chỉ</h4>
                        <button class="btn btn-sm btn-dark" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addAddressModal">
                            <i class="bi bi-plus-lg me-1"></i>Thêm mới
                        </button>
                    </div>
                    
                    <div class="row g-3" id="addressList">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-dark" role="status"></div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</main>

<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
                    <input type="hidden" id="editAddressId" name="id" value="">

                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label">Tên người nhận</label>
                            <input type="text" class="form-control" name="ten_nguoi_nhan" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" name="so_dien_thoai" required>
                        </div>
                        
                        <div class="col-md-4 col-12">
                            <label class="form-label">Tỉnh/Thành phố</label>
                            <select class="form-select" name="tinh_thanh" id="provinceSelect" required>
                                <option value="">Chọn...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-12">
                            <label class="form-label">Quận/Huyện</label>
                            <select class="form-select" name="quan_huyen" id="districtSelect" required>
                                <option value="">Chọn...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-12">
                            <label class="form-label">Phường/Xã</label>
                            <select class="form-select" name="phuong_xa" id="wardSelect" required>
                                <option value="">Chọn...</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Địa chỉ cụ thể</label>
                            <input type="text" class="form-control" name="dia_chi_cu_the" placeholder="Số nhà, tên đường..." required>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="mac_dinh" id="defaultAddress">
                                <label class="form-check-label" for="defaultAddress">
                                    Đặt làm địa chỉ mặc định
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-dark" onclick="saveAddress()">Lưu địa chỉ</button>
            </div>
        </div>
    </div>
</div>

<script>
// CẤU HÌNH API
const PROVINCE_API = 'https://provinces.open-api.vn/api';
const API_BASE = '<?php echo $API_BASE; ?>';

// Biến toàn cục để lưu danh sách địa chỉ (giúp việc edit không cần gọi lại API)
let globalAddresses = [];

document.addEventListener('DOMContentLoaded', () => {
    loadAddresses();     // Load danh sách địa chỉ
    initLocationSelects(); // Khởi tạo logic select Tỉnh/Huyện/Xã
    
    // SỰ KIỆN: Khi đóng modal -> Reset form về trạng thái "Thêm mới"
    const modalEl = document.getElementById('addAddressModal');
    modalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('addressForm').reset();
        document.getElementById('editAddressId').value = ''; // Xóa ID
        document.getElementById('modalTitle').textContent = 'Thêm địa chỉ mới';
        
        // Reset các select con
        document.getElementById('districtSelect').innerHTML = '<option value="">Chọn Quận/Huyện...</option>';
        document.getElementById('wardSelect').innerHTML = '<option value="">Chọn Phường/Xã...</option>';
    });
});

// --- 1. HÀM HỖ TRỢ API HÀNH CHÍNH (Tách lẻ để tái sử dụng khi Edit) ---

// Load Quận/Huyện dựa trên Mã Tỉnh
async function fetchDistricts(provinceCode) {
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');
    
    // Reset
    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện...</option>';
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã...</option>';

    if (!provinceCode) return;

    try {
        const res = await fetch(`${PROVINCE_API}/p/${provinceCode}?depth=2`);
        const data = await res.json();
        data.districts.forEach(item => {
            const option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            option.dataset.code = item.code; // Lưu code để dùng load xã
            districtSelect.appendChild(option);
        });
    } catch (e) { console.error(e); }
}

// Load Phường/Xã dựa trên Mã Huyện
async function fetchWards(districtCode) {
    const wardSelect = document.getElementById('wardSelect');
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã...</option>';

    if (!districtCode) return;

    try {
        const res = await fetch(`${PROVINCE_API}/d/${districtCode}?depth=2`);
        const data = await res.json();
        data.wards.forEach(item => {
            const option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            wardSelect.appendChild(option);
        });
    } catch (e) { console.error(e); }
}

// Khởi tạo Select Tỉnh ban đầu
async function initLocationSelects() {
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');

    // 1. Load danh sách Tỉnh
    try {
        const res = await fetch(PROVINCE_API + '/?depth=1');
        const data = await res.json();
        provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành...</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            option.dataset.code = item.code;
            provinceSelect.appendChild(option);
        });
    } catch (e) { console.error("Lỗi load tỉnh:", e); }

    // 2. Sự kiện khi chọn Tỉnh
    provinceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        fetchDistricts(selectedOption.dataset.code);
    });

    // 3. Sự kiện khi chọn Huyện
    districtSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        fetchWards(selectedOption.dataset.code);
    });
}

// --- 2. LOGIC CRUD (Thêm, Sửa, Xóa, Xem) ---

// Load danh sách địa chỉ từ Database
async function loadAddresses() {
    const addressList = document.getElementById('addressList');
    
    try {
        const response = await fetch(`${API_BASE}/api/dia-chi`, { credentials: 'include' });
        globalAddresses = await response.json(); // Lưu dữ liệu vào biến toàn cục
        
        if (globalAddresses && globalAddresses.length > 0) {
            addressList.innerHTML = '';
            globalAddresses.forEach(address => {
                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.innerHTML = `
                    <div class="card h-100 shadow-sm border-0 bg-white">
                        <div class="card-body">
                            ${address.mac_dinh ? '<span class="badge bg-dark mb-2">Mặc định</span>' : ''}
                            <h6 class="card-title fw-bold">${address.ten_nguoi_nhan}</h6>
                            <p class="card-text small text-secondary mb-3">
                                <i class="bi bi-telephone me-1"></i> ${address.so_dien_thoai}<br>
                                <i class="bi bi-geo-alt me-1"></i> ${address.dia_chi_cu_the}, ${address.phuong_xa}, ${address.quan_huyen}, ${address.tinh_thanh}
                            </p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-dark" onclick="editAddress(${address.id})">Sửa</button>
                                ${!address.mac_dinh ? `<button class="btn btn-sm btn-outline-danger" onclick="deleteAddress(${address.id})">Xóa</button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                addressList.appendChild(col);
            });
        } else {
            addressList.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-light border text-center py-4">
                        <i class="bi bi-geo-alt fs-1 text-muted d-block mb-2"></i>
                        <p class="mb-0">Chưa có địa chỉ nào</p>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading addresses:', error);
        addressList.innerHTML = `<div class="col-12"><div class="alert alert-danger text-center">Không thể tải danh sách địa chỉ</div></div>`;
    }
}

// Xử lý khi bấm nút "Sửa"
async function editAddress(id) {
    // 1. Tìm địa chỉ trong biến toàn cục
    const addr = globalAddresses.find(a => a.id == id);
    if (!addr) return;

    // 2. Đổi tiêu đề Modal & Gán ID
    document.getElementById('modalTitle').textContent = 'Cập nhật địa chỉ';
    document.getElementById('editAddressId').value = addr.id;

    // 3. Điền thông tin cơ bản
    const form = document.getElementById('addressForm');
    form.elements['ten_nguoi_nhan'].value = addr.ten_nguoi_nhan;
    form.elements['so_dien_thoai'].value = addr.so_dien_thoai;
    form.elements['dia_chi_cu_the'].value = addr.dia_chi_cu_the;
    form.elements['mac_dinh'].checked = (addr.mac_dinh == 1);

    // 4. Xử lý Select Địa điểm (Logic Cascading: Tỉnh -> Huyện -> Xã)
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');

    // Set Tỉnh
    provinceSelect.value = addr.tinh_thanh; 

    // Tìm option Tỉnh đã chọn để lấy CODE load huyện
    const pOption = Array.from(provinceSelect.options).find(opt => opt.value === addr.tinh_thanh);
    
    if (pOption) {
        // Load Huyện và CHỜ (await) nó xong
        await fetchDistricts(pOption.dataset.code);
        
        // Sau khi load huyện xong, set giá trị Huyện
        districtSelect.value = addr.quan_huyen;

        // Tìm option Huyện đã chọn để lấy CODE load xã
        const dOption = Array.from(districtSelect.options).find(opt => opt.value === addr.quan_huyen);
        
        if (dOption) {
            // Load Xã và CHỜ nó xong
            await fetchWards(dOption.dataset.code);
            // Set giá trị Xã
            wardSelect.value = addr.phuong_xa;
        }
    }

    // 5. Hiển thị Modal
    const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();
}

// Xử lý Lưu (Thêm mới hoặc Cập nhật)
async function saveAddress() {
    const form = document.getElementById('addressForm');
    
    // Validate HTML5
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const id = document.getElementById('editAddressId').value;
    
    // Dữ liệu gửi đi
    const data = {
        ten_nguoi_nhan: formData.get('ten_nguoi_nhan'),
        so_dien_thoai: formData.get('so_dien_thoai'),
        tinh_thanh: formData.get('tinh_thanh'),
        quan_huyen: formData.get('quan_huyen'),
        phuong_xa: formData.get('phuong_xa'),
        dia_chi_cu_the: formData.get('dia_chi_cu_the'),
        mac_dinh: formData.get('mac_dinh') ? true : false
    };
    
    // Quyết định URL và Method (POST hay PUT)
    let url = `${API_BASE}/api/dia-chi`;
    let method = 'POST';
    
    if (id) {
        url = `${API_BASE}/api/dia-chi/${id}`;
        method = 'PUT'; // Quan trọng: Cần cấu hình route PUT ở Backend
    }
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            credentials: 'include'
        });
        
        const result = await response.json();
        
        if (response.ok && result.ok) {
            alert(result.message);
            
            // Đóng modal
            const modalEl = document.getElementById('addAddressModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            // Load lại danh sách
            loadAddresses();
        } else {
            alert('Lỗi: ' + (result.message || 'Không thể lưu địa chỉ'));
        }
    } catch (error) {
        console.error(error);
        alert('Có lỗi xảy ra khi kết nối server');
    }
}

// Xử lý Xóa
async function deleteAddress(id) {
    if (!confirm('Bạn có chắc muốn xóa địa chỉ này?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/api/dia-chi/${id}`, {
            method: 'DELETE',
            credentials: 'include'
        });
        
        const result = await response.json();
        
        if (response.ok && result.ok) {
            // Không cần alert nếu thích mượt mà, chỉ cần reload
            loadAddresses();
        } else {
            alert('Không thể xóa địa chỉ: ' + (result.message || 'Lỗi server'));
        }
    } catch (error) {
        console.error(error);
        alert('Có lỗi xảy ra khi xóa');
    }
}
</script>

<?php include '../footer.php'; ?>