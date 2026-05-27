<?php
// --- GIỮ NGUYÊN ĐƯỜNG DẪN GỐC ---
$base_url = '/PTUD_Final/fe/'; 
// -------------------------

// 1. Khởi tạo Session (Giữ nguyên)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/PTUD_Final',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// 2. Logic lấy số lượng giỏ hàng (Giữ nguyên logic SQL)
$cart_count = 0;
if (isset($_SESSION['nguoi_dung_id'])) {
    $conn_header = new mysqli("localhost", "root", "", "PTUD_Final");
    if (!$conn_header->connect_error) {
        $uid = (int)$_SESSION['nguoi_dung_id'];
        $sql_count = "SELECT SUM(ct.so_luong) as total 
                      FROM chi_tiet_gio_hang ct 
                      JOIN gio_hang gh ON ct.gio_hang_id = gh.id 
                      WHERE gh.nguoi_dung_id = $uid";
        
        $result_count = $conn_header->query($sql_count);
        if ($result_count) {
            $row_count = $result_count->fetch_assoc();
            $cart_count = (int)$row_count['total'];
        }
        $conn_header->close();
    }
}

// 3. Logic lấy từ khóa tìm kiếm (Giữ nguyên)
$search_value = isset($_GET['tu_khoa']) ? htmlspecialchars($_GET['tu_khoa']) : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sole Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar-brand { font-size: 1.8rem; font-weight: bold; }
        .nav-icons { display: flex; align-items: center; gap: 15px; }
        .nav-icons .nav-link { padding: 0.5rem; color: #333; }
        .dropdown-menu { min-width: 200px; }
        .nav-link.dropdown-toggle::after { display: none; }
        
        body { 
            padding-top: 20px;
            background-color: #f8f9fa; 
        }
        
        /* --- STYLE MỚI CHO THANH TÌM KIẾM --- */
        .search-form {
            position: relative;
            width: 100%;
            max-width: 350px; /* Độ rộng tối đa của thanh tìm kiếm */
        }
        
        .search-form input {
            border-radius: 50px; /* Bo tròn */
            padding-right: 40px; /* Chừa chỗ cho nút icon bên phải */
            padding-left: 20px;
            border: 1px solid #e1e1e1;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .search-form input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0,0,0,0.05);
            border-color: #333;
        }

        .search-form button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #666;
            transition: color 0.3s;
        }

        .search-form button:hover {
            color: #000;
        }

        /* Responsive: Trên mobile thanh tìm kiếm giãn full width */
        @media (max-width: 991px) {
            .search-form {
                max-width: 100%;
                margin: 15px 0;
            }
        }
        /* ------------------------------------ */

        .breadcrumb {
            background-color: transparent !important;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #6c757d;
            transition: color 0.3s;
        }
        .breadcrumb-item a:hover {
            color: #1a1a2e;
        }
        .breadcrumb-item.active {
            color: #ff6b6b;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">Sole Studio.</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>aboutus.php">About us</a></li>
                </ul>
                
                <form action="<?php echo $base_url; ?>shop.php" method="GET" class="search-form me-3">
                    <input type="text" name="tu_khoa" class="form-control" placeholder="Tìm sản phẩm..." value="<?php echo $search_value; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div class="nav-icons">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i>
                            
                            <?php if (isset($_SESSION['nguoi_dung_id'])): ?>
                                <span class="position-absolute start-100 translate-middle p-1 bg-success border border-light rounded-circle" style="top: 5px;">
                                    <span class="visually-hidden">Đang online</span>
                                </span>
                            <?php endif; ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if (isset($_SESSION['nguoi_dung_id'])): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>userprofile.php">
                                        <i class="fas fa-user-circle me-2"></i>Tài khoản của tôi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>login.php">
                                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>register.php">
                                        <i class="fas fa-user-plus me-2"></i>Đăng ký
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <a class="nav-link position-relative" href="<?php echo $base_url; ?>cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        
                        <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.6rem; <?php echo $cart_count > 0 ? '' : 'display: none;'; ?>">
                            <?php echo $cart_count > 99 ? '99+' : $cart_count; ?>
                            <span class="visually-hidden">sản phẩm trong giỏ</span>
                        </span>
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($current_page) && $current_page != 'Trang chủ'): ?>
    <div class="container mt-4 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_url; ?>index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo $current_page; ?>
                </li>
            </ol>
        </nav>
    </div>
    <?php endif; ?> 

    <script>
        function updateCartCount() {
            // Gọi API lấy số lượng mới
            fetch('<?php echo $base_url; ?>api_get_cart_count.php') 
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-badge');
                    if (data.status === 'success') {
                        const count = parseInt(data.count);
                        if (count > 0) {
                            badge.style.display = 'inline-block'; // Hiện badge
                            badge.innerText = count > 99 ? '99+' : count;
                        } else {
                            badge.style.display = 'none'; // Ẩn badge
                        }
                    }
                })
                .catch(error => console.error('Lỗi cập nhật giỏ hàng:', error));
        }
    </script>