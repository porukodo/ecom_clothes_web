<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'header.php'; 

// --- KẾT NỐI DATABASE ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PTUD_Final";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ====== THÊM HÀM CHUẨN HÓA ẢNH ======
function normalizeImagePath($path) {
    if (empty($path) || $path === null) {
        return 'https://placehold.co/400x600?text=No+Image';
    }
    
    $path = trim($path);
    
    // 1. Nếu đã là URL đầy đủ (http/https), giữ nguyên
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // 2. Nếu là số (ID từ bảng anh_san_pham), dùng placeholder
    if (is_numeric($path)) {
        return 'https://placehold.co/400x600?text=ID-' . $path;
    }
    
    // 3. Xử lý backslashes escape từ JSON/database
    $path = str_replace('\\', '/', $path);
    
    // 4. Loại bỏ "PTUD_Final/" nếu có ở đầu (sẽ thêm lại sau)
    $path = preg_replace('/^PTUD_Final\//i', '', $path);
    
    // 5. Đảm bảo có / ở đầu
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    
    // 6. Tạo URL đầy đủ cho XAMPP
    return 'http://localhost/PTUD_Final' . $path;
}

// 1. TRUY VẤN BANNER (Lấy banner đang hiện mới nhất)
$sql_banner = "SELECT * FROM banners WHERE status = 1 ORDER BY id DESC LIMIT 1";
$result_banner = $conn->query($sql_banner);
$banner = $result_banner->fetch_assoc();

// Thiết lập giá trị mặc định với đường dẫn đã chuẩn hóa
if ($banner && !empty($banner['image'])) {
    $banner_img = normalizeImagePath($banner['image']);
} else {
    // Dùng banner mặc định với đường dẫn đầy đủ
    $banner_img = 'http://localhost/PTUD_Final/images/banner1.jpg';
}
$banner_content = $banner ? $banner['content'] : '';

// 2. TRUY VẤN HÀNG MỚI VỀ
$sql_new = "SELECT sp.*, COALESCE(asp.url_anh, sp.anh_dai_dien_url) as anh_url
            FROM san_pham sp 
            LEFT JOIN anh_san_pham asp ON asp.id = CAST(sp.anh_dai_dien_url AS UNSIGNED)
            WHERE sp.trang_thai = 'DANG_BAN' 
            ORDER BY sp.tao_luc DESC 
            LIMIT 4";
$result_new = $conn->query($sql_new);

// 3. TRUY VẤN SẢN PHẨM BÁN CHẠY
$sql_bestseller = "SELECT sp.*, 
                          COALESCE(SUM(ctdh.so_luong), 0) as tong_ban,
                          COALESCE(asp.url_anh, sp.anh_dai_dien_url) as anh_url
                   FROM san_pham sp 
                   LEFT JOIN chi_tiet_don_hang ctdh ON sp.id = ctdh.san_pham_id 
                   LEFT JOIN anh_san_pham asp ON asp.id = CAST(sp.anh_dai_dien_url AS UNSIGNED)
                   WHERE sp.trang_thai = 'DANG_BAN' 
                   GROUP BY sp.id 
                   ORDER BY tong_ban DESC, sp.id ASC 
                   LIMIT 4";
$result_bestseller = $conn->query($sql_bestseller);
?>

