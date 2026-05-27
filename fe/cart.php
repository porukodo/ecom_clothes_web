<?php include 'header.php'; ?>

<main class="container py-5">
    
    <div class="row">
        <div class="col-12">
            <div id="cartContent">
                </div>
        </div>
    </div>

</main>

<script>
  // 1) LOGIN FLAG
  const isLoggedIn = <?php echo isset($_SESSION['nguoi_dung_id']) ? 'true' : 'false'; ?>;

  // 2) API base
  const API_BASE = 'http://localhost/PTUD_Final/public';

  // 3) State
  let cart = null; // { gio_hang_id, items, tam_tinh }

  // ====== THÊM HÀM CHUẨN HÓA ẢNH ======
  function getImageUrl(dbUrl) {
      console.log('[DEBUG] Original image URL:', dbUrl);
      
      // 1. Nếu không có URL, trả về placeholder
      if (!dbUrl || dbUrl.trim() === '') {
          return 'https://placehold.co/80x80?text=No+Image';
      }
      
      dbUrl = dbUrl.trim();
      
      // 2. Nếu là số (ID từ bảng anh_san_pham), dùng placeholder
      if (/^\d+$/.test(dbUrl)) {
          return 'https://placehold.co/80x80?text=ID-' + dbUrl;
      }
      
      // 3. Nếu đã là URL đầy đủ (http/https), giữ nguyên
      if (dbUrl.startsWith('http://') || dbUrl.startsWith('https://')) {
          return dbUrl;
      }
      
      // 4. Chuẩn hóa đường dẫn tương đối từ database
      // Loại bỏ 'PTUD_Final/' nếu có ở đầu
      let cleanUrl = dbUrl.replace(/^PTUD_Final\//i, '');
      
      // Đảm bảo có dấu / ở đầu
      if (!cleanUrl.startsWith('/')) {
          cleanUrl = '/' + cleanUrl;
      }
      
      // Tạo URL đầy đủ: http://localhost + /PTUD_Final + /images/...
      const fullUrl = 'http://localhost/PTUD_Final' + cleanUrl;
      console.log('[DEBUG] Full image URL:', fullUrl);
      return fullUrl;
  }

  function formatCurrency(amount) {
    return Number(amount || 0).toLocaleString('vi-VN') + 'đ';
  }

  // ====== API CALLS ======
  async function loadCart() {
    const res = await fetch(`${API_BASE}/api/gio-hang`, { credentials: 'include' });
    const data = await res.json().catch(()=> ({}));

    if (res.status === 401) {
      cart = { items: [], tam_tinh: 0 };
      renderCart({ notLogged: true });
      return;
    }

    if (!res.ok || !data.ok) {
      alert(data.error || 'Không tải được giỏ hàng');
      return;
    }

    cart = data;
    renderCart({ notLogged: false });
  }

  async function apiUpdateQty(chi_tiet_id, so_luong) {
    const res = await fetch(`${API_BASE}/api/gio-hang/${chi_tiet_id}`, {
      method: 'PATCH',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ so_luong })
    });
    const data = await res.json().catch(()=> ({}));

    if (res.status === 401) {
      alert('Bạn cần đăng nhập.');
      window.location.href = 'login.php';
      return false;
    }

    if (!res.ok || !data.ok) {
      alert(data.error || 'Không cập nhật được');
      return false;
    }
    return true;
  }

  async function apiDeleteItem(chi_tiet_id) {
    const res = await fetch(`${API_BASE}/api/gio-hang/${chi_tiet_id}`, {
      method: 'DELETE',
      credentials: 'include'
    });
    const data = await res.json().catch(()=> ({}));

    if (res.status === 401) {
      alert('Bạn cần đăng nhập.');
      window.location.href = 'login.php';
      return false;
    }

    if (!res.ok || !data.ok) {
      alert(data.error || 'Không xóa được');
      return false;
    }
    return true;
  }

  // ====== UI ACTIONS ======
  async function updateQuantity(chi_tiet_id, currentQty, delta) {
    const newQty = Math.max(1, Number(currentQty) + delta);
    
    // Gọi API cập nhật
    const ok = await apiUpdateQty(chi_tiet_id, newQty);
    
    if (ok) {
        await loadCart(); // reload danh sách để tính lại tiền
        
        // --- CẬP NHẬT SỐ LƯỢNG TRÊN HEADER ---
        if(typeof updateCartCount === 'function') {
            updateCartCount();
        }
    }
  }

  async function removeItem(chi_tiet_id) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    const ok = await apiDeleteItem(chi_tiet_id);
    
    if (ok) {
        await loadCart(); // reload danh sách
        
        // --- CẬP NHẬT SỐ LƯỢNG TRÊN HEADER ---
        if(typeof updateCartCount === 'function') {
            updateCartCount();
        }
    }
  }

  function checkout() {
    if (!isLoggedIn) {
      alert("Bạn phải đăng nhập trước khi tiến hành thanh toán");
      window.location.href = 'login.php';
      return;
    }
    window.location.href = 'checkout.php';
  }

  // ====== RENDER ======
  function renderCart({ notLogged }) {
    const cartContent = document.getElementById('cartContent');
    const items = Array.isArray(cart?.items) ? cart.items : [];

    // 1) Chưa đăng nhập
    if (notLogged) {
      cartContent.innerHTML = `
        <div class="text-center py-5 bg-light rounded shadow-sm">
          <i class="fas fa-user-lock fa-4x mb-3 text-secondary"></i>
          <h3 class="text-secondary fw-bold">Bạn cần đăng nhập để xem giỏ hàng</h3>
          <a href="login.php" class="btn btn-dark mt-3 px-4 py-2 text-uppercase fw-bold">Đăng nhập</a>
        </div>
      `;
      return;
    }

    // 2) Giỏ trống
    if (items.length === 0) {
      cartContent.innerHTML = `
        <div class="text-center py-5 bg-light rounded shadow-sm">
          <i class="fas fa-shopping-cart fa-4x mb-3 text-secondary"></i>
          <h3 class="text-secondary fw-bold">Giỏ hàng của bạn đang trống</h3>
          <p class="text-muted">Hãy quay lại và chọn thêm sản phẩm yêu thích nhé!</p>
          <a href="shop.php" class="btn btn-dark mt-3 px-4 py-2 text-uppercase fw-bold">Tiếp tục mua hàng</a>
        </div>
      `;
      return;
    }

    // 3) Giỏ có items
    let cartHTML = `
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
              <thead class="bg-light">
                <tr>
                  <th class="py-3 ps-4 border-0">Sản phẩm</th>
                  <th class="py-3 border-0 text-center">Đơn giá</th>
                  <th class="py-3 border-0 text-center">Số lượng</th>
                  <th class="py-3 border-0 text-center">Thành tiền</th>
                  <th class="py-3 border-0 text-center">Xóa</th>
                </tr>
              </thead>
              <tbody>
    `;

    items.forEach(it => {
      const img = getImageUrl(it.anh_dai_dien_url);
      const name = it.ten_san_pham || 'Sản phẩm';
      const sku = it.ma_sku ? `SKU: ${it.ma_sku}` : '';
      const price = it.don_gia;
      const qty = it.so_luong;
      const subtotal = it.thanh_tien;

      cartHTML += `
        <tr>
          <td class="ps-4">
            <div class="d-flex align-items-center">
              <img src="${img}" alt="${name}" class="rounded border" style="width:80px;height:80px;object-fit:cover;">
              <div class="ms-3">
                <h6 class="mb-0 fw-bold text-dark">${name}</h6>
                <small class="text-muted d-block mt-1">${sku}</small>
              </div>
            </div>
          </td>

          <td class="text-center fw-semibold">${formatCurrency(price)}</td>

          <td class="text-center">
            <div class="quantity-selector mx-auto">
                <button class="btn btn-sm" type="button" onclick="updateQuantity(${it.chi_tiet_id}, ${qty}, -1)">
                    <i class="fas fa-minus fa-xs"></i>
                </button>
                
                <input type="number" class="form-control text-center" value="${qty}" readonly>
                
                <button class="btn btn-sm" type="button" onclick="updateQuantity(${it.chi_tiet_id}, ${qty}, 1)">
                    <i class="fas fa-plus fa-xs"></i>
                </button>
            </div>
          </td>

          <td class="text-center fw-bold text-danger fs-6">${formatCurrency(subtotal)}</td>

          <td class="text-center">
            <button class="btn btn-link text-muted p-0" onclick="removeItem(${it.chi_tiet_id})" title="Xóa">
              <i class="fas fa-trash-alt fa-lg hover-danger"></i>
            </button>
          </td>
        </tr>
      `;
    });

    cartHTML += `
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row justify-content-end">
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm border-0 bg-light">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Tạm tính:</span>
                <span class="fw-bold">${formatCurrency(cart.tam_tinh)}</span>
              </div>

              <hr>

              <div class="d-flex justify-content-between mb-4 align-items-center">
                <span class="h5 mb-0 fw-bold">Tổng cộng:</span>
                <span class="h4 mb-0 fw-bold text-danger">${formatCurrency(cart.tam_tinh)}</span>
              </div>

              <p class="small text-muted mb-4">
                <i class="fas fa-info-circle me-1"></i>Phí vận chuyển và mã giảm giá sẽ được áp dụng ở bước thanh toán.
              </p>

              <div class="d-grid gap-2">
                <button class="btn btn-dark py-2 text-uppercase fw-bold" onclick="checkout()">Tiến hành thanh toán</button>
                <a href="index.php" class="btn btn-outline-secondary py-2">Tiếp tục mua hàng</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    cartContent.innerHTML = cartHTML;
    // Trong phần render, thêm debug:
    console.log('Image URL from API:', it.anh_dai_dien_url);
    console.log('Normalized URL:', normalizeImageUrl(it.anh_dai_dien_url));

    // Và test trực tiếp URL
    const testUrl = 'http://localhost/PTUD_Final/images/hoodie/hoodie-oversize-basic/den.png';
    console.log('Testing URL:', testUrl);
  }

  // 4) load lần đầu
  loadCart();

</script>


<style>
    .hover-danger:hover {
        color: #dc3545 !important;
        transition: color 0.2s;
    }

    /* CSS cho bộ chọn số lượng (Quantity Selector) */
    .quantity-selector {
        width: 110px; /* Nhỏ hơn một chút để vừa table */
        border: 1px solid #ced4da;
        border-radius: 50px;
        overflow: hidden;
        padding: 2px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    .quantity-selector .btn {
        border: none !important;
        background: transparent;
        color: #333;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .quantity-selector .btn:hover {
        background-color: #f0f0f0;
    }

    .quantity-selector input {
        border: none !important;
        background: transparent !important;
        font-weight: 600;
        padding: 0;
        height: 100%;
        color: #000;
        /* Tắt outline khi focus */
        box-shadow: none !important; 
    }

    /* Ẩn mũi tên input number */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<?php include 'footer.php'; ?>