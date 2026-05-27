<?php include 'header.php'; ?>

<style>
    /* Tab chuyển đổi Đăng nhập / Đăng ký */
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
    
    .login-tab-item:hover {
        color: #333;
    }

    .login-tab-item.active {
        color: #000;
        border-bottom: 2px solid #000;
        font-weight: 700;
    }

    /* Hiệu ứng báo lỗi form */
    .error-text {
        font-size: 0.85rem;
        color: #dc3545; /* Màu đỏ Bootstrap */
        margin-top: 5px;
        display: none;
    }
    .error-text.show {
        display: block;
    }

    /* Đường kẻ phân cách */
    .social-separator {
        position: relative;
        text-align: center;
        margin: 30px 0;
    }
    .social-separator::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #e0e0e0;
        z-index: 0;
    }
    .social-separator span {
        background-color: #fff;
        padding: 0 15px;
        color: #999;
        font-size: 0.9rem;
        position: relative;
        z-index: 1;
    }
</style>

<main class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="d-flex gap-4 mb-4 border-bottom">
                            <div class="login-tab-item active">Đăng nhập</div>
                            <a href="register.php" class="login-tab-item">Đăng ký</a>
                        </div>

                        <form id="loginForm" novalidate>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control bg-light border-0 py-3 required" placeholder="name@example.com">
                                <div class="error-text">Vui lòng nhập email</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control bg-light border-0 py-3 required" placeholder="Nhập mật khẩu">
                                <div class="error-text">Vui lòng nhập mật khẩu</div>
                            </div>

                            <div class="d-flex justify-content-end mb-4">
                                <a href="#" class="text-decoration-none text-muted small">Quên mật khẩu?</a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary py-3 fw-bold text-uppercase login-button" disabled>
                                    Đăng nhập
                                </button>
                            </div>

                        </form>

                        <div class="social-separator">
                            <span>hoặc đăng nhập qua</span>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-dark w-100 py-2">
                                    <i class="fab fa-facebook-f me-2"></i> Facebook
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-dark w-100 py-2">
                                    <i class="fab fa-google me-2"></i> Google
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
        </div>
    </div>
</main>

<script>
  const form = document.getElementById('loginForm');
  const inputs = document.querySelectorAll('.required');
  const button = document.querySelector('.login-button');

  // Input mapping theo form hiện tại
  const emailInput = inputs[0];
  const passInput  = inputs[1];

  // URL API (theo cấu trúc bạn đang dùng /public làm front controller)
  const API_BASE = 'http://localhost/PTUD_Final/public';

  function isValidEmail(email){
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function showError(input, msg){
    const error = input.nextElementSibling; // div.error-text
    input.classList.add('is-invalid');
    error.textContent = msg;
    error.classList.add('show');
  }

  function clearError(input){
    const error = input.nextElementSibling;
    input.classList.remove('is-invalid');
    error.classList.remove('show');
  }

  function checkInput(input){
    const v = input.value;

    if(v === ''){
      if(input === emailInput) showError(input, 'Vui lòng nhập email');
      else showError(input, 'Vui lòng nhập mật khẩu');
      return false;
    }

    // validate thêm cho email (đồng bộ chuẩn BE)
    if(input === emailInput && !isValidEmail(v.trim())){ // Email vẫn cần trim
      showError(input, 'Email không hợp lệ');
      return false;
    }

    clearError(input);
    return true;
  }

  function checkForm(){
    let valid = true;

    // Kiểm tra email (có trim) và mật khẩu (không trim)
    const emailValue = emailInput.value.trim();
    const passValue = passInput.value; // Không trim ở đây

    // Email không được trống sau khi trim
    if(emailValue === ''){
      valid = false;
    }
    
    // Mật khẩu không được trống
    if(passValue === ''){
      valid = false;
    }

    // email format
    if(valid && !isValidEmail(emailValue)) valid = false;

    button.disabled = !valid;
    if(valid){
      button.classList.remove('btn-secondary');
      button.classList.add('btn-dark');
    }else{
      button.classList.remove('btn-dark');
      button.classList.add('btn-secondary');
    }
  }

  // blur + input events
  inputs.forEach(input => {
    input.addEventListener('blur', () => {
      checkInput(input);
      checkForm();
    });

    input.addEventListener('input', () => {
      // Chỉ bỏ lỗi cho password khi đang gõ, nhưng không trim
      if(input.classList.contains('is-invalid')) clearError(input);
      checkForm();
    });
  });

  async function login(){
    const email = emailInput.value.trim(); // Email vẫn cần trim
    const mat_khau = passInput.value; // Mật khẩu KHÔNG trim

    // validate lần cuối
    let ok = true;
    
    // Kiểm tra email riêng
    if(email === ''){
      showError(emailInput, 'Vui lòng nhập email');
      ok = false;
    } else if(!isValidEmail(email)){
      showError(emailInput, 'Email không hợp lệ');
      ok = false;
    }
    
    // Kiểm tra mật khẩu (không trim)
    if(mat_khau === ''){
      showError(passInput, 'Vui lòng nhập mật khẩu');
      ok = false;
    }
    
    checkForm();
    if(!ok || button.disabled) return;

    button.disabled = true;
    button.textContent = 'ĐANG ĐĂNG NHẬP...';

    try{
      const res = await fetch(`${API_BASE}/api/auth/dang-nhap`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'include',
        body: JSON.stringify({ email, mat_khau })
      });

      const data = await res.json().catch(()=> ({}));

      if(!res.ok){
        const msg = data.error || 'Đăng nhập thất bại';

        if(res.status === 401){
          showError(passInput, msg);
        }else if(res.status === 403){
          showError(emailInput, msg);
        }else{
          alert(msg);
        }
        return;
      }

      window.location.href = 'index.php';
    }catch(err){
      alert('Không kết nối được server. Kiểm tra XAMPP và URL API.');
    }finally{
      button.textContent = 'Đăng nhập';
      checkForm();
    }
  }

  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    login();
  });

  // initial state
  checkForm();
</script>

<?php include 'footer.php'; ?>