<style>
    /* Banner chính */
    .hero-banner {
        background-position: top center;
        background-size: cover;
        background-repeat: no-repeat;
        border-radius: 12px;
        padding: 180px 20px;
        position: relative;
        overflow: hidden;
        color: white;
    }
    /* Lớp phủ tối */
    .hero-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    /* Nội dung banner */
    .banner-content {
        position: relative;
        z-index: 2;
    }
    .hero-banner .btn {
        transition: transform 0.3s;
    }
    .hero-banner .btn:hover {
        transform: scale(1.1);
    }

    /* Card Danh mục */
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .category-icon { font-size: 2rem; margin-bottom: 10px; }

    /* Product Card */
    .product-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .product-image-container {
        height: 280px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .product-image-container img {
        transition: transform 0.5s ease;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-card:hover .product-image-container img { transform: scale(1.08); }
    
    /* Tags */
    .tag-label {
        position: absolute;
        top: 10px; left: 10px;
        color: white;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 20px;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .bg-red { background-color: #dc3545; }
    .bg-green { background-color: #198754; }

    /* Value Props */
    .value-icon { font-size: 2.5rem; margin-bottom: 15px; color: #212529; }
</style>

<main> 
    <section class="py-4">
        <div class="container">
            <div class="hero-banner text-center" style="background-image: url('<?php echo $banner_img; ?>');">
                <div class="banner-content">
                    <button class="btn btn-light px-5 py-3 rounded-pill fw-bold text-uppercase" onclick="goAllProducts()">Mua ngay</button>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 justify-content-center">
                <div class="col" onclick="goCategory('1')">
                    <div class="card category-card border-0 shadow-sm h-100 py-3 text-center text-dark">
                        <div class="category-icon"><i class="fa-solid fa-shirt"></i></div>
                        <div class="fw-bold">ÁO THUN</div>
                    </div>
                </div>
                <div class="col" onclick="goCategory('3')">
                    <div class="card category-card border-0 shadow-sm h-100 py-3 text-center text-dark">
                      <div class="category-icon"><i class="fa-solid fa-person-walking"></i></div>
                        <div class="fw-bold">QUẦN</div>
                    </div>
                </div>
                <div class="col" onclick="goCategory('6')">
                    <div class="card category-card border-0 shadow-sm h-100 py-3 text-center text-dark">
                        <div class="category-icon"><i class="fa-solid fa-glasses"></i></div>
                        <div class="fw-bold">PHỤ KIỆN</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold text-uppercase mb-5">Hàng mới về</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                <?php
                if ($result_new->num_rows > 0) {
                    while($row = $result_new->fetch_assoc()) {
                        ?>
                        <div class="col">
                            <a href="productdetail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                                <div class="card product-card border-0 shadow-sm h-100">
                                    <div class="product-image-container">
                                        <div class="tag-label bg-green">MỚI</div>
                                        <img src="<?php echo normalizeImagePath($row['anh_url']); ?>" alt="<?php echo $row['ten_san_pham']; ?>">
                                    </div>
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title mb-1 text-truncate"><?php echo $row['ten_san_pham']; ?></h6>
                                        <div class="fw-bold"><?php echo number_format($row['gia_ban'], 0, ',', '.'); ?>đ</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center w-100'>Chưa có sản phẩm mới.</p>";
                }
                ?>
            </div>
            <div class="text-center mt-5">
                <button class="btn btn-outline-dark rounded-pill px-5 py-2" onclick="goAllProducts()">Xem tất cả</button>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold text-uppercase mb-5">Sản phẩm bán chạy</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                <?php
                if ($result_bestseller->num_rows > 0) {
                    $rank = 1;
                    while($row = $result_bestseller->fetch_assoc()) {
                        ?>
                        <div class="col">
                            <a href="productdetail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                                <div class="card product-card border-0 shadow-sm h-100">
                                    <div class="product-image-container">
                                        <div class="tag-label bg-red">TOP <?php echo $rank; ?></div>
                                        <img src="<?php echo normalizeImagePath($row['anh_url']); ?>" alt="<?php echo $row['ten_san_pham']; ?>">
                                    </div>
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title mb-1 text-truncate"><?php echo $row['ten_san_pham']; ?></h6>
                                        <div class="fw-bold"><?php echo number_format($row['gia_ban'], 0, ',', '.'); ?>đ</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                        $rank++;
                    }
                } else {
                    echo "<p class='text-center w-100'>Đang cập nhật dữ liệu bán chạy.</p>";
                }
                ?>
            </div>
            <div class="text-center mt-5">
                <button class="btn btn-outline-dark rounded-pill px-5 py-2" onclick="goAllProducts()">Xem thêm</button>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Tại sao chọn chúng tôi?</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 text-center">
                <div class="col">
                    <div class="p-3 h-100">
                        <div class="value-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <h5 class="fw-bold">Giao hàng nhanh</h5>
                        <p class="text-muted small">Miễn phí vận chuyển cho đơn hàng từ 500.000đ</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 h-100">
                        <div class="value-icon"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                        <h5 class="fw-bold">Đổi trả dễ dàng</h5>
                        <p class="text-muted small">Đổi trả sản phẩm trong vòng 30 ngày</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 h-100">
                        <div class="value-icon"><i class="fa-solid fa-credit-card"></i></div>
                        <h5 class="fw-bold">Thanh toán an toàn</h5>
                        <p class="text-muted small">Bảo mật thông tin khách hàng tuyệt đối</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 h-100">
                        <div class="value-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                        <h5 class="fw-bold">Hỗ trợ 24/7</h5>
                        <p class="text-muted small">Đội ngũ chăm sóc khách hàng luôn sẵn sàng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    function goAllProducts() {
        window.location.href = "shop.php";
    }

    function goCategory(categoryId) {
        window.location.href = "shop.php?danh_muc_id=" + encodeURIComponent(categoryId);
    }
</script>

<?php 
$conn->close();
include 'footer.php'; 
?>