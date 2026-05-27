<?php
// profile/orders.php - Trang quản lý đơn hàng
if (session_status() === PHP_SESSION_NONE) session_start();

$API_BASE = 'http://localhost/PTUD_Final/public';
$cookie = session_name() . '=' . session_id();
session_write_close();

// Lấy thông tin user
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
                <div class="profile-card p-0 overflow-hidden">
                    <div class="header-tabs px-3 px-md-4 pt-2">
                        <div class="tabs-container" id="orderTabs">
                            <button class="tab-button active" data-tab="all">Tất cả</button>
                            <button class="tab-button" data-tab="CHO_XU_LY">Chờ xử lý</button>
                            <button class="tab-button" data-tab="DANG_XU_LY">Đang giao</button>
                            <button class="tab-button" data-tab="HOAN_TAT">Hoàn thành</button>
                            <button class="tab-button" data-tab="HUY">Đã hủy</button>
                            <button class="tab-button" data-tab="YEU_CAU_HUY">Yêu cầu hủy</button>
                        </div>
                    </div>

                    <div class="p-3 p-md-4">
                        <div class="search-box mb-4">
                            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="search-input" id="orderSearchInput" placeholder="Tìm theo Mã đơn hàng hoặc Tên sản phẩm...">
                        </div>

                        <div id="ordersLoading" class="text-center py-5">
                            <div class="spinner-border text-dark" role="status"></div>
                            <p class="mt-2 text-muted">Đang tải đơn hàng...</p>
                        </div>

                        <div id="ordersContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function formatPrice(price) {
        return parseInt(price).toLocaleString('vi-VN') + '₫';
    }

    function getImageUrl(dbUrl) {
        console.log('[DEBUG] Order image URL:', dbUrl);
        
        if (!dbUrl || dbUrl.trim() === '') {
            return 'https://placehold.co/60x60?text=No+Image';
        }
        
        dbUrl = dbUrl.trim();
        
        // 1. Nếu đã là URL đầy đủ (http/https), giữ nguyên
        if (dbUrl.startsWith('http://') || dbUrl.startsWith('https://')) {
            return dbUrl;
        }
        
        // 2. Nếu là số (ID từ bảng anh_san_pham), dùng placeholder
        if (/^\d+$/.test(dbUrl)) {
            return 'https://placehold.co/60x60?text=ID-' + dbUrl;
        }
        
        // 3. Chuẩn hóa đường dẫn tương đối từ API
        // API trả về: "PTUD_Final\/images\/hoodie\/hoodie-zip-street\/trang.png"
        // Cần chuyển thành: "http://localhost/PTUD_Final/images/hoodie/hoodie-zip-street/trang.png"
        
        // Xử lý backslashes escape từ JSON
        let cleanUrl = dbUrl.replace(/\\\//g, '/');
        
        // Loại bỏ "PTUD_Final/" nếu có ở đầu
        cleanUrl = cleanUrl.replace(/^PTUD_Final\//i, '');
        
        // Đảm bảo có dấu / ở đầu
        if (!cleanUrl.startsWith('/')) {
            cleanUrl = '/' + cleanUrl;
        }
        
        // Tạo URL đầy đủ
        const fullUrl = 'http://localhost/PTUD_Final' + cleanUrl;
        console.log('[DEBUG] Full image URL:', fullUrl);
        return fullUrl;
    }

    let allOrders = []; 
    let currentTab = 'all';
    let searchQuery = '';

    async function loadOrders() {
        const container = document.getElementById('ordersContainer');
        const loader = document.getElementById('ordersLoading');
        
        loader.style.display = 'block';
        container.innerHTML = '';

        try {
            const response = await fetch('<?php echo $API_BASE; ?>/api/don-hang', {
                credentials: 'include'
            });
            const result = await response.json();
            allOrders = result.items || [];
            loader.style.display = 'none';
            renderOrders();
        } catch (error) {
            console.error('Lỗi tải đơn hàng:', error);
            loader.style.display = 'none';
            container.innerHTML = `<div class="alert alert-danger text-center">Không thể tải lịch sử đơn hàng.</div>`;
        }
    }

    // Hàm gửi yêu cầu hủy
    async function requestCancel(orderId) {
        const reason = prompt("Vui lòng nhập lý do bạn muốn hủy đơn hàng này:");
        if (reason === null) return; // User ấn Cancel
        if (reason.trim() === "") {
            alert("Bạn cần nhập lý do để hủy đơn!");
            return;
        }

        try {
            const res = await fetch(`<?php echo $API_BASE; ?>/api/don-hang/${orderId}/huy`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ly_do: reason }),
                credentials: 'include'
            });
            const data = await res.json();

            if (data.ok) {
                alert("Đã gửi yêu cầu hủy đơn hàng. Vui lòng chờ quản trị viên xét duyệt.");
                loadOrders(); // Tải lại danh sách
            } else {
                alert(data.error || "Có lỗi xảy ra.");
            }
        } catch (e) {
            alert("Lỗi kết nối: " + e.message);
        }
    }

    async function reOrder(orderId) {
        if (!confirm('Bạn muốn thêm lại các sản phẩm này vào giỏ hàng?')) return;
        // ... Logic reOrder giữ nguyên như cũ ...
        const loader = document.getElementById('ordersLoading');
        try {
            const res = await fetch(`<?php echo $API_BASE; ?>/api/don-hang/${orderId}`, { credentials: 'include' });
            const data = await res.json();

            if (!data.ok) throw new Error(data.error || 'Không lấy được thông tin đơn hàng');

            let successCount = 0;
            let errorMessages = [];

            for (const item of data.chi_tiet) {
                if (!item.sku_id) continue;
                const resCart = await fetch('<?php echo $API_BASE; ?>/api/gio-hang/them', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        sku_id: item.sku_id,
                        so_luong: item.so_luong || 1 
                    }),
                    credentials: 'include'
                });
                const dataCart = await resCart.json();
                if (resCart.ok && dataCart.ok) {
                    successCount++;
                } else {
                    errorMessages.push(`${item.ten_san_pham}: ${dataCart.error || 'Lỗi'}`);
                }
            }

            if (successCount > 0) {
                alert('Đã thêm sản phẩm vào giỏ hàng!');
                window.location.href = '../cart.php';
            } else {
                alert('Không thể thêm sản phẩm nào. ' + errorMessages.join('\n'));
            }
        } catch (e) {
            alert('Lỗi hệ thống: ' + e.message);
        }
    }

    function renderOrders() {
        const container = document.getElementById('ordersContainer');
        const statusMap = {
            'CHO_XU_LY':  { text: 'Chờ xử lý',   class: 'bg-info text-white' },
            'DANG_XU_LY': { text: 'Đang giao',  class: 'bg-primary text-white' },
            'HOAN_TAT':   { text: 'Hoàn thành',  class: 'bg-success text-white' },
            'HUY':        { text: 'Đã hủy',      class: 'bg-danger text-white' },
            'YEU_CAU_HUY':{ text: 'Đang yêu cầu hủy', class: 'bg-warning text-dark' }
        };

        const filteredOrders = allOrders.filter(order => {
            const matchesTab = (currentTab === 'all') || (order.trang_thai === currentTab);
            const searchLower = searchQuery.toLowerCase();
            const orderIdMatch = order.ma_don_hang.toLowerCase().includes(searchLower);
            const productMatch = (order.chi_tiet_don_hang || []).some(p => 
                p.ten_san_pham.toLowerCase().includes(searchLower)
            );
            return matchesTab && (orderIdMatch || productMatch);
        });

        if (filteredOrders.length === 0) {
            container.innerHTML = `<div class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào</div>`;
            return;
        }

        container.innerHTML = filteredOrders.map(order => {
            const status = statusMap[order.trang_thai] || { text: order.trang_thai, class: 'bg-secondary text-white' };
            
            const productsHTML = (order.chi_tiet_don_hang || []).map(p => `
                <div class="product-item d-flex align-items-center mb-3">
                    <!-- SỬA DÒNG NÀY: Gọi hàm getImageUrl() -->
                    <img src="${getImageUrl(p.hinh_anh)}" 
                        class="product-image rounded border" 
                        style="width:60px; height:60px; object-fit:cover;"
                        onerror="this.onerror=null; this.src='https://placehold.co/60x60?text=Ảnh+Lỗi'">
                    <div class="ms-3 flex-grow-1">
                        <div class="fw-bold small">${p.ten_san_pham}</div>
                        <div class="text-muted smaller">Số lượng: x${p.so_luong}</div>
                    </div>
                    <div class="text-end fw-bold">${formatPrice(p.don_gia)}</div>
                </div>
            `).join('');

            // Nút Mua lại (Chỉ khi hoàn tất hoặc đã hủy)
            const reorderBtn = ['HOAN_TAT', 'HUY'].includes(order.trang_thai) 
                ? `<button onclick="reOrder(${order.id})" class="btn btn-dark btn-sm">Mua lại</button>` 
                : '';

            // Nút Hủy đơn (Chỉ khi Chờ xử lý)
            const cancelBtn = (order.trang_thai === 'CHO_XU_LY')
                ? `<button onclick="requestCancel(${order.id})" class="btn btn-outline-danger btn-sm">Hủy đơn</button>`
                : '';

            return `
                <div class="order-item-card border rounded p-3 mb-4 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="fw-bold">Mã: #${order.ma_don_hang}</span>
                        <span class="badge ${status.class}">${status.text}</span>
                    </div>
                    <div class="order-body">
                        ${productsHTML}
                    </div>
                    <div class="order-footer border-top pt-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted smaller">Ngày đặt: ${new Date(order.ngay_dat).toLocaleDateString('vi-VN')}</div>
                        <div class="text-end">
                            <div class="mb-2">Tổng tiền: <span class="text-danger fw-bold fs-5">${formatPrice(order.tong_tien)}</span></div>
                            <div class="d-flex gap-2 justify-content-end">
                                ${cancelBtn}
                                <a href="order-detail.php?id=${order.id}" class="btn btn-outline-dark btn-sm">Chi tiết</a>
                                ${reorderBtn}
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentTab = btn.dataset.tab;
                renderOrders();
            });
        });

        document.getElementById('orderSearchInput')?.addEventListener('input', (e) => {
            searchQuery = e.target.value;
            renderOrders();
        });

        loadOrders();
    });
</script>
<?php include '../footer.php'; ?>