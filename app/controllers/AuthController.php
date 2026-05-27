<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/PhienDangNhap.php';

class AuthController {

    private function validateEmail(string $email): bool {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function dangKy(): void {
        $body = doc_json_body();
        $email = trim((string)($body['email'] ?? ''));
        $mat_khau = (string)($body['mat_khau'] ?? '');
        $ho_ten = trim((string)($body['ho_ten'] ?? ''));
        $so_dien_thoai = trim((string)($body['so_dien_thoai'] ?? ''));
        $ngay_sinh = trim((string)($body['ngay_sinh'] ?? ''));

        // Kiểm tra cơ bản
        if ($email === '' || !$this->validateEmail($email)) {
            json(['error' => 'Email không hợp lệ'], 400);
        }
        
        if (strlen($mat_khau) < 8) {
            json(['error' => 'Mật khẩu phải có tối thiểu 8 ký tự'], 400);
        }
        
        if ($ngay_sinh === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngay_sinh)) {
            json(['error' => 'Ngày sinh không hợp lệ'], 400);
        }

        if (User::timTheoEmail($email)) {
            json(['error' => 'Email đã tồn tại'], 409);
        }

        try {
            // Gọi hàm taoMoi đã có validation
            $id = User::taoMoi([
                'email' => $email,
                'mat_khau_bam' => password_hash($mat_khau, PASSWORD_BCRYPT),
                'ho_ten' => $ho_ten !== '' ? $ho_ten : null,
                'so_dien_thoai' => $so_dien_thoai !== '' ? $so_dien_thoai : null,
                'ngay_sinh' => $ngay_sinh,
                'vai_tro' => 'NGUOI_DUNG',
                'trang_thai' => 'HOAT_DONG', // SỬA: 'trang_thai' không phải 'trang_thoai'
            ]);

            $_SESSION['nguoi_dung_id'] = $id;
            json(['ok' => true, 'message' => 'Đăng ký thành công', 'nguoi_dung_id' => $id], 201);
            
        } catch (Exception $e) {
            // Xử lý lỗi validation từ model
            $errorData = json_decode($e->getMessage(), true);
            if ($errorData && isset($errorData['errors'])) {
                // Lấy lỗi đầu tiên để hiển thị
                $firstError = reset($errorData['errors']);
                json(['error' => $firstError], 400);
            } else {
                json(['error' => 'Đăng ký thất bại'], 400);
            }
        }
    }

    public function dangNhap(): void {
        $body = doc_json_body();
        $email = trim((string)($body['email'] ?? ''));
        $mat_khau = (string)($body['mat_khau'] ?? '');

        if ($email === '' || $mat_khau === '') json(['error' => 'Vui lòng nhập email và mật khẩu'], 400);

        $user = User::timTheoEmail($email);
        if (!$user) json(['error' => 'Email hoặc mật khẩu không đúng'], 401);
        if ($user['trang_thai'] !== 'HOAT_DONG') json(['error' => 'Tài khoản đang bị khóa'], 403);
        if (!password_verify($mat_khau, $user['mat_khau_bam'])) json(['error' => 'Email hoặc mật khẩu không đúng'], 401);
        
        session_regenerate_id(true);

        $_SESSION['nguoi_dung_id'] = (int)$user['id'];
        User::capNhatLanDangNhapGanNhat((int)$user['id']);

        $token = PhienDangNhap::tao((int)$user['id']);
        $_SESSION['phien_token'] = $token;

        json([
            'ok' => true, 
            'message' => 'Đăng nhập thành công',
            'nguoi_dung' => [
                'id' => (int)$user['id'],
                'email' => $user['email'],
                'ho_ten' => $user['ho_ten'],
                'vai_tro' => $user['vai_tro'],
            ]
        ]);
    }

    public function dangXuat(): void {
        $token = (string)($_SESSION['phien_token'] ?? '');
        if ($token !== '') {
            PhienDangNhap::thuHoi($token);
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        json(['ok' => true, 'message' => 'Đăng xuất thành công']);
    }

    public function me(): void {
        $id = (int)($_SESSION['nguoi_dung_id'] ?? 0);
        if ($id <= 0) { json(['ok' => false, 'authenticated' => false], 200); return; }

        $pdo = Database::pdo();
        $stm = $pdo->prepare("SELECT id, email, ho_ten, vai_tro, trang_thai, ngay_sinh, so_dien_thoai FROM nguoi_dung WHERE id = :id LIMIT 1");
        $stm->execute(['id' => $id]);
        $u = $stm->fetch();
        
        if (!$u) { json(['ok' => false, 'authenticated' => false], 200); return; }

        json(['ok' => true, 'authenticated' => true, 'nguoi_dung' => $u]);
    }

    public function capNhat() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['nguoi_dung_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['ho_ten']) || empty($input['so_dien_thoai'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Thiếu thông tin bắt buộc']);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        $success = User::capNhatThongTin($userId, [
            'ho_ten' => $input['ho_ten'],
            'ngay_sinh' => $input['ngay_sinh'] ?? null,
            'so_dien_thoai' => $input['so_dien_thoai']
        ]);

        if ($success) {
            echo json_encode(['ok' => true, 'message' => 'Cập nhật thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Lỗi database']);
        }
    }   

    public function doiMatKhau() {
        // [QUAN TRỌNG] Đặt header JSON để Frontend nhận diện đúng
        header('Content-Type: application/json');

        // 1. Check đăng nhập
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['nguoi_dung_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        $input = json_decode(file_get_contents('php://input'), true);

        $currentPass = $input['current_password'] ?? '';
        $newPass = $input['new_password'] ?? '';
        $confirmPass = $input['confirm_password'] ?? '';

        // 2. Validate dữ liệu đầu vào
        if (empty($currentPass) || empty($newPass)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        // Kiểm tra độ dài: Phải khớp với frontend (8 ký tự)
        if (strlen($newPass) < 8) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Mật khẩu mới phải từ 8 ký tự trở lên']);
            return;
        }

        if ($newPass !== $confirmPass) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Mật khẩu xác nhận không khớp']);
            return;
        }

        // 3. Lấy thông tin user từ DB để check mật khẩu cũ
        $user = User::timTheoId($userId);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'Không tìm thấy tài khoản']);
            return;
        }

        // 4. So sánh mật khẩu hiện tại
        if (!password_verify($currentPass, $user['mat_khau_bam'])) {
            http_response_code(400); 
            echo json_encode(['ok' => false, 'message' => 'Mật khẩu hiện tại không đúng']);
            return;
        }

        // 5. Cập nhật mật khẩu mới (đã hash)
        $newHash = password_hash($newPass, PASSWORD_BCRYPT);
        $success = User::capNhatMatKhau($userId, $newHash);

        if ($success) {
            echo json_encode(['ok' => true, 'message' => 'Đổi mật khẩu thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Lỗi hệ thống, không thể cập nhật']);
        }
    }
}