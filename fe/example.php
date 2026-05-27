<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo Dõi Đơn Hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
       
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
       
        .header-tabs {
            background: white;
            border-bottom: 1px solid #e5e5e5;
            position: sticky;
            top: 0;
            z-index: 100;
        }
       
        .tabs-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            overflow-x: auto;
        }
       
        .tab-button {
            padding: 16px 24px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-size: 15px;
            white-space: nowrap;
            color: #666;
            transition: all 0.3s;
        }
       
        .tab-button:hover {
            color: #333;
        }
       
        .tab-button.active {
            color: #ee4d2d;
            border-bottom-color: #ee4d2d;
            font-weight: 500;
        }
       
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 16px;
        }
       
        .search-box {
            position: relative;
            margin-bottom: 24px;
        }
       
        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
       
        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }
       
        .search-input:focus {
            border-color: #ee4d2d;
        }
       
        .order-card {
            background: white;
            border-radius: 4px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
       
        .order-header {
            padding: 16px 24px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .order-id {
            font-weight: 500;
            color: #333;
        }
       
        .order-status-container {
            display: flex;
            align-items: center;
            gap: 16px;
        }
       
        .delivery-success {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #26aa99;
            font-size: 14px;
        }
       
        .status-badge {
            font-weight: 600;
            font-size: 14px;
        }
       
        .status-completed { color: #ee4d2d; }
        .status-shipping { color: #1890ff; }
        .status-cancelled { color: #999; }
        .status-return { color: #ff8c00; }
        .status-exchange { color: #9c27b0; }
       
        .product-item {
            padding: 16px 24px;
            display: flex;
            gap: 16px;
        }
       
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
        }
       
        .product-info {
            flex: 1;
        }
       
        .product-name {
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
       
        .product-variant {
            font-size: 13px;
            color: #999;
            margin-bottom: 4px;
        }
       
        .product-quantity {
            font-size: 14px;
            color: #666;
        }
       
        .product-price {
            text-align: right;
        }
       
        .original-price {
            font-size: 13px;
            color: #999;
            text-decoration: line-through;
            margin-bottom: 4px;
        }
       
        .current-price {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
       
        .order-footer {
            padding: 16px 24px;
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .action-buttons {
            display: flex;
            gap: 12px;
        }
       
        .btn {
            padding: 10px 24px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
       
        .btn-secondary {
            background: white;
            border: 1px solid #ddd;
            color: #333;
        }
       
        .btn-secondary:hover {
            background: #f5f5f5;
        }
       
        .btn-primary {
            background: #ee4d2d;
            color: white;
        }
       
        .btn-primary:hover {
            background: #d73211;
        }
       
        .order-total {
            text-align: right;
        }
       
        .total-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }
       
        .total-price {
            font-size: 24px;
            font-weight: 600;
            color: #ee4d2d;
        }
       
        .empty-state {
            background: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 4px;
        }
       
        .empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #ddd;
        }
       
        .empty-text {
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Header Tabs -->
    <div class="header-tabs">
        <div class="tabs-container">
            <button class="tab-button active" data-tab="all">Tất cả</button>
            <button class="tab-button" data-tab="shipping">Đang giao hàng</button>
            <button class="tab-button" data-tab="completed">Hoàn thành</button>
            <button class="tab-button" data-tab="cancelled">Hủy đơn</button>
            <button class="tab-button" data-tab="return">Trả hàng</button>
            <button class="tab-button" data-tab="exchange">Đổi hàng</button>
        </div>
    </div>


    <!-- Main Container -->
    <div class="container">
        <!-- Search Box -->
        <div class="search-box">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input
                type="text"
                class="search-input"
                id="searchInput"
                placeholder="Bạn có thể tìm kiếm theo mã đơn hàng hoặc Tên Sản phẩm"
            >
        </div>


        <!-- Orders Container -->
        <div id="ordersContainer"></div>
    </div>


    <script>
        // Dữ liệu đơn hàng
        const orders = [
            {
                id: 'DH001',
                orderId: '#DH2024001',
                status: 'completed',
                deliverySuccess: true,
                products: [
                    {
                        name: '[DAILY] Sữa Rửa Mặt Dưỡng Trắng Cho Mọi Loại Da Hada Labo Perfect White Cleanser 80g Hasaki Chính Hãng',
                        image: 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=100&h=100&fit=crop',
                        quantity: 1,
                        originalPrice: 97000,
                        price: 78000
                    }
                ],
                total: 70200
            },
            {
                id: 'DH002',
                orderId: '#DH2024002',
                status: 'completed',
                deliverySuccess: true,
                products: [
                    {
                        name: 'Trà Sữa Matcha Cozy Hòa Tan 3IN1 (170gr - 10 gói và 306gr - 18 gói) Hương Vị Ngọt Thơm, Không Lo Tăng Cân',
                        image: 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=100&h=100&fit=crop',
                        quantity: 1,
                        variant: 'Hộp Nhỏ 10gói',
                        originalPrice: 50000,
                        price: 37500
                    }
                ],
                total: 36159
            },
            {
                id: 'DH003',
                orderId: '#DH2024003',
                status: 'shipping',
                deliverySuccess: false,
                products: [
                    {
                        name: 'Kem Dưỡng Da Mặt Cetaphil Sáng Da Và Mờ Thâm Nám 50ml',
                        image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=100&h=100&fit=crop',
                        quantity: 2,
                        originalPrice: 285000,
                        price: 245000
                    }
                ],
                total: 490000
            },
            {
                id: 'DH004',
                orderId: '#DH2024004',
                status: 'cancelled',
                deliverySuccess: false,
                products: [
                    {
                        name: 'Serum Vitamin C The Ordinary Ascorbic Acid 8% + Alpha Arbutin 2%',
                        image: 'https://images.unsplash.com/photo-1620916297026-22412e9f3f47?w=100&h=100&fit=crop',
                        quantity: 1,
                        originalPrice: 180000,
                        price: 165000
                    }
                ],
                total: 165000
            }
        ];


        const statusLabels = {
            completed: { text: 'HOÀN THÀNH', class: 'status-completed' },
            shipping: { text: 'ĐANG GIAO HÀNG', class: 'status-shipping' },
            cancelled: { text: 'ĐÃ HỦY', class: 'status-cancelled' },
            return: { text: 'TRẢ HÀNG', class: 'status-return' },
            exchange: { text: 'ĐỔI HÀNG', class: 'status-exchange' }
        };


        let currentTab = 'all';
        let searchQuery = '';


        function formatPrice(price) {
            return price.toLocaleString('vi-VN') + '₫';
        }


        function renderOrders() {
            const container = document.getElementById('ordersContainer');
            const filteredOrders = orders.filter(order => {
                const matchesTab = currentTab === 'all' || order.status === currentTab;
                const matchesSearch = order.orderId.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                    order.products.some(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()));
                return matchesTab && matchesSearch;
            });


            if (filteredOrders.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="empty-text">Không có đơn hàng nào</p>
                    </div>
                `;
                return;
            }


            container.innerHTML = filteredOrders.map(order => {
                const status = statusLabels[order.status];
                const productsHTML = order.products.map(product => `
                    <div class="product-item">
                        <img src="${product.image}" alt="${product.name}" class="product-image">
                        <div class="product-info">
                            <div class="product-name">${product.name}</div>
                            ${product.variant ? `<div class="product-variant">Phân loại hàng: ${product.variant}</div>` : ''}
                            <div class="product-quantity">x${product.quantity}</div>
                        </div>
                        <div class="product-price">
                            ${product.originalPrice > product.price ?
                                `<div class="original-price">${formatPrice(product.originalPrice)}</div>` : ''}
                            <div class="current-price">${formatPrice(product.price)}</div>
                        </div>
                    </div>
                `).join('');


                return `
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Mã đơn hàng: ${order.orderId}</div>
                            <div class="order-status-container">
                                ${order.deliverySuccess ? `
                                    <div class="delivery-success">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Giao hàng thành công
                                    </div>
                                ` : ''}
                                <div class="status-badge ${status.class}">${status.text}</div>
                            </div>
                        </div>
                        ${productsHTML}
                        <div class="order-footer">
                            <div class="action-buttons">
                                <button class="btn btn-secondary">Liên Hệ Người Bán</button>
                                <button class="btn btn-primary">Mua Lại</button>
                            </div>
                            <div class="order-total">
                                <div class="total-label">Thành tiền:</div>
                                <div class="total-price">${formatPrice(order.total)}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }


        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentTab = this.getAttribute('data-tab');
                renderOrders();
            });
        });


        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            searchQuery = e.target.value;
            renderOrders();
        });


        // Initial render
        renderOrders();
    </script>
</body>
</html>

