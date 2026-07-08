<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once dirname(__DIR__) . '/app/security/EncryptionService.php';
require_once dirname(__DIR__) . '/app/security/PiiFields.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $stmt = $pdo->prepare("SELECT id, email, mat_khau_bam, ho_ten, vai_tro FROM nguoi_dung WHERE email = ? AND vai_tro = 'QUAN_TRI' AND trang_thai = 'HOAT_DONG'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['mat_khau_bam'])) {
            $admin = EncryptionService::decryptFields($admin, PiiFields::USER);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_ho_ten'] = $admin['ho_ten'];
            $_SESSION['vai_tro'] = $admin['vai_tro'];
            
            // Cập nhật lần đăng nhập cuối
            $stmt = $pdo->prepare("UPDATE nguoi_dung SET lan_dang_nhap_gan_nhat = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);
            
            redirect('index.php');
        } else {
            $error = 'Email hoặc mật khẩu không chính xác';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0ede8;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(180,160,130,0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(150,130,110,0.12) 0%, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1px 4px rgba(0,0,0,0.06);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background-color: #1a1a1a;
            padding: 2.25rem 2rem;
            text-align: center;
        }
        .login-header .brand-mark {
            width: 44px; height: 44px;
            background: #fff;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .login-header .brand-mark i {
            color: #1a1a1a;
            font-size: 1.25rem;
        }
        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            font-size: 0.925rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(26,26,26,0.08);
        }
        .input-group-text {
            border-radius: 0.5rem 0 0 0.5rem !important;
            background: #f7f7f7;
            border-color: #ddd;
        }
        .input-group .form-control {
            border-radius: 0 0.5rem 0.5rem 0 !important;
        }
        .btn-login {
            background: #1a1a1a;
            color: #fff;
            padding: 0.825rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            letter-spacing: 0.01em;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-login:hover {
            background: #333;
            color: #fff;
            transform: translateY(-1px);
        }
        .form-body {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-mark">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h4 class="text-white fw-semibold mb-1" style="letter-spacing:-0.01em">Admin</h4>
            <p class="mb-0" style="color:rgba(255,255,255,0.45);font-size:0.8rem">Hệ thống quản trị nội bộ</p>
        </div>

        <div class="form-body">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3">
                            <i class="fas fa-envelope text-secondary"></i>
                        </span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" 
                               placeholder="admin@example.com" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3">
                            <i class="fas fa-lock text-secondary"></i>
                        </span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" 
                               placeholder="••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                </button>
            </form>
        </div><!-- /form-body -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>