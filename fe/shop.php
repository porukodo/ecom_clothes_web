<?php 
  $current_page = "Cửa hàng"; 
  include 'header.php'; 

  // helper lấy GET an toàn
  function g($k){ return $_GET[$k] ?? null; }
  $dm = g('danh_muc_id');
  $gt = g('gia_tu');
  $gd = g('gia_den');
  $sx = g('sap_xep');
  $kc = g('kich_co_id');
?>

<style>
  .sidebar-card { background:#fff; border-radius:16px; box-shadow:0 8px 25px rgba(0,0,0,0.08); }
  .filter-section { margin-bottom:1.8rem; border-bottom:1px solid #f0f0f0; padding-bottom:1.8rem; }
  .filter-section:last-child { border-bottom:none; }
  .filter-title { font-size:1rem; font-weight:700; color:#222; margin-bottom:1.2rem; text-transform:uppercase; letter-spacing:.5px; display:flex; justify-content:space-between; align-items:center; }
  .clear-filter { font-size:.9rem; font-weight:500; color:#777; text-decoration:none; }
  .clear-filter:hover { color:#000; }
  .filter-link { font-size:.95rem; color:#555; transition:all .2s; text-decoration:none; display:block; padding:8px 0; border-radius:8px; padding-left:12px; position:relative; }
  .filter-link:hover,.filter-link.active { color:#000; background-color:#f8f9fa; font-weight:500; }
  .filter-link.active:before { content:""; position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:16px; background-color:#000; border-radius:2px; }

  .price-filter-item { display:flex; justify-content:space-between; align-items:center; padding:10px 0; cursor:pointer; transition:all .2s; border-radius:8px; padding-left:12px; }
  .price-filter-item:hover { background-color:#f8f9fa; }
  .price-filter-item.active { background-color:#f8f9fa; font-weight:500; }
  .price-filter-item .checkmark { width:18px; height:18px; border-radius:50%; border:2px solid #ddd; display:flex; align-items:center; justify-content:center; margin-right:8px; transition:all .2s; }
  .price-filter-item.active .checkmark { border-color:#000; background-color:#000; }
  .price-filter-item.active .checkmark:after { content:""; width:8px; height:8px; border-radius:50%; background-color:#fff; }

  .btn-filter-toggle { background:#fff; border:2px solid #222; color:#222; font-weight:500; border-radius:10px; padding:10px 20px; transition:all .2s; }
  .btn-filter-toggle:hover { background:#222; color:#fff; }

  .mobile-filter-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1050; display:none; }
  .mobile-filter-sidebar { position:fixed; top:0; left:-320px; width:300px; height:100%; background:#fff; z-index:1060; padding:25px; overflow-y:auto; transition:left .3s ease; box-shadow:5px 0 25px rgba(0,0,0,0.1); }
  .mobile-filter-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; padding-bottom:15px; border-bottom:1px solid #eee; }
  .close-filter-btn { background:none; border:none; font-size:1.5rem; color:#666; cursor:pointer; }

  .btn-size { min-width:45px; border-radius:20px; }
  .btn-size.active { background:#222; color:#fff; border-color:#222; }

  .product-card { transition:all .3s ease; border-radius:16px; overflow:hidden; }
  .product-card:hover { transform:translateY(-6px); box-shadow:0 16px 35px rgba(0,0,0,0.1) !important; }
  .product-image-container { height:280px; background-color:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden; }
  .product-image-container img { transition:transform .5s ease; width:100%; height:100%; object-fit:cover; }
  .product-card:hover .product-image-container img { transform:scale(1.08); }
  .old-price { font-size:.9rem; text-decoration:line-through; color:#999; }
</style>

<main class="bg-light py-5">
  <div class="container">
    <div class="row">

      <!-- Desktop Sidebar -->
      <div class="col-lg-3 d-none d-lg-block">
        <div class="sidebar-card p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">Bộ lọc</h4>
            <a href="shop.php" class="clear-filter">Xóa tất cả</a>
          </div>

          <div class="filter-section">
            <h6 class="filter-title">Danh mục</h6>
            <ul class="list-unstyled" id="catLinksDesktop">
              <li><a href="shop.php?danh_muc_id=1" class="filter-link <?php echo ($dm=='1')?'active':''; ?>">Áo thun</a></li>
              <li><a href="shop.php?danh_muc_id=2" class="filter-link <?php echo ($dm=='2')?'active':''; ?>">Hoodie</a></li>
              <li><a href="shop.php?danh_muc_id=3" class="filter-link <?php echo ($dm=='3')?'active':''; ?>">Quần</a></li>
              <li><a href="shop.php?danh_muc_id=4" class="filter-link <?php echo ($dm=='4')?'active':''; ?>">Áo khoác</a></li>
              <li><a href="shop.php?danh_muc_id=5" class="filter-link <?php echo ($dm=='5')?'active':''; ?>">Áo sơ mi</a></li>
              <li><a href="shop.php?danh_muc_id=6" class="filter-link <?php echo ($dm=='6')?'active':''; ?>">Phụ kiện</a></li>
              <li><a href="shop.php" class="filter-link <?php echo ($dm===null)?'active':''; ?>">Tất cả</a></li>
            </ul>
          </div>

          <div class="filter-section">
            <h6 class="filter-title">Khoảng giá</h6>
            <div id="priceFilters">
              <div class="price-filter-item <?php echo ($gt===null && $gd===null) ? 'active':''; ?>" data-price-range="all">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Tất cả giá</span></div>
              </div>
              <div class="price-filter-item <?php echo ($gt=='0' && $gd=='300000') ? 'active':''; ?>" data-price-range="0-300000">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Dưới 300.000đ</span></div>
              </div>
              <div class="price-filter-item <?php echo ($gt=='300000' && $gd=='500000') ? 'active':''; ?>" data-price-range="300000-500000">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>300.000đ - 500.000đ</span></div>
              </div>
              <div class="price-filter-item <?php echo ($gt=='500000' && $gd=='1000000') ? 'active':''; ?>" data-price-range="500000-1000000">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>500.000đ - 1.000.000đ</span></div>
              </div>
              <!-- <div class="price-filter-item <?php echo ($gt=='1000000' && $gd===null) ? 'active':''; ?>" data-price-range="1000000">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Trên 1.000.000đ</span></div>
              </div> -->
            </div>

            <hr class="my-4">

            <h6 class="filter-title">Kích cỡ</h6>
            <div class="d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='1')?'active':''; ?>" data-size-id="1">S</button>
              <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='2')?'active':''; ?>" data-size-id="2">M</button>
              <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='3')?'active':''; ?>" data-size-id="3">L</button>
              <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='4')?'active':''; ?>" data-size-id="4">XL</button>
            </div>
          </div>

          <div class="filter-section">
            <h6 class="filter-title">Sắp xếp</h6>
            <div id="sortFilters">
              <div class="price-filter-item <?php echo ($sx===null) ? 'active':''; ?>" data-sort="">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Mặc định</span></div>
              </div>
              <div class="price-filter-item <?php echo ($sx==='gia_tang') ? 'active':''; ?>" data-sort="gia_tang">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Giá: Thấp đến cao</span></div>
              </div>
              <div class="price-filter-item <?php echo ($sx==='gia_giam') ? 'active':''; ?>" data-sort="gia_giam">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Giá: Cao đến thấp</span></div>
              </div>
              <div class="price-filter-item <?php echo ($sx==='moi_nhat') ? 'active':''; ?>" data-sort="moi_nhat">
                <div class="d-flex align-items-center"><div class="checkmark"></div><span>Mới nhất</span></div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Mobile Filter Overlay -->
      <div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>

      <!-- Mobile Filter Sidebar -->
      <div class="mobile-filter-sidebar" id="mobileFilterSidebar">
        <div class="mobile-filter-header">
          <h4 class="fw-bold m-0">Bộ lọc</h4>
          <button class="close-filter-btn" id="closeFilterBtn">&times;</button>
        </div>

        <div class="filter-section">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="filter-title m-0">Danh mục</h6>
            <a href="shop.php" class="clear-filter">Xóa tất cả</a>
          </div>
          <ul class="list-unstyled" id="catLinksMobile">
            <li><a href="shop.php?danh_muc_id=1" class="filter-link <?php echo ($dm=='1')?'active':''; ?>">Áo thun</a></li>
            <li><a href="shop.php?danh_muc_id=2" class="filter-link <?php echo ($dm=='2')?'active':''; ?>">Hoodie</a></li>
            <li><a href="shop.php?danh_muc_id=3" class="filter-link <?php echo ($dm=='3')?'active':''; ?>">Quần</a></li>
            <li><a href="shop.php?danh_muc_id=4" class="filter-link <?php echo ($dm=='4')?'active':''; ?>">Áo khoác</a></li>
            <li><a href="shop.php?danh_muc_id=5" class="filter-link <?php echo ($dm=='5')?'active':''; ?>">Áo sơ mi</a></li>
            <li><a href="shop.php?danh_muc_id=6" class="filter-link <?php echo ($dm=='6')?'active':''; ?>">Phụ kiện</a></li>
            <li><a href="shop.php" class="filter-link <?php echo ($dm===null)?'active':''; ?>">Tất cả</a></li>
          </ul>
        </div>

        <div class="filter-section">
          <h6 class="filter-title">Khoảng giá</h6>
          <div id="mobilePriceFilters">
            <div class="price-filter-item <?php echo ($gt===null && $gd===null) ? 'active':''; ?>" data-price-range="all">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Tất cả giá</span></div>
            </div>
            <div class="price-filter-item <?php echo ($gt=='0' && $gd=='300000') ? 'active':''; ?>" data-price-range="0-300000">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Dưới 300.000đ</span></div>
            </div>
            <div class="price-filter-item <?php echo ($gt=='300000' && $gd=='500000') ? 'active':''; ?>" data-price-range="300000-500000">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>300.000đ - 500.000đ</span></div>
            </div>
            <div class="price-filter-item <?php echo ($gt=='500000' && $gd=='1000000') ? 'active':''; ?>" data-price-range="500000-1000000">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>500.000đ - 1.000.000đ</span></div>
            </div>
            <div class="price-filter-item <?php echo ($gt=='1000000' && $gd===null) ? 'active':''; ?>" data-price-range="1000000">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Trên 1.000.000đ</span></div>
            </div>
          </div>
        </div>

        <div class="filter-section">
          <h6 class="filter-title">Kích cỡ</h6>
          <div class="d-flex flex-wrap gap-2" id="mobileSizeFilters">
            <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='1')?'active':''; ?>" data-size-id="1">S</button>
            <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='2')?'active':''; ?>" data-size-id="2">M</button>
            <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='3')?'active':''; ?>" data-size-id="3">L</button>
            <button type="button" class="btn btn-outline-dark btn-sm btn-size <?php echo ($kc=='4')?'active':''; ?>" data-size-id="4">XL</button>
          </div>
        </div>

        <div class="filter-section">
          <h6 class="filter-title">Sắp xếp</h6>
          <div id="mobileSortFilters">
            <div class="price-filter-item <?php echo ($sx===null) ? 'active':''; ?>" data-sort="">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Mặc định</span></div>
            </div>
            <div class="price-filter-item <?php echo ($sx==='gia_tang') ? 'active':''; ?>" data-sort="gia_tang">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Giá: Thấp đến cao</span></div>
            </div>
            <div class="price-filter-item <?php echo ($sx==='gia_giam') ? 'active':''; ?>" data-sort="gia_giam">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Giá: Cao đến thấp</span></div>
            </div>
            <div class="price-filter-item <?php echo ($sx==='moi_nhat') ? 'active':''; ?>" data-sort="moi_nhat">
              <div class="d-flex align-items-center"><div class="checkmark"></div><span>Mới nhất</span></div>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3">
          <button class="btn btn-dark w-100 py-3" id="applyMobileFilter">Áp dụng bộ lọc</button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 fw-bold text-uppercase m-0">Tất cả sản phẩm</h1>
          <button class="btn btn-filter-toggle d-lg-none" id="openFilterBtn">
            <i class="fas fa-filter me-2"></i>Bộ lọc
          </button>
        </div>

        <div id="productGrid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4"></div>

        <div class="d-flex justify-content-between align-items-center mt-4" id="pagingBar" style="display:none;">
          <button class="btn btn-outline-dark" id="btnPrev">Trang trước</button>
          <div class="text-muted small" id="pagingText"></div>
          <button class="btn btn-outline-dark" id="btnNext">Trang sau</button>
        </div>
      </div>

    </div>
  </div>
</main>

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

function normImgUrl(u){
    u = String(u || '').trim();
    if(!u) return 'https://placehold.co/600x800?text=No+Image';
    
    if(/^https?:\/\//i.test(u)) return u;
    
    u = u.replace(/\\\//g, '/');
    
    console.log('📸 Image debug - Original:', u); // Debug
    
    // Database có thể lưu đường dẫn sai
    // Ví dụ: "PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png"
    // Nhưng file thực tế là: "PTUD_Final/images/ao_thun/ao-thun-polo-minimal/trang.png"
    // (thư mục "ao_thun" vs "ao-thun")
    
    // Fix common path issues
    let fixedUrl = u;
    
    console.log('📸 Image debug - Fixed:', fixedUrl); // Debug
    
    if(!fixedUrl.startsWith('/')) {
        fixedUrl = '/' + fixedUrl;
    }
    
    const result = window.location.origin + fixedUrl;
    console.log('📸 Image debug - Final URL:', result); // Debug
    
    return result;
}

function renderProductCard(sp){
    const imgUrl = sp.anh_dai_dien_url || 'https://placehold.co/600x800?text=No+Image';
    const normalizedUrl = normImgUrl(imgUrl);
    const productId = sp.id;
    
    const ten = escapeHtml(sp.ten_san_pham);
    const gia = formatVND(sp.gia_ban);
    const href = `productdetail.php?id=${encodeURIComponent(productId)}`;

    return `
    <div class="col">
      <a href="${href}" class="text-decoration-none text-dark">
        <div class="card product-card h-100 border-0 shadow-sm">
          <div class="product-image-container">
            <img src="${normalizedUrl}" alt="${ten}" 
                 onerror="console.error('❌ Image load failed:', this.src, 'Product ID:', ${productId}); this.src='https://placehold.co/600x800?text=404'"
                 onload="console.log('✅ Image loaded:', this.src)">
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

// Category links: giữ các filter khác, chỉ đổi danh_muc_id
function bindCategoryLinks(containerId){
  const root = document.getElementById(containerId);
  if(!root) return;
  root.querySelectorAll('a.filter-link').forEach(a=>{
    a.addEventListener('click', (e)=>{
      e.preventDefault();
      const u = new URL(a.href);
      const dm = u.searchParams.get('danh_muc_id');
      setQs({ danh_muc_id: dm ? dm : null, trang: 1 });
    });
  });
}

function setupPriceFilters(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.querySelectorAll('.price-filter-item').forEach(item => {
    item.addEventListener('click', () => {
      container.querySelectorAll('.price-filter-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');

      const priceRange = item.getAttribute('data-price-range');
      const params = { trang: 1, gia_tu: null, gia_den: null };

      if (priceRange !== 'all') {
        if (priceRange === '1000000') {
          params.gia_tu = 1000000;
        } else {
          const [tu, den] = priceRange.split('-');
          params.gia_tu = tu;
          params.gia_den = den;
        }
      }
      setQs(params);
    });
  });
}

function setupSortFilters(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.querySelectorAll('.price-filter-item').forEach(item => {
    item.addEventListener('click', () => {
      container.querySelectorAll('.price-filter-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');

      const sortValue = item.getAttribute('data-sort');
      setQs({ trang: 1, sap_xep: sortValue ? sortValue : null });
    });
  });
}

function setupSizeButtons(rootSelector){
  document.querySelectorAll(rootSelector + ' .btn-size').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-size-id');
      const current = qs('kich_co_id');
      if (current === id) setQs({ kich_co_id: null, trang: 1 });
      else setQs({ kich_co_id: id, trang: 1 });
    });
  });
}

function setupMobileFilters() {
  const openFilterBtn = document.getElementById('openFilterBtn');
  const closeFilterBtn = document.getElementById('closeFilterBtn');
  const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
  const mobileFilterSidebar = document.getElementById('mobileFilterSidebar');
  const applyMobileFilterBtn = document.getElementById('applyMobileFilter');

  function open() {
    mobileFilterOverlay.style.display = 'block';
    mobileFilterSidebar.style.left = '0';
    document.body.style.overflow = 'hidden';
  }
  function close() {
    mobileFilterOverlay.style.display = 'none';
    mobileFilterSidebar.style.left = '-320px';
    document.body.style.overflow = 'auto';
  }

  if (openFilterBtn) openFilterBtn.addEventListener('click', open);
  if (closeFilterBtn) closeFilterBtn.addEventListener('click', close);
  if (mobileFilterOverlay) mobileFilterOverlay.addEventListener('click', close);

  if (applyMobileFilterBtn) {
    applyMobileFilterBtn.addEventListener('click', () => {
      const activePrice = document.querySelector('#mobilePriceFilters .price-filter-item.active');
      const priceRange = activePrice ? activePrice.getAttribute('data-price-range') : 'all';

      const activeSort = document.querySelector('#mobileSortFilters .price-filter-item.active');
      const sortValue = activeSort ? activeSort.getAttribute('data-sort') : '';

      const activeCat = document.querySelector('#catLinksMobile .filter-link.active');
      let danh_muc_id = null;
      if (activeCat && activeCat.href) {
        const u = new URL(activeCat.href);
        danh_muc_id = u.searchParams.get('danh_muc_id');
      }

      const activeSizeBtn = document.querySelector('#mobileSizeFilters .btn-size.active');
      const kich_co_id = activeSizeBtn ? activeSizeBtn.getAttribute('data-size-id') : null;

      const params = {
        trang: 1,
        danh_muc_id: danh_muc_id ? danh_muc_id : null,
        gia_tu: null, gia_den: null,
        sap_xep: sortValue ? sortValue : null,
        kich_co_id: kich_co_id ? kich_co_id : null
      };

      if (priceRange !== 'all') {
        if (priceRange === '1000000') params.gia_tu = 1000000;
        else {
          const [tu, den] = priceRange.split('-');
          params.gia_tu = tu;
          params.gia_den = den;
        }
      }

      close();
      setQs(params);
    });
  }
}

async function loadProducts(){
  const tu_khoa = qs('tu_khoa') || '';
  const danh_muc_id = qs('danh_muc_id');
  const kich_co_id = qs('kich_co_id');

  const trang = Number(qs('trang') || 1);
  const gioi_han = Number(qs('gioi_han') || 12);

  const gia_tu = qs('gia_tu');
  const gia_den = qs('gia_den');
  const sap_xep = qs('sap_xep');

  const url = new URL(`${API_BASE}/api/san-pham`);
  url.searchParams.set('trang', String(trang));
  url.searchParams.set('gioi_han', String(gioi_han));

  if(tu_khoa) url.searchParams.set('tu_khoa', tu_khoa);
  if(danh_muc_id) url.searchParams.set('danh_muc_id', danh_muc_id);
  if(kich_co_id) url.searchParams.set('kich_co_id', kich_co_id);

  if(gia_tu) url.searchParams.set('gia_tu', gia_tu);
  if(gia_den) url.searchParams.set('gia_den', gia_den);
  if(sap_xep) url.searchParams.set('sap_xep', sap_xep);

  const grid = document.getElementById('productGrid');
  const pagingBar = document.getElementById('pagingBar');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const pagingText = document.getElementById('pagingText');

  grid.innerHTML = `<div class="col-12 text-center text-muted py-5">
    <div class="spinner-border text-dark" role="status"></div>
    <div class="mt-2">Đang tải sản phẩm...</div>
  </div>`;

  try {
    const res = await fetch(url.toString(), { credentials: 'include' });
    const data = await res.json().catch(()=> ({}));

    if(!res.ok || !data.ok){
      grid.innerHTML = `<div class="col-12 text-center text-danger py-5">Không tải được danh sách sản phẩm</div>`;
      if(pagingBar) pagingBar.style.display = 'none';
      return;
    }

    const items = data.items || [];
    if(items.length === 0){
      grid.innerHTML = `<div class="col-12 text-center text-muted py-5">
        <i class="fas fa-box-open fa-2x mb-3"></i>
        <p class="mb-2">Không có sản phẩm phù hợp</p>
        <a href="shop.php" class="btn btn-outline-dark btn-sm">Xóa bộ lọc</a>
      </div>`;
      if(pagingBar) pagingBar.style.display = 'none';
      return;
    }

    grid.innerHTML = items.map(renderProductCard).join('');

    // paging (đọc từ response để tránh lệch)
    if(pagingBar){
      const p = data.paging || {};
      const tong_trang = Number(p.tong_trang || 1);
      const currentPage = Number(p.trang || trang);

      if (tong_trang <= 1) {
        pagingBar.style.display = 'none';
      } else {
        pagingBar.style.display = 'flex';
        pagingText.textContent = `Trang ${currentPage} / ${tong_trang} (Tổng: ${p.tong || 0})`;
        btnPrev.disabled = (currentPage <= 1);
        btnNext.disabled = (currentPage >= tong_trang);
        btnPrev.onclick = ()=> setQs({ trang: currentPage - 1 });
        btnNext.onclick = ()=> setQs({ trang: currentPage + 1 });
      }
    }
  } catch (e) {
    grid.innerHTML = `<div class="col-12 text-center text-danger py-5">Lỗi: không kết nối được API</div>`;
    if(pagingBar) pagingBar.style.display = 'none';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  bindCategoryLinks('catLinksDesktop');
  bindCategoryLinks('catLinksMobile');

  setupPriceFilters('priceFilters');
  setupPriceFilters('mobilePriceFilters');

  setupSortFilters('sortFilters');
  setupSortFilters('mobileSortFilters');

  setupSizeButtons('body');          // desktop + mobile đều dùng .btn-size
  setupMobileFilters();

  loadProducts();
});
</script>

<?php include 'footer.php'; ?>
