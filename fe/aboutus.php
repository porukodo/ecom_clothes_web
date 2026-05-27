<?php 
    $current_page = "Về chúng tôi"; 
    include 'header.php'; 
?>

<style>
    .text-brand {
        color: #FF4500;
    }
    .divider-brand {
        height: 3px;
        width: 50px; 
        background-color: #FF4500;
        margin: 10px 0;
        border-radius: 2px;
    }
    
    .milestone-card {
        transition: transform 0.3s ease;
    }
    .milestone-card:hover {
        transform: translateY(-5px);
    }
</style>

<main class="bg-light py-5">
    <div class="container">
        
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center mb-5">
                    <h1 class="fw-bold text-uppercase mb-4">Sole Studio - Cột mốc và Tầm nhìn</h1>
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <p class="lead text-muted mb-3">
                                <strong>Sole Studio</strong> ra đời với sứ mệnh mang Streetwear đậm chất cá tính, sáng tạo đến với cộng đồng Gen Z. Chúng tôi không chỉ bán quần áo, mà còn lan tỏa tinh thần dám nghĩ, dám làm và tự thể hiện bản thân.
                            </p>
                            <p class="text-muted">
                                Trong suốt hành trình phát triển, Sole Studio đã trở thành lựa chọn hàng đầu của giới trẻ Việt Nam, không ngừng thách thức giới hạn và cùng cộng đồng định hình nên phong cách thời trang đường phố độc đáo.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3">
                    
                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+700K</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Sản phẩm</h5>
                            <p class="text-muted small">đã được bán ra thị trường, khẳng định chất lượng và phong cách.</p>
                        </div>
                    </div>

                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+90</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Tỉnh thành</h5>
                            <p class="text-muted small">có khách hàng thân thiết của Sole Studio trên khắp Việt Nam.</p>
                        </div>
                    </div>

                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+400</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Đối tác & Nhân sự</h5>
                            <p class="text-muted small">cùng Sole Studio xây dựng và phát triển cộng đồng thời trang Streetwear.</p>
                        </div>
                    </div>

                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+100K</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Followers</h5>
                            <p class="text-muted small">trên các nền tảng xã hội, tạo nên một cộng đồng Gen Z sôi nổi và tương tác cao.</p>
                        </div>
                    </div>

                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+8.1 M</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Lượt tương tác</h5>
                            <p class="text-muted small"> trên các chiến dịch truyền thông của Sole Studio trong 1 năm.</p>
                        </div>
                    </div>

                    <div class="col">
                        <div class="milestone-card h-100">
                            <div class="display-5 fw-bold text-brand">+4</div>
                            <div class="divider-brand"></div>
                            <h5 class="fw-bold text-dark mt-2">Bộ sưu tập</h5>
                            <p class="text-muted small">chính thức (BST) được Sole Studio ra mắt mỗi năm, luôn dẫn đầu xu hướng.</p>
                        </div>
                    </div>

                </div> </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>