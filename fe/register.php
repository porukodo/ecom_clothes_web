<?php include 'header.php'; ?>

<style>
    /* Tab chuyển đổi */
    .login-tab-item {
        font-size: 1.1rem;
        color: #999;
        text-decoration: none;
        padding-bottom: 10px;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }
    .login-tab-item:hover { color: #333; }
    .login-tab-item.active {
        color: #000;
        border-bottom: 2px solid #000;
        font-weight: 700;
    }

    /* Style riêng cho Input */
    .form-control {
        background-color: #f8f9fa; /* Màu xám nhạt */
        border: 1px solid transparent;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #000;
        box-shadow: none;
    }
    
    /* Ẩn icon lịch mặc định của trình duyệt để custom (nếu muốn), hoặc để mặc định cho đơn giản */
    /* Ở đây ta dùng mặc định của Bootstrap cho ổn định */
</style>

<main class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="d-flex gap-4 mb-4 border-bottom">
                            <a href="login.php" class="login-tab-item">Đăng nhập</a>
                            <div class="login-tab-item active">Đăng ký</div>
                        </div>

                        <form id="registerForm" novalidate>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Họ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control py-3 required" name="ho" placeholder="Nguyễn">
                                    <div class="invalid-feedback">Vui lòng nhập họ</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control py-3 required" name="ten" placeholder="Văn A">
                                    <div class="invalid-feedback">Vui lòng nhập tên</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Ngày sinh <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-3 required" name="ngay_sinh" placeholder="DD/MM/YYYY" 
                                       onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
                                <div class="invalid-feedback">Vui lòng chọn ngày sinh</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control py-3 required phone-check" name="so_dien_thoai" placeholder="0912xxxxxx">
                                <div class="invalid-feedback">SĐT không hợp lệ (phải có 10 số)</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control py-3 required email-check" name="email" placeholder="name@example.com">
                                <div class="invalid-feedback">Email không đúng định dạng</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control py-3 required" name="mat_khau" placeholder="Nhập mật khẩu">
                                <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary py-3 fw-bold text-uppercase register-button" disabled>
                                    Tạo tài khoản
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
                </div>
        </div>
    </div>
</main>

<script>
    const form = document.getElementById('registerForm');
    const inputs = document.querySelectorAll('.required');
    const button = document.querySelector('.register-button');

    // === REGEX (Biểu thức chính quy) ===
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidPhone(phone) {
        // Bắt đầu bằng 0, theo sau là 9 hoặc 10 chữ số (tổng 10-11 số)
        return /^0\d{9,10}$/.test(phone);
    }

    function isValidVietnameseName(name) { return /^[A-Za-zÀ-ỹà-ỹ\s\-\\.]+$/u.test(name); }

    // === HÀM KIỂM TRA TỪNG Ô INPUT ===
    function checkInput(input) {
        const value = input.value.trim();
        let isValid = true;
        let errorMsgElement = input.nextElementSibling; // div.invalid-feedback

        // 1. Kiểm tra rỗng
        if (value === '') {
            isValid = false;
            // Nếu là ô ngày sinh thì đổi lại nội dung lỗi mặc định
            if(input.placeholder === "DD/MM/YYYY") errorMsgElement.textContent = "Vui lòng chọn ngày sinh";
            else errorMsgElement.textContent = "Trường này không được để trống";
        } 
        // 2. Kiểm tra Email
        else if (input.classList.contains('email-check') && !isValidEmail(value)) {
            isValid = false;
            errorMsgElement.textContent = "Email không đúng định dạng";
        }
        // 3. Kiểm tra Số điện thoại
        else if (input.classList.contains('phone-check') && !isValidPhone(value)) {
            isValid = false;
            errorMsgElement.textContent = "Số điện thoại không hợp lệ (10 số)";
        }
        // 4. Kiểm tra Họ và Tên
        else if ((input.name === 'ho' || input.name === 'ten') && !isValidVietnameseName(value)) {
            isValid = false;
            errorMsgElement.textContent = "Họ/tên không được chứa số hoặc ký tự đặc biệt.";
        }

        // Cập nhật giao diện (Thêm/Xóa class Bootstrap)
        if (!isValid) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
        return isValid;
    }

    // === HÀM KIỂM TRA TOÀN BỘ FORM ĐỂ BẬT NÚT ===
    function checkForm() {
        let allValid = true;
        inputs.forEach(input => {
            const value = input.value.trim();
            // Nếu rỗng -> Sai
            if (value === '') {
                allValid = false;
            }
            // Nếu là Email và sai định dạng -> Sai
            else if (input.classList.contains('email-check') && !isValidEmail(value)) {
                allValid = false;
            }
            // Nếu là Phone và sai định dạng -> Sai
            else if (input.classList.contains('phone-check') && !isValidPhone(value)) {
                allValid = false;
            }
        });

        if (allValid) {
            button.disabled = false;
            button.classList.remove('btn-secondary');
            button.classList.add('btn-dark'); // Đổi màu đen
        } else {
            button.disabled = true;
            button.classList.remove('btn-dark');
            button.classList.add('btn-secondary'); // Đổi về màu xám
        }
    }

    // === GẮN SỰ KIỆN CHO CÁC Ô INPUT ===
    inputs.forEach(input => {
        // Khi rời khỏi ô (Blur): Hiện lỗi đỏ ngay lập tức
        input.addEventListener('blur', () => {
            checkInput(input);
            checkForm();
        });

        // Khi đang gõ (Input): 
        // 1. Tắt lỗi đỏ cho đỡ khó chịu 
        // 2. Kiểm tra tổng thể để bật nút nếu đúng hết
        input.addEventListener('input', () => {
            if (input.classList.contains('is-invalid')) {
                input.classList.remove('is-invalid');
            }
            checkForm();
        });
    });

    // === XỬ LÝ KHI BẤM NÚT ĐĂNG KÝ ===
    form.addEventListener('submit', async e => {
        e.preventDefault();

        let isAllValid = true;
        inputs.forEach(input => {
            if (!checkInput(input)) isAllValid = false;
        });
        if (!isAllValid || button.disabled) return;

        // Lấy input theo đúng thứ tự form hiện tại
        const ho = inputs[0].value.trim();
        const ten = inputs[1].value.trim();
        const ngay_sinh = inputs[2].value.trim(); // type="date" => YYYY-MM-DD
        const so_dien_thoai = inputs[3].value.trim();
        const email = inputs[4].value.trim();
        const mat_khau = inputs[5].value;

        const payload = {
            ho_ten: `${ho} ${ten}`.trim(),
            ngay_sinh,
            so_dien_thoai,
            email,
            mat_khau
        };

        button.disabled = true;
        button.textContent = 'ĐANG TẠO...';

        try {
            const res = await fetch('http://localhost/PTUD_Final/public/api/auth/dang-ky', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            credentials: 'include',
            body: JSON.stringify(payload)
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
            alert(data.error || 'Đăng ký thất bại');
            button.textContent = 'TẠO TÀI KHOẢN';
            checkForm(); // bật lại nút nếu form vẫn hợp lệ
            return;
            }

            alert(data.message || 'Đăng ký thành công');
            window.location.href = 'login.php';
        } catch (err) {
            alert('Không kết nối được server. Kiểm tra XAMPP và URL API.');
        } finally {
            button.textContent = 'TẠO TÀI KHOẢN';
            checkForm();
        }
    });
</script>

<?php include 'footer.php'; ?>