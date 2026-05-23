<?php 
$current_page = "Chi tiết sản phẩm";
include 'header.php'; 
?>

<style>
    .btn-check:checked + .btn-outline-dark,
    .btn-check:active + .btn-outline-dark,
    .btn-outline-dark.active,
    .btn-outline-dark:active,
    .size-option.active {
        background-color: #000 !important;
        color: #fff !important;
    }

    .thumbnail.active img {
        border: 2px solid #000;
        opacity: 1;
    }
    .thumbnail img {
        border: 2px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .color-option {
        cursor: pointer;
        transition: transform 0.2s;
    }
    .color-option:hover {
        transform: scale(1.1);
    }
    .color-option.active {
        ring: 2px solid #000;
        outline: 2px solid #000;
        outline-offset: 2px;
    }

    /* Quantity Selector Styles */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    .quantity-selector {
        width: 130px;
        border: 1px solid #ced4da;
        border-radius: 50px;
        overflow: hidden;
        padding: 3px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    .quantity-selector .btn {
        border: none !important;
        background: transparent;
        color: #333;
        width: 35px;
        height: 35px;
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
    }
    .quantity-selector input:focus {
        box-shadow: none !important;
    }
</style>

<main class="container py-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <div class="position-sticky" style="top: 2rem;">
                <div class="position-relative bg-light rounded overflow-hidden mb-3">
                    <img id="mainImage" src="" class="w-100 h-auto object-fit-cover" alt="Product Image">
                    <button class="btn btn-light rounded-circle shadow-sm position-absolute top-50 start-0 translate-middle-y ms-3" onclick="changeImage(-1)" style="width: 40px; height: 40px;">❮</button>
                    <button class="btn btn-light rounded-circle shadow-sm position-absolute top-50 end-0 translate-middle-y me-3" onclick="changeImage(1)" style="width: 40px; height: 40px;">❯</button>
                </div>
                <div class="d-flex gap-2 overflow-auto pb-2" id="thumbnails"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <h1 class="fw-bold mb-3 display-6" id="pdName">...</h1>
            
            <div class="mb-4">
                <span class="fs-2 fw-bold text-dark" id="pdPrice">...</span>
            </div>

            <div class="mb-3">
                <span id="pdStock" class="small text-muted"></span>
            </div>

            <div class="mb-4">
                <label class="fw-bold mb-2 d-block">Chọn size:</label>
                <div class="d-flex gap-2" id="pdSizes"></div>
            </div>

            <div class="mb-4">
                <label class="fw-bold mb-2 d-block">Chọn màu:</label>
                <div class="d-flex flex-wrap gap-2" id="pdColors"></div>
            </div>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Với 07 option màu sắc đa dạng</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Phá cách trong thiết kế tay nhún</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bo chun dày dặn ôm vừa phải tinh tế</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Khóa kéo custom logo độc quyền</li>
                    </ul>
                </div>
            </div>

            <div class="d-flex gap-4 mb-4 border-bottom pb-4">
                <div class="d-flex align-items-center text-muted">
                    <div class="bg-light rounded-circle p-2 me-2"><i class="fas fa-box"></i></div>
                    <span class="small">Giao hàng toàn quốc</span>
                </div>
                <div class="d-flex align-items-center text-muted">
                    <div class="bg-light rounded-circle p-2 me-2"><i class="fas fa-money-bill-wave"></i></div>
                    <span class="small">Thanh toán khi nhận</span>
                </div>
            </div>

            <div class="d-flex gap-3">
                <div class="quantity-selector">
                    <button class="btn btn-sm" type="button" onclick="changeQuantity(-1)">
                        <i class="fas fa-minus fa-xs"></i>
                    </button>
                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" 
                           oninput="handleQuantityInput(this)" onchange="handleQuantityChange(this)">
                    <button class="btn btn-sm" type="button" onclick="changeQuantity(1)">
                        <i class="fas fa-plus fa-xs"></i>
                    </button>
                </div>
                
                <button id="btnAddToCart" class="btn btn-outline-dark"
                onclick="if(!validateBeforeBuy()) return; handleAddToCart();">THÊM VÀO GIỎ</button>

                <button id="btnBuyNow" class="btn btn-dark flex-grow-1 fw-bold py-2">MUA NGAY</button>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-dark fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">Thông tin sản phẩm</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-muted fw-bold" id="care-tab" data-bs-toggle="tab" data-bs-target="#care-pane" type="button" role="tab">Hướng dẫn bảo quản</button>
                </li>
            </ul>

            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="info-pane" role="tabpanel">
                    <div id="pdThongTinSP"></div>
                </div>
                <div class="tab-pane fade" id="care-pane" role="tabpanel">
                    <div id="pdBaoQuan"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const API_BASE = 'http://localhost/ecom_clothes_web/public';
    const MAX_BUY_PER_SKU = 10;

    function qs(name){ return new URLSearchParams(window.location.search).get(name); }
    function formatVND(n){ return Number(n||0).toLocaleString('vi-VN') + '₫'; }
    function escapeHtml(s){
        return String(s ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;");
    }

    let originalImages = [];
    let images = [];
    let currentImageIndex = 0;
    
    function normImgUrl(u){
        u = String(u || '').trim();
        if(!u) return 'https://placehold.co/600x800?text=No+Image';
        
        // Nếu đã là full URL (placehold.co) thì giữ nguyên
        if(/^https?:\/\//i.test(u)) return u;
        
        // Xử lý backslashes từ JSON
        u = u.replace(/\\\//g, '/');
        
        // Database trả về: "ecom_clothes_web/images/hoodie/hoodie-zip-street/trang.png"
        // Cần chuyển thành: "http://localhost/ecom_clothes_web/images/hoodie/hoodie-zip-street/trang.png"
        
        // Loại bỏ "ecom_clothes_web/" nếu có ở đầu (vì sẽ thêm base URL)
        u = u.replace(/^ecom_clothes_web\//i, '');
        u = u.replace(/^PTUD\//i, '');
        
        // Tạo full URL
        const base = window.location.origin; // "http://localhost"
        const projectPath = '/ecom_clothes_web'; // Đường dẫn project trong localhost
        
        // Đảm bảo đường dẫn bắt đầu với /
        if(!u.startsWith('/')) {
            u = '/' + u;
        }
        
        // Kết hợp: http://localhost + /ecom_clothes_web + /images/...
        return base + projectPath + u;
    }

    
    function selectThumbnail(index){
        currentImageIndex = index;
        const main = document.getElementById('mainImage');
        if(main && images[index]) main.src = images[index];
        document.querySelectorAll('.thumbnail').forEach((t,i)=> t.classList.toggle('active', i===index));
    }
    
    function changeImage(direction){
        if(!images.length) return;
        currentImageIndex += direction;
        if(currentImageIndex < 0) currentImageIndex = images.length - 1;
        if(currentImageIndex >= images.length) currentImageIndex = 0;
        const main = document.getElementById('mainImage');
        if(main && images[currentImageIndex]) main.src = images[currentImageIndex];
        document.querySelectorAll('.thumbnail').forEach((t,i)=> t.classList.toggle('active', i===currentImageIndex));
    }

    let sp = null;
    let variants = [];
    let selectedSizeId = null;
    let selectedColorId = null;
    let sizeToColors = new Map();
    let colorToSizes = new Map();
    let variantMap = new Map();

    function buildIndexes(){
        sizeToColors = new Map();
        colorToSizes = new Map();
        variantMap = new Map();
        
        console.log('🔧 Building indexes for variants:', variants); // Debug
        
        variants.forEach((v, index) => {
            const sId = v.kich_co_id ? Number(v.kich_co_id) : null;
            const cId = v.mau_sac_id ? Number(v.mau_sac_id) : null;
            const key = `${sId||0}|${cId||0}`;
            
            console.log(`🔧 Variant ${index}: SKU=${v.ma_sku}, sizeId=${sId}, colorId=${cId}`); // Debug
            
            variantMap.set(key, v);
            
            if(sId){
                if(!sizeToColors.has(sId)) sizeToColors.set(sId, new Set());
                if(cId) {
                    sizeToColors.get(sId).add(cId);
                    console.log(`  ↪ Added color ${cId} to size ${sId}`); // Debug
                }
            }
            if(cId){
                if(!colorToSizes.has(cId)) colorToSizes.set(cId, new Set());
                if(sId) {
                    colorToSizes.get(cId).add(sId);
                    console.log(`  ↪ Added size ${sId} to color ${cId}`); // Debug
                }
            }
        });
        
        console.log('🔧 Final sizeToColors:', sizeToColors); // Debug
        console.log('🔧 Final colorToSizes:', colorToSizes); // Debug
        console.log('🔧 Total variants in map:', variantMap.size); // Debug
    }

    function getVariant(sizeId, colorId){
        return variantMap.get(`${sizeId||0}|${colorId||0}`) || null;
    }

    /* Quantity Logic */
    function getCurrentStock() {
        if (selectedSizeId !== null && selectedColorId !== null) {
            const v = getVariant(selectedSizeId, selectedColorId);
            if (v) return Number(v.so_luong_ton || 0);
        }
        return Infinity;
    }

    function pickVariantForColor(colorId){
        if(colorId === null) return null;
        
        console.log('🔍 pickVariantForColor called with colorId:', colorId);
        console.log('🔍 Current selectedSizeId:', selectedSizeId);
        
        // Nếu đang chọn size và có đúng biến thể size+color => ưu tiên
        if(selectedSizeId !== null){
            const v = getVariant(selectedSizeId, colorId);
            console.log('🔍 Looking for variant with size', selectedSizeId, 'and color', colorId, 'Found:', v);
            if(v) return v;
        }

        // Nếu chưa chọn size hoặc size không khớp => lấy đại 1 SKU bất kỳ theo màu
        const variantsByColor = variants.filter(x => Number(x.mau_sac_id) === Number(colorId));
        console.log('🔍 All variants with this color:', variantsByColor);
        
        // Ưu tiên SKU có ảnh
        const variantWithImage = variantsByColor.find(v => v.anh_url);
        console.log('🔍 Variant with image:', variantWithImage);
        
        const result = variantWithImage || variantsByColor[0] || null;
        console.log('🔍 Returning variant:', result);
        return result;
    }

function applySkuImageByColor(){
    console.log('🖼️ =========== applySkuImageByColor START ===========');
    console.log('🖼️ selectedColorId:', selectedColorId);
    console.log('🖼️ originalImages:', originalImages);
    
    if(selectedColorId === null) {
        console.log('🖼️ No color selected, resetting to original images');
        images = [...originalImages];
        currentImageIndex = 0;
        updateImageDisplay();
        return;
    }

    const v = pickVariantForColor(selectedColorId);
    console.log('🖼️ Selected variant:', v);
    
    if(!v || !v.anh_url) {
        console.log('🖼️ No variant or no image, using original images');
        images = [...originalImages];
        currentImageIndex = 0;
    } else {
        const url = normImgUrl(v.anh_url);
        console.log('🖼️ Variant image URL:', url);
        
        // TÌM INDEX CỦA ẢNH NÀY TRONG originalImages
        const imageIndexInOriginals = originalImages.findIndex(originalUrl => {
            return normImgUrl(originalUrl) === url;
        });
        
        console.log('🖼️ Image found at index in originals:', imageIndexInOriginals);
        
        if(imageIndexInOriginals !== -1) {
            // Ảnh đã tồn tại trong originalImages
            console.log('🖼️ Image exists in originals at index:', imageIndexInOriginals);
            images = [...originalImages]; // Giữ nguyên mảng
            currentImageIndex = imageIndexInOriginals; // QUAN TRỌNG: Set đúng index!
        } else {
            // Ảnh mới, thêm vào đầu
            console.log('🖼️ New image, adding to beginning');
            images = [url, ...originalImages];
            currentImageIndex = 0;
        }
    }
    
    console.log('🖼️ Final images array:', images);
    console.log('🖼️ Final currentImageIndex:', currentImageIndex); // <-- Kiểm tra cái này!
    console.log('🖼️ =========== applySkuImageByColor END ===========');
    
    updateImageDisplay();
}

    function updateImageDisplay() {
        console.log('🖼️ =========== updateImageDisplay START ===========');
        console.log('🖼️ Images array:', images);
        console.log('🖼️ Current image index:', currentImageIndex);
        
        // Cập nhật ảnh chính
        const main = document.getElementById('mainImage');
        if(main) {
            console.log('🖼️ Main image element found');
            if(images.length > 0 && images[currentImageIndex]) {
                const src = images[currentImageIndex];
                console.log('🖼️ Setting main image src to:', src);
                main.src = src;
            } else {
                console.log('🖼️ No image to display');
                main.src = 'https://placehold.co/600x800?text=No+Image';
            }
        } else {
            console.error('🖼️ Main image element not found!');
        }

        // Cập nhật thumbnails
        const wrap = document.getElementById('thumbnails');
        if(wrap){
            console.log('🖼️ Thumbnails container found');
            console.log('🖼️ Creating', images.length, 'thumbnails');
            
            wrap.innerHTML = images.map((url, idx)=>`
            <div class="thumbnail ${idx===currentImageIndex?'active':''}" onclick="selectThumbnail(${idx})" style="width:80px;height:80px;">
                <img src="${normImgUrl(url)}" class="w-100 h-100 rounded object-fit-cover">
            </div>`).join('');
        } else {
            console.error('🖼️ Thumbnails container not found!');
        }
        
        console.log('🖼️ =========== updateImageDisplay END ===========');
    }


    function handleQuantityInput(el) {
        el.value = el.value.replace(/[^0-9]/g, '');
    }

    function handleQuantityChange(el) {
        let val = parseInt(el.value, 10);
        if (isNaN(val) || val < 1) val = 1;

        const stock = getCurrentStock();
        const limit = Math.min(stock, MAX_BUY_PER_SKU);

        if (val > limit) {
            if (stock === Infinity) {
                val = MAX_BUY_PER_SKU;
                alert(`Giới hạn mua tối đa là ${MAX_BUY_PER_SKU} sản phẩm.`);
            } else {
                val = limit;
                if (stock < MAX_BUY_PER_SKU) {
                    alert(`Chỉ còn ${stock} sản phẩm trong kho.`);
                } else {
                    alert(`Giới hạn mua tối đa là ${MAX_BUY_PER_SKU} sản phẩm.`);
                }
            }
        }
        el.value = val;
    }

    function changeQuantity(delta){
        const input = document.getElementById('quantity');
        if(!input) return;
        let val = parseInt(input.value || '1', 10);
        if (isNaN(val)) val = 1;
        val += delta;
        input.value = val;
        handleQuantityChange(input);
    }

    /* Render Logic */
    function renderSizes(){
        const wrap = document.getElementById('pdSizes');
        if(!wrap) return;
        
        // Ẩn tiêu đề nếu không có size
        const label = wrap.previousElementSibling; // Thẻ label "Chọn size:"
        const sizes = Array.isArray(sp?.sizes) ? sp.sizes : [];
        
        if(sizes.length === 0) {
            if(label) label.style.display = 'none';
            wrap.innerHTML = '<span class="text-muted small">Freesize / Một kích cỡ</span>';
            return;
        }
        
        if(label) label.style.display = 'block';

        wrap.innerHTML = sizes.map(s=>{
            const id = Number(s.id);
            const active = (selectedSizeId === id) ? ' active' : '';
            return `<button class="btn btn-outline-dark px-4 py-2 size-option${active}" data-size-id="${id}" type="button">${escapeHtml(s.ten)}</button>`;
        }).join('');
        
        wrap.querySelectorAll('[data-size-id]').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const id = Number(btn.getAttribute('data-size-id'));
                if(btn.disabled) return;
                // Nếu chỉ có 1 size thì không cho bỏ chọn (UX tốt hơn)
                if(sizes.length > 1) {
                    selectedSizeId = (selectedSizeId === id) ? null : id;
                } else {
                    selectedSizeId = id; 
                }
                syncOptionStates();
                syncPriceAndStock();
            });
        });
    }

    function renderColors(){
        const wrap = document.getElementById('pdColors');
        if(!wrap) return;

        // Ẩn tiêu đề nếu không có màu
        const label = wrap.previousElementSibling; // Thẻ label "Chọn màu:"
        const colors = Array.isArray(sp?.colors) ? sp.colors : [];

        if(colors.length === 0) {
            if(label) label.style.display = 'none';
            wrap.innerHTML = ''; // Không render gì cả
            return;
        }

        if(label) label.style.display = 'block';

        wrap.innerHTML = colors.map(c=>{
            const id = Number(c.id);
            const hex = c.ma || '#ffffff';
            const active = (selectedColorId === id) ? ' active' : '';
            return `<div class="color-option rounded-circle border${active}" data-color-id="${id}"
            title="${escapeHtml(c.ten)}" style="width:35px;height:35px;background-color:${escapeHtml(hex)};" role="button" tabindex="0"></div>`;
        }).join('');
        
        wrap.querySelectorAll('[data-color-id]').forEach(div=>{
            div.addEventListener('click', ()=>{
                if(div.classList.contains('disabled')) return;
                const id = Number(div.getAttribute('data-color-id'));
                
                // Nếu chỉ có 1 màu thì không cho bỏ chọn
                if(colors.length > 1) {
                    selectedColorId = (selectedColorId === id) ? null : id;
                } else {
                    selectedColorId = id;
                }

                syncOptionStates();
                syncPriceAndStock();
                applySkuImageByColor();
            });
        });
    }

    function syncOptionStates(){
        console.log('🎨 syncOptionStates called');
        console.log('🎨 Selected size:', selectedSizeId, 'Selected color:', selectedColorId);
        
        document.querySelectorAll('.size-option').forEach(btn=>{
            const sId = Number(btn.getAttribute('data-size-id'));
            let allowed = true;
            if(selectedColorId !== null){
                const set = colorToSizes.get(selectedColorId);
                allowed = set ? set.has(sId) : false;
                console.log(`🎨 Size ${sId} allowed with color ${selectedColorId}:`, allowed, 'set:', set); // Debug
            }
            btn.disabled = !allowed;
            if(!allowed && selectedSizeId === sId) {
                console.log(`🎨 Deselecting size ${sId} because not allowed with color ${selectedColorId}`); // Debug
                selectedSizeId = null;
            }
        });

        document.querySelectorAll('.color-option').forEach(div=>{
            const cId = Number(div.getAttribute('data-color-id'));
            let allowed = true;
            if(selectedSizeId !== null){
                const set = sizeToColors.get(selectedSizeId);
                allowed = set ? set.has(cId) : false;
                console.log(`🎨 Color ${cId} allowed with size ${selectedSizeId}:`, allowed, 'set:', set); // Debug
            }
            div.classList.toggle('disabled', !allowed);
            div.style.opacity = allowed ? '1' : '0.35';
            div.style.pointerEvents = allowed ? 'auto' : 'none';
            if(!allowed && selectedColorId === cId) {
                console.log(`🎨 Deselecting color ${cId} because not allowed with size ${selectedSizeId}`); // Debug
                selectedColorId = null;
            }
        });

        // Re-check active after disabling
        document.querySelectorAll('.size-option').forEach(btn=>{
            const id = Number(btn.getAttribute('data-size-id'));
            const isActive = (selectedSizeId === id);
            btn.classList.toggle('active', isActive);
            console.log(`🎨 Size ${id} active:`, isActive); // Debug
        });
        
        document.querySelectorAll('.color-option').forEach(div=>{
            const id = Number(div.getAttribute('data-color-id'));
            const isActive = (selectedColorId === id);
            div.classList.toggle('active', isActive);
            console.log(`🎨 Color ${id} active:`, isActive); // Debug
        });
    }

    // Thay thế hàm syncPriceAndStock cũ
    function syncPriceAndStock(){
        const priceEl = document.getElementById('pdPrice');
        const stockEl = document.getElementById('pdStock');
        const qtyInput = document.getElementById('quantity');
        const addBtn = document.getElementById('btnAddToCart');
        const buyBtn = document.getElementById('btnBuyNow');

        if(!priceEl || !stockEl || !sp) return;

        const hasSizes = Array.isArray(sp?.sizes) && sp.sizes.length > 0;
        const hasColors = Array.isArray(sp?.colors) && sp.colors.length > 0;

        // Kiểm tra xem đã chọn đủ các thuộc tính CÓ SẴN chưa
        const missingSize = hasSizes && selectedSizeId === null;
        const missingColor = hasColors && selectedColorId === null;

        if(missingSize || missingColor){
            // Nếu chưa chọn đủ -> Hiển thị giá range hoặc giá gốc
            priceEl.textContent = formatVND(sp.gia_ban);
            stockEl.textContent = 'Vui lòng chọn phân loại hàng';
            stockEl.className = 'small text-muted';
            if(addBtn) addBtn.disabled = false; // Vẫn để enable để user bấm vào trigger validate alert
            if(buyBtn) buyBtn.disabled = false;
            return;
        }

        // Tìm variant
        const v = getVariant(selectedSizeId, selectedColorId);
        
        if(!v){
            stockEl.textContent = 'Biến thể không tồn tại';
            stockEl.className = 'small text-danger fw-bold';
            if(addBtn) addBtn.disabled = true;
            if(buyBtn) buyBtn.disabled = true;
            return;
        }

        // Update giá và kho
        priceEl.textContent = formatVND(v.gia_ban);
        const ton = Number(v.so_luong_ton || 0);
        const sku = v.ma_sku ? `SKU: ${v.ma_sku} • ` : '';

        if(ton <= 0){
            stockEl.textContent = `${sku}Hết hàng`;
            stockEl.className = 'small text-danger fw-bold';
        } else {
            stockEl.textContent = `${sku}Còn ${ton} sản phẩm`;
            stockEl.className = 'small text-success fw-bold';
        }

        if (qtyInput) handleQuantityChange(qtyInput);

        const disabled = (ton <= 0);
        if(addBtn) addBtn.disabled = disabled;
        if(buyBtn) buyBtn.disabled = disabled;
    }

    function handleAddToCart(){
        const v = getVariant(selectedSizeId, selectedColorId);
        if(!v){ alert('Biến thể không hợp lệ'); return; }
        const skuId = Number(v.id);
        const qty = Number(document.getElementById('quantity')?.value || 1);
        apiAddToCart({ skuId, qty });
    }

    function validateBeforeBuy(){
        // Kiểm tra xem sản phẩm có options không
        const hasSizes = Array.isArray(sp?.sizes) && sp.sizes.length > 0;
        const hasColors = Array.isArray(sp?.colors) && sp.colors.length > 0;

        // Nếu sản phẩm có list size mà chưa chọn size -> Báo lỗi
        if(hasSizes && selectedSizeId === null){
            alert('Vui lòng chọn size');
            return false;
        }

        // Nếu sản phẩm có list màu mà chưa chọn màu -> Báo lỗi
        if(hasColors && selectedColorId === null){
            alert('Vui lòng chọn màu sắc');
            return false;
        }

        // Lấy variant tương ứng (với trường hợp không màu, selectedColorId là null vẫn hợp lệ)
        const v = getVariant(selectedSizeId, selectedColorId);
        
        if(!v){
            alert('Phiên bản này hiện không khả dụng. Vui lòng chọn kết hợp khác.');
            return false;
        }

        const ton = Number(v.so_luong_ton || 0);
        const qty = Number(document.getElementById('quantity')?.value || 1);

        if(ton <= 0){
            alert('Sản phẩm này đã hết hàng');
            return false;
        }
        if(qty > ton){
            alert(`Kho chỉ còn ${ton} sản phẩm, vui lòng giảm số lượng.`);
            return false;
        }
        if(qty > MAX_BUY_PER_SKU){
            alert(`Giới hạn mua tối đa là ${MAX_BUY_PER_SKU} sản phẩm.`);
            return false;
        }
        return true;
    }

    async function apiAddToCart({ skuId, qty }) {
        const res = await fetch(`${API_BASE}/api/gio-hang/them`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sku_id: skuId, so_luong: qty })
        });
        const data = await res.json().catch(()=> ({}));
        if (res.status === 401) {
            alert('Bạn cần đăng nhập để thêm vào giỏ.');
            window.location.href = 'login.php';
            return;
        }
        if (!res.ok || !data.ok) {
            alert(data.error || 'Không thêm vào giỏ được');
            return;
        }
        alert('Đã thêm vào giỏ!');
    }

    // Thêm hàm xử lý MUA NGAY
    async function handleBuyNow() {
        if (!validateBeforeBuy()) return;
        
        const v = getVariant(selectedSizeId, selectedColorId);
        if (!v) {
            alert('Biến thể không hợp lệ');
            return;
        }
        
        const skuId = Number(v.id);
        const qty = Number(document.getElementById('quantity')?.value || 1);
        const productName = sp.ten_san_pham || 'Sản phẩm';
        const price = v.gia_ban || sp.gia_ban;
        
        // Lấy thông tin size và màu đã chọn từ variants
        const selectedSize = sp.sizes?.find(s => Number(s.id) === selectedSizeId);
        const selectedColor = sp.colors?.find(c => Number(c.id) === selectedColorId);
        
        // Hiển thị loading
        const btn = document.getElementById('btnBuyNow');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
        btn.disabled = true;
        
        try {
            // Kiểm tra đăng nhập
            const resCheck = await fetch(`${API_BASE}/api/auth/me`, { 
                credentials: 'include' 
            });
            
            if (resCheck.status === 401) {
                alert('Bạn cần đăng nhập để mua hàng.');
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Tạo URL checkout với đầy đủ thông tin SKU
            const params = new URLSearchParams({
                buy_now: '1',
                sku_id: skuId,
                quantity: qty,
                product_id: sp.id,
                product_name: encodeURIComponent(productName),
                price: price,
                sku_code: v.ma_sku || '',
                size_name: selectedSize ? encodeURIComponent(selectedSize.ten) : '',
                color_name: selectedColor ? encodeURIComponent(selectedColor.ten) : ''
            });
            
            // Chuyển hướng đến trang checkout
            window.location.href = `checkout.php?${params.toString()}`;
            
        } catch (error) {
            console.error('Lỗi khi xử lý mua ngay:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Thêm event listener cho nút MUA NGAY
    document.addEventListener('DOMContentLoaded', function() {
        const btnBuyNow = document.getElementById('btnBuyNow');
        if (btnBuyNow) {
            btnBuyNow.addEventListener('click', handleBuyNow);
        }
    });

    async function loadDetail(){
        const id = Number(qs('id') || 0);
        if(!id){
            alert('Thiếu id sản phẩm');
            window.location.href = 'shop.php';
            return;
        }
        const res = await fetch(`${API_BASE}/api/san-pham/${id}`, { credentials: 'include' });
        const data = await res.json().catch(()=> ({}));
        if(!res.ok || !data.ok){
            alert(data.error || 'Không tìm thấy sản phẩm');
            window.location.href = 'shop.php';
            return;
        }

        sp = data.san_pham;
        variants = Array.isArray(sp?.variants) ? sp.variants : [];
        document.getElementById('pdName').textContent = sp.ten_san_pham || 'Sản phẩm';
        document.getElementById('pdPrice').textContent = formatVND(sp.gia_ban);

        // Lưu ảnh gốc
        const list = [];
        if (Array.isArray(sp.anh_phu) && sp.anh_phu.length) {
            sp.anh_phu.forEach(a => {
                const u = normImgUrl(a?.url_anh);
                if (u) list.push(u);
            });
        }

        if (!list.length) {
            const u0 = normImgUrl(sp.anh_dai_dien_url);
            list.push(u0 || 'https://placehold.co/600x800?text=No+Image');
        }

        originalImages = list; // Lưu ảnh gốc
        images = [...originalImages]; // Copy sang images hiện tại

        const wrap = document.getElementById('thumbnails');
        if(wrap){
            wrap.innerHTML = images.map((url, idx)=>`
            <div class="thumbnail ${idx===0?'active':''}" onclick="selectThumbnail(${idx})" style="width:80px;height:80px;">
                <img src="${normImgUrl(url)}" class="w-100 h-100 rounded object-fit-cover">
            </div>`).join('');
        }
        document.getElementById('mainImage').src = images[0] || '';

        const infoEl = document.getElementById('pdThongTinSP');
        const careEl = document.getElementById('pdBaoQuan');

        if (infoEl) {
            const txt = sp.thong_tin_sp || sp.mo_ta || '';
            infoEl.innerHTML = txt
                ? `<div class="alert alert-light border mb-0">${escapeHtml(txt).replaceAll('\n','<br>')}</div>`
                : `<div class="text-muted">Chưa có thông tin sản phẩm.</div>`;
        }
        if (careEl) {
            const txt = String(sp.huong_dan_bao_quan ?? '').trim();
            careEl.innerHTML = txt
                ? `<div class="alert alert-light border mb-0">${escapeHtml(txt).replace(/\r?\n/g, '<br>')}</div>`
                : `<div class="text-muted">Chưa có hướng dẫn bảo quản.</div>`;
        }

        buildIndexes();
        autoPickDefaults();
        applySkuImageByColor();
        renderSizes();
        renderColors();
        syncOptionStates();
        syncPriceAndStock();
    }

    function autoPickDefaults(){
        if(!variants.length) return;
        const v0 = variants[0];
        selectedSizeId = v0.kich_co_id ? Number(v0.kich_co_id) : null;
        selectedColorId = v0.mau_sac_id ? Number(v0.mau_sac_id) : null;
    }

    loadDetail().catch(()=>{
        alert('Không kết nối được API');
        window.location.href = 'shop.php';
    });
</script>

<?php include 'footer.php'; ?>