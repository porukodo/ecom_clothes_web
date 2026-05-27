<?php
// profile/sidebar.php

// Kiểm tra session và user data
if (!isset($user)) {
    // Fallback nếu chưa có biến $user (tránh lỗi crash trang)
    $user = ['ho_ten' => 'Khách', 'email' => ''];
}

// Xác định trang hiện tại để active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sticky-sidebar">
    <div class="profile-tab p-0">
        <div class="p-4 text-center border-bottom bg-white">
            <h5 class="mb-1 text-truncate px-2 fw-bold text-dark">
                <?php echo htmlspecialchars($user['ho_ten'] ?? 'Người dùng'); ?>
            </h5>
            <small class="text-muted d-block text-truncate px-2">
                <?php echo htmlspecialchars($user['email'] ?? ''); ?>
            </small>
        </div>

        <button class="btn btn-light w-100 d-lg-none py-3 px-3 fw-bold d-flex justify-content-between align-items-center border-bottom" 
                type="button" 
                onclick="document.getElementById('profileMenu').classList.toggle('show')">
            <span><i class="bi bi-list me-2"></i> Menu Tài khoản</span>
            <i class="bi bi-chevron-down"></i>
        </button>

        <div id="profileMenu" class="profile-sidebar-content d-lg-block">
            <ul class="nav flex-column mb-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'info.php' || $current_page == 'userprofile.php') ? 'active' : ''; ?>" 
                       href="info.php" 
                       onclick="closeMenuOnMobile()">
                        <i class="bi bi-person"></i> Thông tin tài khoản
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>" 
                       href="orders.php" 
                       onclick="closeMenuOnMobile()">
                        <i class="bi bi-receipt"></i> Đơn hàng của tôi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'address.php') ? 'active' : ''; ?>" 
                       href="address.php" 
                       onclick="closeMenuOnMobile()">
                        <i class="bi bi-geo-alt"></i> Sổ địa chỉ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'security.php') ? 'active' : ''; ?>" 
                       href="security.php" 
                       onclick="closeMenuOnMobile()">
                        <i class="bi bi-shield-lock"></i> Bảo mật
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger border-bottom-0" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Hàm đóng menu trên mobile
    function closeMenuOnMobile() {
        if (window.innerWidth < 992) {
            document.getElementById('profileMenu').classList.remove('show');
        }
    }
</script>