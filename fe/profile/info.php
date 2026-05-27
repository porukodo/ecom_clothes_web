<?php
// profile/info.php - Trang thông tin cá nhân
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

$res = curl_exec($ch);
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

<style>
    .form-control.is-invalid { background-image: none; }
    .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
    .form-control.is-invalid ~ .invalid-feedback { display: block; }
</style>

<main class="bg-light py-4 py-lg-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <?php include 'sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
                <div class="profile-card">
                    <h4 class="mb-4">Thông tin cá nhân</h4>
                    <form id="profileForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-bold small text-uppercase">Họ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" name="ho" placeholder="Nguyễn" value="<?php echo htmlspecialchars(explode(' ', $user['ho_ten'])[0] ?? ''); ?>">
                                <div class="invalid-feedback">Vui lòng nhập họ</div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-bold small text-uppercase">Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" name="ten" placeholder="Văn A" value="<?php echo htmlspecialchars(implode(' ', array_slice(explode(' ', $user['ho_ten']), 1)) ?: ''); ?>">
                                <div class="invalid-feedback">Vui lòng nhập tên</div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-bold small text-uppercase">Ngày sinh <span class="text-danger">*</span></label>
                                <input type="date" class="form-control required" name="ngay_sinh" value="<?php echo htmlspecialchars($user['ngay_sinh'] ?? ''); ?>">
                                <div class="invalid-feedback">Vui lòng chọn ngày sinh</div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-bold small text-uppercase">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control required phone-check" name="so_dien_thoai" placeholder="09xxxxxxxx" value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>">
                                <div class="invalid-feedback">SĐT không hợp lệ (phải có 10 số)</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Email</label>
                                <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                                <div class="form-text text-muted">Email không thể thay đổi.</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-dark px-4 py-2 w-100 w-md-auto update-button">
                                <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('.required');
    const button = form.querySelector('.update-button');

    function isValidPhone(phone) { return /^0\d{9,10}$/.test(phone); }
    function isValidVietnameseName(name) { return /^[A-Za-zÀ-ỹà-ỹ\s\-\\.]+$/u.test(name); }

    function checkInput(input) {
        const value = input.value.trim();
        let isValid = true;
        let errorMsgElement = input.nextElementSibling;

        if (value === '') {
            isValid = false;
            errorMsgElement.textContent = "Trường này không được để trống.";
        } else if (input.classList.contains('phone-check') && !isValidPhone(value)) {
            isValid = false;
            errorMsgElement.textContent = "SĐT phải bắt đầu bằng 0 và có 10-11 chữ số.";
        } else if ((input.name === 'ho' || input.name === 'ten') && !isValidVietnameseName(value)) {
            isValid = false;
            errorMsgElement.textContent = "Họ/tên không được chứa số hoặc ký tự đặc biệt.";
        }

        if (!isValid) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
        return isValid;
    }

    inputs.forEach(input => {
        input.addEventListener('blur', () => checkInput(input));
        input.addEventListener('input', () => { 
            if (input.classList.contains('is-invalid')) {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = "Vui lòng nhập thông tin.";
            }
        });
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        let isAllValid = true;
        inputs.forEach(input => { if (!checkInput(input)) isAllValid = false; });
        if (!isAllValid) {
            const firstError = form.querySelector('.is-invalid');
            if(firstError) firstError.focus();
            return;
        }

        const formData = new FormData(this);
        const data = {
            ho_ten: (formData.get('ho') + ' ' + formData.get('ten')).trim(),
            ngay_sinh: formData.get('ngay_sinh'),
            so_dien_thoai: formData.get('so_dien_thoai')
        };

        const originalBtnText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

        try {
            const response = await fetch('<?php echo $API_BASE; ?>/api/nguoi-dung/cap-nhat', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data),
                credentials: 'include'
            });
            
            const responseText = await response.text();
            let result;
            try { result = JSON.parse(responseText); } catch (err) { throw new Error("Server lỗi: " + responseText); }
            
            if (response.ok && result.ok) {
                alert('Cập nhật thành công!');
                location.reload();
            } else {
                // Xử lý lỗi validation
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).join('\n');
                    alert("Vui lòng sửa các lỗi sau:\n" + errorMessages);
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể cập nhật'));
                }
            }
        } catch (error) {
            console.error(error);
            // Xử lý lỗi ném ra từ model (validation fail)
            if (error.message.includes('{')) {
                try {
                    const errResult = JSON.parse(error.message);
                    if (errResult.errors) {
                        const errorMessages = Object.values(errResult.errors).join('\n');
                        alert("Dữ liệu không hợp lệ:\n" + errorMessages);
                        return;
                    }
                } catch {}
            }
            alert('Có lỗi xảy ra: ' + error.message);
        } finally {
            button.disabled = false;
            button.innerHTML = originalBtnText;
        }
    });
</script>
<?php include '../footer.php'; ?>