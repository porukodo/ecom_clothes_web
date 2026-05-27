<?php

// Import các Controller
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/ProductController.php';
require __DIR__ . '/controllers/CartController.php';
require __DIR__ . '/controllers/OrderController.php';
require __DIR__ . '/controllers/AddressController.php';

// --- HEALTH CHECK ---
if ($method === 'GET' && $path === '/api/health') {
  json(['ok' => true, 'message' => 'Backend running']);
  exit;
}

// --- AUTHENTICATION ---
if ($method === 'POST' && $path === '/api/auth/dang-ky') { (new AuthController)->dangKy(); exit; }
if ($method === 'POST' && $path === '/api/auth/dang-nhap') { (new AuthController)->dangNhap(); exit; }
if ($method === 'POST' && $path === '/api/auth/dang-xuat') { (new AuthController)->dangXuat(); exit; }
if ($method === 'GET'  && $path === '/api/auth/me')       { (new AuthController)->me(); exit; }
if ($method === 'POST' && $path === '/api/nguoi-dung/cap-nhat') { (new AuthController)->capNhat(); exit; }
if ($method === 'POST' && str_contains($path, '/api/nguoi-dung/doi-mat-khau')) {(new AuthController)->doiMatKhau(); exit;}

// --- PRODUCTS (SẢN PHẨM) ---
if ($method === 'GET' && $path === '/api/san-pham') {
  (new ProductController)->danhSach();
  exit;
}
if ($method === 'GET' && preg_match('#^/api/san-pham/(\d+)$#', $path, $m)) {
  (new ProductController)->chiTiet((int)$m[1]);
  exit;
}

// --- CART (GIỎ HÀNG) ---
if ($method === 'GET' && $path === '/api/gio-hang') { (new CartController)->xem(); exit; }
if ($method === 'POST' && $path === '/api/gio-hang/them') { (new CartController)->them(); exit; }
if ($method === 'PATCH' && preg_match('#^/api/gio-hang/(\d+)$#', $path, $m)) { (new CartController)->capNhat((int)$m[1]); exit; }
if ($method === 'DELETE' && preg_match('#^/api/gio-hang/(\d+)$#', $path, $m)) { (new CartController)->xoa((int)$m[1]); exit; }

// --- ORDERS (ĐƠN HÀNG) ---
if ($method === 'POST' && $path === '/api/don-hang') { (new OrderController)->tao(); exit; }
if ($method === 'POST' && $path === '/api/don-hang/buy-now') { (new OrderController)->buyNow(); exit; }
if ($method === 'GET'  && $path === '/api/don-hang') { (new OrderController)->danhSach(); exit; }
if ($method === 'GET' && preg_match('#^/api/don-hang/(\d+)$#', $path, $m)) {(new OrderController)->chiTiet((int)$m[1]); exit;}
if ($method === 'GET' && preg_match('#^/api/don-hang/(\d+)/mua-lai$#', $path, $m)) {
    (new OrderController)->laySanPhamDeMuaLai((int)$m[1]);
    exit;
}
// --- MỚI: Route Yêu cầu hủy đơn hàng ---
if ($method === 'POST' && preg_match('#^/api/don-hang/(\d+)/huy$#', $path, $m)) {
    (new OrderController)->yeuCauHuy((int)$m[1]);
    exit;
}

// --- ADDRESS (SỔ ĐỊA CHỈ) ---
if ($method === 'GET' && $path === '/api/dia-chi') { 
    (new AddressController)->index(); 
    exit; 
}
if ($method === 'POST' && $path === '/api/dia-chi') { 
    (new AddressController)->store(); 
    exit; 
}

// BỔ SUNG: Route PUT để cập nhật
if ($method === 'PUT' && preg_match('#^/api/dia-chi/(\d+)$#', $path, $m)) { 
    (new AddressController)->update((int)$m[1]); 
    exit; 
}

if ($method === 'DELETE' && preg_match('#^/api/dia-chi/(\d+)$#', $path, $m)) { 
    (new AddressController)->delete((int)$m[1]); 
    exit; 
}

// --- FALLBACK (LỖI 404) ---
json(['error' => 'Not Found'], 404);