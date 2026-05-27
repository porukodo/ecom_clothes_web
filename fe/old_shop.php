<?php include 'header.php'; ?>

<style>
    /* Sidebar styling */
    .sidebar-card {
        background: #fff;
        border-radius: 12px;
    }
    
    .filter-link {
        font-size: 15px;
        color: #555;
        transition: color 0.2s;
        text-decoration: none;
        display: block;
        padding: 5px 0;
    }
    .filter-link:hover {
        color: #000;
        font-weight: 500;
    }

    /* Size buttons */
    .btn-size {
        min-width: 45px;
        border-radius: 20px;
    }

    /* Product Card Customization */
    .product-card {
        transition: all 0.3s ease;
        border-radius: 16px;
        overflow: hidden;
    }
    
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.1) !important;
    }

    .product-image-container {
        height: 280px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-image-container img {
        transition: transform 0.5s ease;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-card:hover .product-image-container img {
        transform: scale(1.08);
    }

    .old-price {
        font-size: 0.9rem;
        text-decoration: line-through;
        color: #999;
    }
</style>

<main class="bg-light py-5">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar-card p-4 shadow-sm">
                    <h4 class="fw-bold mb-4">Bộ lọc</h4>

                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase mb-3">Danh mục</h6>
                        <ul class="list-unstyled">
                            <li><a href="shop.php?danh_muc_id=1" class="filter-link">Áo thun</a></li>
                            <li><a href="shop.php?danh_muc_id=2" class="filter-link">Hoodie</a></li>
                            <li><a href="shop.php?danh_muc_id=3" class="filter-link">Quần</a></li>
                            <li><a href="shop.php?danh_muc_id=4" class="filter-link">Áo khoác</a></li>
                            <li><a href="shop.php?danh_muc_id=5" class="filter-link">Áo sơ mi</a></li>
                            <li><a href="shop.php?danh_muc_id=6" class="filter-link">Phụ kiện</a></li>
                            <li><a href="shop.php" class="filter-link">Tất cả</a></li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase mb-3">Kích cỡ</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-dark btn-sm btn-size" data-size-id="1">S</button>
                            <button class="btn btn-outline-dark btn-sm btn-size" data-size-id="2">M</button>
                            <button class="btn btn-outline-dark btn-sm btn-size" data-size-id="3">L</button>
                            <button class="btn btn-outline-dark btn-sm btn-size" data-size-id="4">XL</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold text-uppercase m-0">Tất cả sản phẩm</h1>
                    <button class="btn btn-outline-dark d-lg-none">
                        <i class="fas fa-filter me-2"></i>Bộ lọc
                    </button>
                </div>

                <div id="productGrid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4"></div>

                <div class="d-flex justify-content-between align-items-center mt-4" id="pagingBar" style="display:none;">
                    <button class="btn btn-outline-dark" id="btnPrev">Trang trước</button>
                    <div class="text-muted small" id="pagingText"></div>
                    <button class="btn btn-outline-dark" id="btnNext">Trang sau</button>
                </div> </div> </div> </div> </main>
<script>
const API_BASE = 'http://localhost/PTUD_Final/public';

function qs(name){
  return new URLSearchParams(window.location.search).get(name);
}

function setQs(params){
  const url = new URL(window.location.href);
  Object.entries(params).forEach(([k,v])=>{
    if(v === null || v === '' || typeof v === 'undefined') url.searchParams.delete(k);
    else url.searchParams.set(k, v);
  });
  window.location.href = url.toString();
}

function formatVND(n){
  // n có thể là string "349000.00"
  const num = Number(n || 0);
  return num.toLocaleString('vi-VN') + '₫';
}

function escapeHtml(s){
  return String(s ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'","&#039;");
}

function renderProductCard(sp){
  const img = sp.anh_dai_dien_url || 'https://placehold.co/600x800?text=No+Image';
  const ten = escapeHtml(sp.ten_san_pham);
  const gia = formatVND(sp.gia_ban);

  // Link sang trang chi tiết, truyền id
  const href = `productdetail.php?id=${encodeURIComponent(sp.id)}`;

  return `
    <div class="col">
      <a href="${href}" class="text-decoration-none text-dark">
        <div class="card product-card h-100 border-0 shadow-sm">
          <div class="product-image-container">
            <img src="${img}" alt="${ten}">
          </div>
          <div class="card-body p-3 text-center">
            <h6 class="card-title mb-1 fw-normal">${ten}</h6>
            <div class="fw-bold">${gia}</div>
          </div>
        </div>
      </a>
    </div>
  `;
}

document.querySelectorAll('.btn-size').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const id = btn.getAttribute('data-size-id');
    // toggle: bấm lại lần nữa để bỏ lọc
    const current = qs('kich_co_id');
    if (current === id) setQs({ kich_co_id: null, trang: 1 });
    else setQs({ kich_co_id: id, trang: 1 });
  });
});

async function loadProducts(){
  const tu_khoa = qs('tu_khoa') || '';
  const danh_muc_id = qs('danh_muc_id');
  const trang = Number(qs('trang') || 1);
  const gioi_han = Number(qs('gioi_han') || 12); // grid 4 cột -> 12 đẹp
  const kich_co_id = qs('kich_co_id');
  const url = new URL(`${API_BASE}/api/san-pham`);
  url.searchParams.set('trang', String(trang));
  url.searchParams.set('gioi_han', String(gioi_han));
  if(tu_khoa) url.searchParams.set('tu_khoa', tu_khoa);
  if(danh_muc_id) url.searchParams.set('danh_muc_id', danh_muc_id);
  if(kich_co_id) url.searchParams.set('kich_co_id', kich_co_id);

  const grid = document.getElementById('productGrid');
  const pagingBar = document.getElementById('pagingBar');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const pagingText = document.getElementById('pagingText');

  grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Đang tải sản phẩm...</div>`;

  const res = await fetch(url.toString(), { credentials: 'include' });
  const data = await res.json().catch(()=> ({}));

  if(!res.ok || !data.ok){
    grid.innerHTML = `<div class="col-12 text-center text-danger py-5">Không tải được danh sách sản phẩm</div>`;
    if(pagingBar) pagingBar.style.display = 'none';
    return;
  }

  const items = data.items || [];
  if(items.length === 0){
    grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Không có sản phẩm phù hợp</div>`;
    if(pagingBar) pagingBar.style.display = 'none';
    return;
  }

  grid.innerHTML = items.map(renderProductCard).join('');

  // Phân trang (nếu bạn giữ pagingBar)
  if(pagingBar){
    const p = data.paging || {};
    const tong_trang = Number(p.tong_trang || 1);
    pagingBar.style.display = 'flex';

    pagingText.textContent = `Trang ${p.trang || 1} / ${tong_trang} (Tổng: ${p.tong || 0})`;

    btnPrev.disabled = (trang <= 1);
    btnNext.disabled = (trang >= tong_trang);

    btnPrev.onclick = ()=> setQs({ trang: trang - 1 });
    btnNext.onclick = ()=> setQs({ trang: trang + 1 });
  }
}

loadProducts().catch(err=>{
  const grid = document.getElementById('productGrid');
  if(grid) grid.innerHTML = `<div class="col-12 text-center text-danger py-5">Lỗi: không kết nối được API</div>`;
});
</script>

<?php include 'footer.php'; ?>