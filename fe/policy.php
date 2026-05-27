<?php 
    // Thiết lập tiêu đề trang cho Breadcrumb trong header.php
    $current_page = "Chính sách đổi trả"; 
    include 'header.php'; 
?>

<style>
    :root {
        --brand-black: #212529;
        --brand-red: #e74c3c;
        --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    /* Policy Cards - Fixed overflow */
    .policy-card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
        background: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden; /* Ngăn nội dung tràn ra ngoài */
        word-wrap: break-word; /* Xử lý text dài */
    }

    .policy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .policy-icon-wrapper {
        width: 60px;
        height: 60px;
        background-color: rgba(33, 37, 41, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: var(--brand-black);
        font-size: 1.5rem;
        flex-shrink: 0; /* Ngăn icon bị co lại */
    }

    .section-title {
        color: var(--brand-black);
        font-weight: 700;
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 20px;
        word-break: break-word; /* Xử lý tiêu đề dài */
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--brand-red);
    }

    /* Step Process - Fixed layout */
    .step-item {
        position: relative;
        margin-bottom: 25px;
        padding: 0;
        width: 100%;
    }
    
    .step-number {
        background-color: var(--brand-black);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .step-content {
        flex: 1;
        min-width: 0; /* Quan trọng: cho phép text co lại đúng cách */
    }

    /* Highlight Boxes */
    .alert-custom {
        border-left: 4px solid var(--brand-red);
        background-color: #fff5f5;
        color: #842029;
        padding: 1rem;
        border-radius: 8px;
        margin: 0;
    }

    /* Page Header */
    .page-header {
        padding: 2.5rem 1rem 1.5rem;
        background-color: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 2rem;
        overflow: hidden; /* Ngăn nội dung tràn */
    }

    .list-group-item {
        background-color: transparent;
        padding: 0.5rem 0;
        word-break: break-word;
    }

    /* Accordion Improvements */
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: var(--brand-black);
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
        .policy-card {
            margin-left: 0;
            margin-right: 0;
        }
        
        .step-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .step-number {
            margin-bottom: 10px;
            margin-right: 0;
        }
        
        .d-flex.justify-content-center {
            flex-wrap: wrap;
            gap: 10px !important;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .page-header {
            padding: 1.5rem 1rem;
        }
        
        .display-5 {
            font-size: 2rem;
        }
    }

    /* Grid fixes */
    .row.g-4 {
        margin-left: -8px;
        margin-right: -8px;
    }
    
    .col-lg-8, .col-lg-4 {
        padding-left: 8px;
        padding-right: 8px;
    }

    /* Text overflow fixes */
    p, li, .accordion-body {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Button fixes */
    .btn {
        white-space: nowrap;
    }
</style>

<!-- Main Content Section -->
<main class="py-4">
    <div class="container">
        
        <!-- Page Header -->
        <div class="page-header text-center">
            <div class="row justify-content-center mx-0">
                <div class="col-12 col-lg-10 col-xl-8">
                    <h1 class="display-5 fw-bold mb-3" style="color: var(--brand-black);">CHÍNH SÁCH ĐỔI TRẢ</h1>
                    <p class="lead mb-4 text-muted">Cam kết mang lại sự hài lòng tuyệt đối cho khách hàng tại Sole Studio.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="#quy-trinh" class="btn btn-dark fw-bold px-4 rounded-pill">Quy trình đổi trả</a>
                        <a href="#contact-area" class="btn btn-outline-dark fw-bold px-4 rounded-pill">Liên hệ hỗ trợ</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="row g-4 mx-0">
            
            <!-- Left Column - Main Policies -->
            <div class="col-12 col-lg-8 px-2">
                
                <!-- Điều Kiện Đổi Trả -->
                <div class="policy-card p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="policy-icon-wrapper me-3 mb-0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="mb-0 fw-bold" style="word-break: break-word;">Điều Kiện Đổi Trả</h3>
                    </div>
                    
                    <p class="text-secondary mb-3">Sản phẩm đủ điều kiện đổi/trả khi đáp ứng các yêu cầu sau:</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item border-0 ps-0 py-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Sản phẩm còn nguyên tem mác, chưa qua sử dụng, giặt tẩy.
                        </li>
                        <li class="list-group-item border-0 ps-0 py-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Còn đầy đủ hộp, phụ kiện và hóa đơn mua hàng.
                        </li>
                        <li class="list-group-item border-0 ps-0 py-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Thời gian yêu cầu: <strong>Trong vòng 07 ngày</strong> kể từ ngày nhận hàng.
                        </li>
                        <li class="list-group-item border-0 ps-0 py-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất hoặc giao sai mẫu.
                        </li>
                    </ul>

                    <div class="alert alert-custom rounded-3 mt-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Lưu ý:</strong> Không áp dụng đổi trả với các sản phẩm xả hàng (Sale > 50%) hoặc sản phẩm đã qua sử dụng dẫn đến hư hỏng.
                    </div>
                </div>

                <!-- Quy Trình Thực Hiện -->
                <div class="policy-card p-3 p-md-4" id="quy-trinh">
                    <h3 class="section-title">Quy Trình Thực Hiện</h3>
                    
                    <div class="step-item d-flex">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <h5 class="fw-bold mb-2">Liên hệ hỗ trợ</h5>
                            <p class="text-muted mb-0">Gọi hotline <strong>0912345678</strong> hoặc nhắn tin qua Fanpage để thông báo yêu cầu đổi trả.</p>
                        </div>
                    </div>

                    <div class="step-item d-flex">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <h5 class="fw-bold mb-2">Đóng gói sản phẩm</h5>
                            <p class="text-muted mb-0">Đóng gói sản phẩm cẩn thận, kèm theo hóa đơn và quà tặng (nếu có) vào hộp ban đầu.</p>
                        </div>
                    </div>

                    <div class="step-item d-flex">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <h5 class="fw-bold mb-2">Gửi hàng về Sole Studio</h5>
                            <p class="text-muted mb-0">Gửi qua bưu điện hoặc mang trực tiếp đến địa chỉ: <strong>279 Nguyễn Tri Phương, P5, Q10, TP HCM</strong>.</p>
                        </div>
                    </div>

                    <div class="step-item d-flex">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <h5 class="fw-bold mb-2">Xử lý & Hoàn tất</h5>
                            <p class="text-muted mb-0">Chúng tôi sẽ kiểm tra trong 3-5 ngày. Nếu đạt yêu cầu, chúng tôi sẽ gửi sản phẩm mới hoặc hoàn tiền.</p>
                        </div>
                    </div>
                </div>

                <!-- Phương Thức Hoàn Tiền -->
                <div class="policy-card p-3 p-md-4">
                    <h3 class="section-title">Phương Thức Hoàn Tiền</h3>
                    <p class="mb-3">Nếu quý khách không muốn đổi sản phẩm khác, chúng tôi sẽ hoàn tiền theo hình thức:</p>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fas fa-university text-danger mb-2 d-block fs-4"></i>
                                <strong>Chuyển khoản</strong><br>
                                <span class="small text-muted">5-7 ngày làm việc</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fas fa-wallet text-danger mb-2 d-block fs-4"></i>
                                <strong>Ví điện tử / Thẻ</strong><br>
                                <span class="small text-muted">3-5 ngày làm việc</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - Sidebar -->
            <div class="col-12 col-lg-4 px-2">
                
                <!-- Contact Card -->
                <div class="policy-card p-3 p-md-4 text-center" id="contact-area">
                    <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                    <h4 class="fw-bold mb-2">Cần Hỗ Trợ Ngay?</h4>
                    <p class="text-muted small mb-4">Đội ngũ Sole Studio luôn sẵn sàng hỗ trợ bạn</p>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:0912345678" class="btn btn-outline-dark">
                            <i class="fas fa-phone-alt me-2"></i>0912345678
                        </a>
                        <a href="mailto:support@solestudio.com" class="btn btn-outline-dark">
                            <i class="fas fa-envelope me-2"></i>Gửi Email
                        </a>
                        <a href="#" class="btn btn-outline-dark">
                            <i class="fab fa-facebook-messenger me-2"></i>Facebook Messenger
                        </a>
                    </div>
                </div>

                <!-- FAQ Card -->
                <div class="policy-card p-3 p-md-4">
                    <h5 class="fw-bold mb-3">Câu Hỏi Thường Gặp</h5>
                    
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed shadow-none bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
                                    <span class="fw-medium">Phí đổi trả ai chịu?</span>
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small text-muted pt-2">
                                    Sole Studio chịu 100% phí ship nếu lỗi do nhà sản xuất. Khách hàng chịu phí ship nếu đổi theo nhu cầu (đổi size, đổi mẫu).
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed shadow-none bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                    <span class="fw-medium">Mất hóa đơn có đổi được không?</span>
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small text-muted pt-2">
                                    Quý khách vui lòng cung cấp Số điện thoại đặt hàng để chúng tôi tra cứu lịch sử đơn hàng trên hệ thống.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed shadow-none bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                    <span class="fw-medium">Thời gian xử lý đổi trả là bao lâu?</span>
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small text-muted pt-2">
                                    Sau khi nhận được sản phẩm, chúng tôi sẽ kiểm tra và xử lý trong vòng 3-5 ngày làm việc.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        
    </div>
</main>

<?php include 'footer.php'; ?>