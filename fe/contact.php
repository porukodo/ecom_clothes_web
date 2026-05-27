<?php 
    $current_page = "Liên hệ"; 
    include 'header.php'; 
?>

<style>
    .contact-wrapper {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding-top: 20px;
        padding-bottom: 80px;
    }

    .contact-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }
    .contact-hero h1 { font-weight: 700; margin-bottom: 10px; }
    .contact-hero p { opacity: 0.9; max-width: 600px; margin: 0 auto; }

    .contact-box {
        background-color: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        height: 100%; 
        display: flex;
        flex-direction: column;
    }

    .contact-title {
        color: #1a1a2e;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ff6b6b;
        display: inline-block;
        width: fit-content;
    }

    .map-container {
        border-radius: 8px;
        overflow: hidden;
        flex-grow: 1; 
        min-height: 400px;
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
    }
    .info-icon {
        background-color: #f0f5ff;
        color: #1a1a2e;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .info-content h3 {
        color: #1a1a2e;
        margin-bottom: 4px;
        font-size: 1rem;
        font-weight: 700;
    }
    .info-content p {
        color: #555;
        font-size: 0.95rem;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .business-hours {
        background-color: #fcfcfc;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        margin-top: auto; 
    }
    .business-hours h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    .business-hours h3 i { color: #ff6b6b; margin-right: 8px; }

    @media (max-width: 768px) {
        .contact-box { padding: 25px; }
        .map-container { min-height: 300px; }
    }
</style>

<div class="contact-wrapper">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            
            <div class="col-lg-6 col-md-12">
                <div class="contact-box">
                    <h2 class="contact-title">BẢN ĐỒ CỬA HÀNG</h2>
                    <div class="map-container">
                        <iframe 
                            src="https://maps.google.com/maps?q=279%20Nguy%E1%BB%85n%20Tri%20Ph%C6%B0%C6%A1ng%2C%20Ph%C6%B0%E1%BB%9Dng%205%2C%20Qu%E1%BA%ADn%2010%2C%20Th%C3%A0nh%20ph%E1%BB%91%20H%E1%BB%93%20Ch%C3%AD%20Minh&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="contact-box">
                    <h2 class="contact-title">THÔNG TIN LIÊN HỆ</h2>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <h3>Địa chỉ</h3>
                            <p>279 Nguyễn Tri Phương, Phường 5, Quận 10, TP Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-content">
                            <h3>Số điện thoại</h3>
                            <p>0912345678</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>solestudio@gmail.com</p>
                        </div>
                    </div>

                    <div class="business-hours mt-4">
                        <h3><i class="far fa-clock"></i> Giờ làm việc</h3>
                        <p class="mb-0">Từ 9:00 đến 21:30 các ngày trong tuần (T2 - CN)</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>