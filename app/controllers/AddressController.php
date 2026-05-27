<?php
require_once __DIR__ . '/../models/Address.php';

class AddressController {
    
    // API: GET /api/dia-chi
    public function index() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['nguoi_dung_id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Chưa đăng nhập']);
        return;
    }

    // PHẢI LÀ layTheoUser (vì trong Model Address.php bạn đặt tên là layTheoUser)
    $list = Address::layTheoUser($_SESSION['nguoi_dung_id']); 
    
    header('Content-Type: application/json');
    echo json_encode($list); 
}
    // API: POST /api/dia-chi
    public function store() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['nguoi_dung_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        // Lấy dữ liệu JSON gửi lên
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate cơ bản
        if (empty($input['ten_nguoi_nhan']) || empty($input['so_dien_thoai']) || empty($input['tinh_thanh'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Thiếu dữ liệu bắt buộc']);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        $result = Address::taoMoi($userId, $input);

        if ($result) {
            echo json_encode(['ok' => true, 'message' => 'Thêm địa chỉ thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Lỗi lưu database']);
        }
    }

    // API: DELETE /api/dia-chi/{id}
    public function delete($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['nguoi_dung_id'])) {
            http_response_code(401);
            return;
        }

        $result = Address::xoa($id, $_SESSION['nguoi_dung_id']);
        echo json_encode(['ok' => $result]);
    }

    // BỔ SUNG: API: PUT /api/dia-chi/{id}
    public function update($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['nguoi_dung_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['ten_nguoi_nhan']) || empty($input['so_dien_thoai']) || empty($input['tinh_thanh'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Thiếu dữ liệu bắt buộc']);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        $result = Address::capNhat($id, $userId, $input);

        if ($result) {
            echo json_encode(['ok' => true, 'message' => 'Cập nhật địa chỉ thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Lỗi lưu database hoặc không tìm thấy địa chỉ']);
        }
    }
    
}