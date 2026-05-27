<?php
// profile/security.php - Trang bảo mật tài khoản
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
                <div class="profile-card">
                    <h4 class="mb-4">Bảo mật tài khoản</h4>
                    
                    <form id="changePasswordForm">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="mb-3 text-uppercase small fw-bold text-muted">Đổi mật khẩu</h6>
                            <div class="row g-3">
                                <div class="col-lg-4 col-12">
                                    <label class="form-label">Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="currentPassword"
                                               name="current_password"
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('currentPassword')">
                                            <i class="bi bi-eye" id="currentPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 col-12">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="newPassword"
                                               name="new_password"
                                               required 
                                               minlength="8">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('newPassword')">
                                            <i class="bi bi-eye" id="newPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                                </div>
                                
                                <div class="col-lg-4 col-12">
                                    <label class="form-label">Xác nhận mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirmPassword"
                                               name="confirm_password"
                                               required 
                                               minlength="8">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('confirmPassword')">
                                            <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-dark px-4 py-2 w-100 w-md-auto">
                                <i class="bi bi-shield-check me-2"></i>Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="mb-3 text-uppercase small fw-bold text-muted">Lời khuyên bảo mật</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-shield-check text-success fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Sử dụng mật khẩu mạnh</h6>
                                        <small class="text-muted">Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-arrow-repeat text-primary fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Thay đổi định kỳ</h6>
                                        <small class="text-muted">Đổi mật khẩu mỗi 3-6 tháng để bảo mật tốt hơn</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-key text-warning fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Không dùng lại mật khẩu</h6>
                                        <small class="text-muted">Mỗi tài khoản nên có mật khẩu riêng biệt</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Bảo mật thông tin</h6>
                                        <small class="text-muted">Không chia sẻ mật khẩu với bất kỳ ai</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<script>
// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + 'Icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Handle form submission
document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validate
    if (newPassword.length < 8) { // Đã sửa thành 8
        alert('Mật khẩu mới phải có ít nhất 8 ký tự');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alert('Mật khẩu xác nhận không khớp');
        return;
    }
    
    if (currentPassword === newPassword) {
        alert('Mật khẩu mới phải khác mật khẩu hiện tại');
        return;
    }
    
    // Prepare data
    const data = {
        current_password: currentPassword,
        new_password: newPassword,
        confirm_password: confirmPassword
    };
    
    try {
        const response = await fetch('<?php echo $API_BASE; ?>/api/nguoi-dung/doi-mat-khau', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            credentials: 'include'
        });
        
        // Xử lý text trước để an toàn
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (err) {
            console.error('Server response invalid:', text);
            alert('Lỗi hệ thống: Server không trả về JSON.');
            return;
        }
        
        if (response.ok && result.ok) {
            alert('Đổi mật khẩu thành công!');
            this.reset();
        } else {
            alert('Lỗi: ' + (result.message || 'Không thể đổi mật khẩu'));
        }
    } catch (error) {
        console.error('Error changing password:', error);
        alert('Có lỗi xảy ra khi đổi mật khẩu');
    }
});
</script>

<?php include '../footer.php'; ?>