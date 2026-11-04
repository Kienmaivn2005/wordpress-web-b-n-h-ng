/**
 * Teelab Cart, Checkout & Order Tracking Script
 * Quản lý giỏ hàng localStorage, Đặt hàng & Tra cứu đơn hàng thời gian thực
 */

(function($) {
    'use strict';

    var CART_KEY = 'kien_cart';
    var AJAX_URL = (window.KienConfig && window.KienConfig.ajaxUrl) || '/thekien05/wp-admin/admin-ajax.php';
    var NONCE = (window.KienConfig && window.KienConfig.nonce) || '';

    // ================= HELPER FUNCTIONS =================
    function getCart() {
        try {
            var data = localStorage.getItem(CART_KEY);
            return data ? JSON.parse(data) : [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateHeaderBadges();
        renderDrawerItems();
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    function updateHeaderBadges() {
        var cart = getCart();
        var totalQty = 0;
        cart.forEach(function(item) {
            totalQty += parseInt(item.quantity || 1, 10);
        });

        // Cập nhật tất cả các badge giỏ hàng trên header
        $('.count_item_pr, .count_item, .kien-cart-total-qty').text(totalQty);
    }

    function showToast(message) {
        var $toast = $('.kien-toast');
        if (!$toast.length) {
            $toast = $('<div class="kien-toast"></div>').appendTo('body');
        }
        $toast.html('<span>✓</span> ' + message).addClass('show');
        setTimeout(function() {
            $toast.removeClass('show');
        }, 2800);
    }

    // ================= CART DRAWER & MODALS INJECTION =================
    
    // ================= 34 TỈNH THÀNH VIỆT NAM (SÁP NHẬP MỚI) ĐẦY ĐỦ XÃ/PHƯỜNG =================
    // Cấu trúc 2 cấp chuẩn: Tỉnh/Thành phố sáp nhập mới -> Đầy đủ Xã / Phường / Thị trấn (Bỏ cấp huyện)
    var PROVINCES_34_LIST = [
        { key: "Hà Nội", name: "Hà Nội" },
        { key: "TP. Hồ Chí Minh", name: "TP. Hồ Chí Minh (TP.HCM, Bình Dương, BR.Vũng Tàu)" },
        { key: "TP. Đà Nẵng", name: "TP. Đà Nẵng (Đà Nẵng, Quảng Nam)" },
        { key: "TP. Hải Phòng", name: "TP. Hải Phòng (Hải Phòng, Hải Dương)" },
        { key: "TP. Cần Thơ", name: "TP. Cần Thơ (Cần Thơ, Hậu Giang, Sóc Trăng)" },
        { key: "TP. Huế", name: "TP. Huế" },
        { key: "Đắk Lắk", name: "Đắk Lắk (Đắk Lắk, Phú Yên)" },
        { key: "Đồng Nai", name: "Đồng Nai (Đồng Nai, Bình Phước)" },
        { key: "Lâm Đồng", name: "Lâm Đồng (Lâm Đồng, Đắk Nông, Bình Thuận)" },
        { key: "Gia Lai", name: "Gia Lai (Gia Lai, Bình Định)" },
        { key: "Khánh Hòa", name: "Khánh Hòa (Khánh Hòa, Ninh Thuận)" },
        { key: "Bắc Ninh", name: "Bắc Ninh (Bắc Ninh, Bắc Giang)" },
        { key: "Ninh Bình", name: "Ninh Bình (Ninh Bình, Hà Nam, Nam Định)" },
        { key: "Phú Thọ", name: "Phú Thọ (Phú Thọ, Vĩnh Phúc, Hòa Bình)" },
        { key: "Hưng Yên", name: "Hưng Yên (Hưng Yên, Thái Bình)" },
        { key: "Thái Nguyên", name: "Thái Nguyên (Thái Nguyên, Bắc Kạn)" },
        { key: "Lào Cai", name: "Lào Cai (Lào Cai, Yên Bái)" },
        { key: "Tuyên Quang", name: "Tuyên Quang (Tuyên Quang, Hà Giang)" },
        { key: "Quảng Trị", name: "Quảng Trị (Quảng Trị, Quảng Bình)" },
        { key: "Quảng Ngãi", name: "Quảng Ngãi (Quảng Ngãi, Kon Tum)" },
        { key: "Thanh Hóa", name: "Thanh Hóa" },
        { key: "Nghệ An", name: "Nghệ An" },
        { key: "Hà Tĩnh", name: "Hà Tĩnh" },
        { key: "Quảng Ninh", name: "Quảng Ninh" },
        { key: "Sơn La", name: "Sơn La" },
        { key: "Lai Châu", name: "Lai Châu" },
        { key: "Điện Biên", name: "Điện Biên" },
        { key: "Lạng Sơn", name: "Lạng Sơn" },
        { key: "Cao Bằng", name: "Cao Bằng" },
        { key: "Tây Ninh", name: "Tây Ninh (Tây Ninh, Long An)" },
        { key: "Đồng Tháp", name: "Đồng Tháp (Đồng Tháp, Tiền Giang)" },
        { key: "Vĩnh Long", name: "Vĩnh Long (Vĩnh Long, Bến Tre, Trà Vinh)" },
        { key: "An Giang", name: "An Giang (An Giang, Kiên Giang)" },
        { key: "Cà Mau", name: "Cà Mau (Cà Mau, Bạc Liêu)" }
    ];

    function initVietnamAddressSelector() {
        var $province = $('#kien-province');
        var $ward = $('#kien-ward');
        if (!$province.length) return;

        var provincesList = [];
        var wardsByProvince = {};

        // 1. Ưu tiên tải dữ liệu VietMap từ bộ nhớ cục bộ (siêu tốc, offline/online đều mượt)
        if (typeof VIETMAP_PROVINCES !== 'undefined' && typeof VIETMAP_WARDS_BY_PROVINCE !== 'undefined') {
            provincesList = VIETMAP_PROVINCES;
            wardsByProvince = VIETMAP_WARDS_BY_PROVINCE;
            renderProvinces(provincesList);
        } else {
            // Hoặc gọi qua Fetch API chuẩn VietMap dataset
            fetch('/thekien05/wp-content/themes/ht-tracker-v3/api/admin_new/province.json')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    provincesList = Object.values(data).sort(function(a, b) {
                        return (a.name || '').localeCompare(b.name || '', 'vi');
                    });
                    renderProvinces(provincesList);

                    // Tải thêm ward.json
                    return fetch('/thekien05/wp-content/themes/ht-tracker-v3/api/admin_new/ward.json');
                })
                .then(function(r) { if (r) return r.json(); })
                .then(function(wData) {
                    if (wData) {
                        wardsByProvince = {};
                        Object.values(wData).forEach(function(w) {
                            var pCode = String(w.parent_code);
                            if (!wardsByProvince[pCode]) wardsByProvince[pCode] = [];
                            wardsByProvince[pCode].push(w);
                        });
                    }
                })
                .catch(function(err) {
                    console.log('VietMap API fallback:', err);
                });
        }

        function renderProvinces(list) {
            $province.empty().append('<option value="">-- Chọn Tỉnh / Thành phố (34 tỉnh VietMap) --</option>');
            list.forEach(function(item) {
                var pLabel = item.name_with_type || item.name;
                $province.append(`<option value="${item.code}" data-code="${item.code}" data-name="${pLabel}">${pLabel}</option>`);
            });
        }

        // 2. Lắng nghe khi chọn Tỉnh thành -> đổ đầy đủ Xã/Phường theo parent_code
        $province.off('change').on('change', function() {
            var selectedCode = $(this).val();
            $ward.empty();

            if (!selectedCode) {
                $ward.append('<option value="">-- Vui lòng chọn Tỉnh / TP trước --</option>');
                return;
            }

            var wards = wardsByProvince[String(selectedCode)] || [];

            if (wards && wards.length) {
                $ward.append(`<option value="">-- Chọn Xã / Phường (Đầy đủ ${wards.length} xã/phường) --</option>`);
                wards.forEach(function(w) {
                    var wLabel = w.name_with_type || w.name;
                    $ward.append(`<option value="${wLabel}" data-name="${wLabel}">${wLabel}</option>`);
                });
            } else {
                $ward.append('<option value="">-- Không có dữ liệu xã/phường --</option>');
            }
        });
    }

    // Helper kiểm tra định dạng Số điện thoại Việt Nam chuẩn
    function validateVNPhone(phone) {
        var clean = phone.replace(/[\s\.\-\(\)]/g, '');
        // 10 chữ số bắt đầu bằng 03, 05, 07, 08, 09 (hoặc +84/84)
        var regex = /^(0|\+?84)(3|5|7|8|9)[0-9]{8}$/;
        return regex.test(clean);
    }

    function initCartUI() {
        if ($('.kien-cart-drawer').length) return;

        var html = `
            <!-- Backdrop -->
            <div class="kien-cart-backdrop"></div>

            <!-- Cart Drawer -->
            <div class="kien-cart-drawer">
                <div class="kien-cart-header">
                    <h3 class="kien-cart-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Giỏ hàng (<span class="kien-cart-total-qty">0</span>)
                    </h3>
                    <button class="kien-cart-close" title="Đóng">&times;</button>
                </div>
                <div class="kien-freeship-bar">
                    <div class="kien-freeship-text">Freeship đơn hàng từ 400.000₫</div>
                    <div class="kien-freeship-progress">
                        <div class="kien-freeship-fill"></div>
                    </div>
                </div>
                <div class="kien-cart-body">
                    <!-- Dynamic cart items will render here -->
                </div>
                <div class="kien-cart-footer">
                    <div class="kien-cart-subtotal-row">
                        <span class="kien-cart-subtotal-label">Tạm tính:</span>
                        <span class="kien-cart-subtotal-val">0₫</span>
                    </div>
                    <button class="kien-btn-checkout">
                        TIẾN HÀNH ĐẶT HÀNG
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                    <a href="javascript:;" class="btn-open-tracking" style="display:block; text-align:center; font-size:13px; color:#555; margin-top:12px; text-decoration:none; font-weight:600;">
                        📦 Bạn đã có mã đơn? <u>Tra cứu đơn hàng tại đây</u>
                    </a>
                </div>
            </div>

            <!-- Checkout Modal -->
            <div class="kien-checkout-modal">
                <div class="kien-modal-header">
                    <h3 class="kien-modal-title">Thông tin đặt hàng</h3>
                    <button class="kien-modal-close" title="Đóng">&times;</button>
                </div>
                <div class="kien-modal-body">
                    <div class="kien-order-summary">
                        <div class="kien-order-summary-title">Tóm tắt đơn hàng</div>
                        <div class="kien-summary-items-list"></div>
                        <div class="kien-summary-item" style="margin-top:8px;">
                            <span>Phí vận chuyển:</span>
                            <span class="kien-summary-shipping">Miễn phí</span>
                        </div>
                        <div class="kien-summary-total">
                            <span>Tổng thanh toán:</span>
                            <span class="kien-summary-final-total">0₫</span>
                        </div>
                    </div>

                    <form id="kien-checkout-form">
                        <div class="kien-form-group">
                            <label class="kien-form-label">Họ và tên người nhận <span class="req">*</span></label>
                            <input type="text" name="customer_name" class="kien-form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                        </div>
                        <div class="kien-form-group">
                            <label class="kien-form-label">Số điện thoại liên hệ <span class="req">*</span></label>
                            <input type="tel" name="customer_phone" id="kien-customer-phone" class="kien-form-control" required placeholder="Ví dụ: 0912345678 hoặc 0359235911" maxlength="11">
                            <div id="kien-phone-error" style="color:#e53935; font-size:12px; margin-top:4px; display:none; font-weight:600;"></div>
                        </div>
                        <div class="kien-form-group">
                            <label class="kien-form-label">Địa chỉ nhận hàng <span class="req">*</span></label>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                                <div>
                                    <select name="customer_province" id="kien-province" class="kien-form-control" required style="cursor:pointer; height:42px; font-size:13px;">
                                        <option value="">-- Chọn Tỉnh / TP (34 tỉnh sáp nhập) --</option>
                                    </select>
                                </div>
                                <div>
                                    <select name="customer_ward" id="kien-ward" class="kien-form-control" required style="cursor:pointer; height:42px; font-size:13px;">
                                        <option value="">-- Chọn Xã / Phường / Thị trấn --</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="customer_detail_address" id="kien-detail-address" class="kien-form-control" required placeholder="Số nhà, tên đường, thôn, xóm/tổ dân phố...">
                            <input type="hidden" name="customer_address" id="kien-full-address">
                        </div>
                        <div class="kien-form-group">
                            <label class="kien-form-label">Ghi chú giao hàng (nếu có)</label>
                            <input type="text" name="customer_notes" class="kien-form-control" placeholder="Ví dụ: Giao sau 5h chiều, gọi trước khi đến...">
                        </div>
                        <div class="kien-form-group">
                            <label class="kien-form-label">Phương thức thanh toán</label>
                            <div class="kien-payment-methods">
                                <label class="kien-payment-option active">
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <span class="kien-payment-desc">Thanh toán khi nhận hàng (COD)</span>
                                </label>
                                <label class="kien-payment-option">
                                    <input type="radio" name="payment_method" value="banking">
                                    <span class="kien-payment-desc">Chuyển khoản VietQR (MB Bank)</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="kien-modal-footer">
                    <button type="submit" form="kien-checkout-form" class="kien-btn-confirm-order">
                        XÁC NHẬN ĐẶT HÀNG
                    </button>
                </div>
            </div>

            <!-- Order Tracking Modal -->
            <div class="kien-tracking-modal">
                <div class="kien-modal-header">
                    <h3 class="kien-modal-title">Tra cứu đơn hàng</h3>
                    <button class="kien-modal-close kien-tracking-close" title="Đóng">&times;</button>
                </div>
                <div class="kien-modal-body">
                    <form id="kien-tracking-form" class="kien-tracking-searchbox">
                        <input type="text" id="kien-track-keyword" class="kien-tracking-input" placeholder="Nhập mã đơn (VD: TK261102...) hoặc Số điện thoại" required>
                        <button type="submit" class="kien-btn-track-submit">TRA CỨU</button>
                    </form>
                    <div id="kien-tracking-results">
                        <div style="text-align:center; padding:30px 10px; color:#888;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:12px;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            <p style="font-size:14px; margin:0;">Nhập mã đơn hàng hoặc số điện thoại để kiểm tra tiến độ giao hàng và thời gian thực.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(html);
        bindCartEvents();
        updateHeaderBadges();

        // Check URL for ?track=TK...
        checkUrlForTracking();
    }

    // ================= RENDER CART ITEMS =================
    function renderDrawerItems() {
        var cart = getCart();
        var $body = $('.kien-cart-body');
        var $subtotal = $('.kien-cart-subtotal-val');
        var $btnCheckout = $('.kien-btn-checkout');
        var $freeshipFill = $('.kien-freeship-fill');
        var $freeshipText = $('.kien-freeship-text');

        if (!cart.length) {
            $body.html(`
                <div class="kien-cart-empty">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <p>Giỏ hàng của bạn đang trống</p>
                    <button class="btn-shop-now">Khám phá sản phẩm</button>
                </div>
            `);
            $subtotal.text('0₫');
            $btnCheckout.prop('disabled', true).css('opacity', 0.5);
            $freeshipFill.css('width', '0%');
            $freeshipText.text('Freeship đơn hàng từ 400.000₫');
            return;
        }

        $btnCheckout.prop('disabled', false).css('opacity', 1);

        var subtotal = 0;
        var itemsHtml = '';

        cart.forEach(function(item, idx) {
            var lineTotal = (item.price || 0) * (item.quantity || 1);
            subtotal += lineTotal;

            var variantText = [];
            if (item.color) variantText.push(item.color);
            if (item.size) variantText.push(item.size);
            var variantStr = variantText.length ? variantText.join(' / ') : 'Tiêu chuẩn';

            itemsHtml += `
                <div class="kien-cart-item" data-idx="${idx}">
                    <img class="kien-cart-item-img" src="${item.image || 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo.png'}" alt="${item.name}">
                    <div class="kien-cart-item-info">
                        <h4 class="kien-cart-item-name">${item.name}</h4>
                        <div class="kien-cart-item-variant">${variantStr}</div>
                        <div class="kien-cart-item-price">${formatMoney(item.price)}</div>
                        <div class="kien-cart-item-stepper">
                            <button type="button" class="btn-qty-minus">-</button>
                            <span>${item.quantity || 1}</span>
                            <button type="button" class="btn-qty-plus">+</button>
                        </div>
                    </div>
                    <button class="kien-cart-item-del" title="Xóa món này">&times;</button>
                </div>
            `;
        });

        $body.html(itemsHtml);
        $subtotal.text(formatMoney(subtotal));

        // Tính tiến độ freeship (400.000₫)
        if (subtotal >= 400000) {
            $freeshipFill.css('width', '100%');
            $freeshipText.html('🎉 Bạn đã được <strong>MIỄN PHÍ VẬN CHUYỂN</strong> toàn quốc!');
        } else {
            var diff = 400000 - subtotal;
            var pct = Math.min(100, Math.round((subtotal / 400000) * 100));
            $freeshipFill.css('width', pct + '%');
            $freeshipText.html(`Mua thêm <strong>${formatMoney(diff)}</strong> để được FREESHIP!`);
        }
    }

    // ================= CART DRAWER CONTROLS =================
    function openCartDrawer() {
        initCartUI();
        renderDrawerItems();
        closeCheckoutModal();
        closeTrackingModal();
        $('.kien-cart-backdrop').addClass('active');
        $('.kien-cart-drawer').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeCartDrawer() {
        $('.kien-cart-drawer').removeClass('active');
        if (!$('.kien-checkout-modal').hasClass('active') && !$('.kien-tracking-modal').hasClass('active')) {
            $('.kien-cart-backdrop').removeClass('active');
            $('body').css('overflow', '');
        }
    }

    // ================= CHECKOUT MODAL CONTROLS =================
    function openCheckoutModal() {
        setTimeout(initVietnamAddressSelector, 100);
        var cart = getCart();
        if (!cart.length) {
            alert('Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước.');
            return;
        }

        initCartUI();
        closeCartDrawer();
        closeTrackingModal();

        var subtotal = 0;
        var summaryHtml = '';
        cart.forEach(function(item) {
            var lineTotal = (item.price || 0) * (item.quantity || 1);
            subtotal += lineTotal;
            var vText = [item.color, item.size].filter(Boolean).join(' / ');
            summaryHtml += `
                <div class="kien-summary-item">
                    <span>${item.name} ${vText ? '('+vText+')' : ''} x ${item.quantity}</span>
                    <span>${formatMoney(lineTotal)}</span>
                </div>
            `;
        });

        var shippingFee = (subtotal >= 400000) ? 0 : 30000;
        var finalTotal = subtotal + shippingFee;

        $('.kien-summary-items-list').html(summaryHtml);
        $('.kien-summary-shipping').text(shippingFee === 0 ? 'Miễn phí (Đơn >= 400k)' : formatMoney(shippingFee));
        $('.kien-summary-final-total').text(formatMoney(finalTotal));

        $('.kien-cart-backdrop').addClass('active');
        $('.kien-checkout-modal').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeCheckoutModal() {
        $('.kien-checkout-modal').removeClass('active');
        if (!$('.kien-cart-drawer').hasClass('active') && !$('.kien-tracking-modal').hasClass('active')) {
            $('.kien-cart-backdrop').removeClass('active');
            $('body').css('overflow', '');
        }
    }

    // ================= TRACKING MODAL CONTROLS =================
    function openTrackingModal(keyword) {
        initCartUI();
        closeCartDrawer();
        closeCheckoutModal();

        $('.kien-cart-backdrop').addClass('active');
        $('.kien-tracking-modal').addClass('active');
        $('body').css('overflow', 'hidden');

        if (keyword) {
            $('#kien-track-keyword').val(keyword);
            searchOrder(keyword);
        } else {
            setTimeout(function() {
                $('#kien-track-keyword').focus();
            }, 100);
        }
    }

    function closeTrackingModal() {
        $('.kien-tracking-modal').removeClass('active');
        if (!$('.kien-cart-drawer').hasClass('active') && !$('.kien-checkout-modal').hasClass('active')) {
            $('.kien-cart-backdrop').removeClass('active');
            $('body').css('overflow', '');
        }
    }

    function searchOrder(keyword) {
        keyword = $.trim(keyword || $('#kien-track-keyword').val());
        if (!keyword) {
            alert('Vui lòng nhập mã đơn hàng hoặc số điện thoại.');
            return;
        }

        var $results = $('#kien-tracking-results');
        $results.html(`
            <div style="text-align:center; padding:30px 10px; color:#555;">
                <div style="font-size:24px; margin-bottom:8px;">⏳</div>
                <p style="font-size:14px; font-weight:600;">Đang tra cứu dữ liệu thời gian thực...</p>
            </div>
        `);

        $.ajax({
            url: AJAX_URL,
            type: 'POST',
            data: {
                action: 'kien_track_order',
                nonce: NONCE,
                keyword: keyword
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success && res.data && res.data.orders && res.data.orders.length) {
                    renderTrackingResults(res.data.orders);
                } else {
                    var msg = (res && res.data && res.data.message) ? res.data.message : 'Không tìm thấy đơn hàng với thông tin đã nhập.';
                    $results.html(`
                        <div style="text-align:center; padding:30px 10px; color:#e53935;">
                            <div style="font-size:30px; margin-bottom:8px;">❌</div>
                            <p style="font-size:14px; font-weight:700;">${msg}</p>
                            <p style="font-size:13px; color:#666;">Vui lòng kiểm tra lại mã đơn hàng (Ví dụ: <strong>TK261102...</strong>) hoặc Số điện thoại đặt hàng.</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $results.html(`
                    <div style="text-align:center; padding:25px 10px; color:#e53935;">
                        <p style="font-size:14px;">Lỗi kết nối máy chủ (${error}). Vui lòng thử lại!</p>
                    </div>
                `);
            }
        });
    }

    function renderTrackingResults(orders) {
        var $results = $('#kien-tracking-results');
        var html = '';

        orders.forEach(function(o) {
            var step = parseInt(o.status_step || 1, 10);
            var pct = step <= 1 ? 0 : (step === 2 ? 33 : (step === 3 ? 66 : 100));

            // Danh sách sản phẩm
            var itemsHtml = '';
            if (o.items && o.items.length) {
                o.items.forEach(function(it) {
                    var vText = [it.color, it.size].filter(Boolean).join(' / ');
                    itemsHtml += `
                        <div class="kien-track-item-row">
                            <img src="${it.product_image || 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo.png'}" alt="${it.product_name}">
                            <div class="kien-track-item-detail">
                                <h5 class="kien-track-item-name">${it.product_name}</h5>
                                <div class="kien-track-item-meta">${vText ? 'Phân loại: '+vText+' | ' : ''}SL: ${it.quantity}</div>
                            </div>
                            <div class="kien-track-item-total">${it.line_total_formatted || formatMoney(it.price * it.quantity)}</div>
                        </div>
                    `;
                });
            }

            html += `
                <div class="kien-track-order-card">
                    <div class="kien-track-order-header">
                        <div>
                            <span class="kien-track-order-code">MÃ ĐƠN: ${o.order_code}</span>
                            <div style="font-size:12px; color:#666; margin-top:2px;">
                                🕒 Thời gian đặt: <strong>${o.created_at_formatted || o.created_at}</strong> (Giờ thực tế)
                            </div>
                        </div>
                        <span class="kien-track-order-badge" style="background:${o.status_color || '#000'}">
                            ${o.status_label || o.order_status}
                        </span>
                    </div>

                    <!-- Timeline 4 bước -->
                    <div class="kien-track-timeline">
                        <div class="kien-track-timeline-line-active" style="width:${pct}%"></div>
                        
                        <div class="kien-track-step ${step >= 1 ? 'active' : ''}">
                            <div class="kien-track-step-circle">1</div>
                            <span class="kien-track-step-label">Đã đặt hàng</span>
                        </div>
                        <div class="kien-track-step ${step >= 2 ? 'active' : ''}">
                            <div class="kien-track-step-circle">2</div>
                            <span class="kien-track-step-label">Đang đóng gói</span>
                        </div>
                        <div class="kien-track-step ${step >= 3 ? 'active' : ''}">
                            <div class="kien-track-step-circle">3</div>
                            <span class="kien-track-step-label">Đang giao hàng</span>
                        </div>
                        <div class="kien-track-step ${step >= 4 ? 'active' : ''}">
                            <div class="kien-track-step-circle">4</div>
                            <span class="kien-track-step-label">Hoàn tất</span>
                        </div>
                    </div>

                    <!-- Thông tin giao hàng -->
                    <div class="kien-track-info-grid">
                        <div><strong>Người nhận:</strong> ${o.customer_name} (${o.customer_phone})</div>
                        <div><strong>Thanh toán:</strong> ${o.payment_label || o.payment_method}</div>
                        <div style="grid-column: 1 / -1;"><strong>Địa chỉ giao:</strong> ${o.customer_address}</div>
                        ${o.notes ? '<div style="grid-column: 1 / -1;"><strong>Ghi chú:</strong> '+o.notes+'</div>' : ''}
                    </div>

                    <!-- Danh sách món -->
                    <div class="kien-track-items">
                        ${itemsHtml}
                    </div>

                    <!-- Tổng thanh toán -->
                    <div class="kien-track-total-box">
                        <span>Tạm tính: ${o.subtotal_formatted}</span> | 
                        <span>Phí ship: ${o.shipping_formatted}</span> | 
                        <span>Tổng thanh toán: <strong>${o.total_formatted}</strong></span>
                    </div>
                </div>
            `;
        });

        $results.html(html);
    }

    function checkUrlForTracking() {
        var params = new URLSearchParams(window.location.search);
        var trackCode = params.get('track') || params.get('tracking_code') || params.get('order_code');
        if (trackCode) {
            openTrackingModal(trackCode);
        } else if (params.get('page') === 'tracking' || params.has('track_order')) {
            openTrackingModal('');
        }
    }

    // ================= EVENT BINDINGS =================
    function bindCartEvents() {
        // Đóng mở Drawer & Modals
        $(document).on('click', '.kien-cart-close, .btn-shop-now', function() {
            closeCartDrawer();
        });
        $(document).on('click', '.kien-modal-close', function() {
            closeCheckoutModal();
        });
        $(document).on('click', '.kien-tracking-close', function() {
            closeTrackingModal();
        });
        $(document).on('click', '.kien-cart-backdrop', function() {
            closeCartDrawer();
            closeCheckoutModal();
            closeTrackingModal();
        });

        // Bấm icon giỏ hàng trên header
        $(document).on('click', '.block-cart, .header-cart, a[href*="page=cart"], a[href="/cart"]', function(e) {
            e.preventDefault();
            openCartDrawer();
        });

        // Bấm Tra cứu đơn hàng trên Header, Menu, Footer hoặc Cart Drawer
        $(document).on('click', '.btn-open-tracking, a[href*="page=tracking"]', function(e) {
            e.preventDefault();
            openTrackingModal('');
        });

        // Bấm Tra cứu đơn vừa đặt
        $(document).on('click', '.btn-track-my-order', function(e) {
            e.preventDefault();
            var code = $(this).data('code');
            openTrackingModal(code);
        });

        // Submit form tra cứu
        $(document).on('submit', '#kien-tracking-form', function(e) {
            e.preventDefault();
            searchOrder();
        });

        // Nút tăng/giảm/xóa số lượng
        $(document).on('click', '.btn-qty-minus', function() {
            var idx = $(this).closest('.kien-cart-item').data('idx');
            var cart = getCart();
            if (cart[idx]) {
                if (cart[idx].quantity > 1) {
                    cart[idx].quantity--;
                } else {
                    cart.splice(idx, 1);
                }
                saveCart(cart);
            }
        });

        $(document).on('click', '.btn-qty-plus', function() {
            var idx = $(this).closest('.kien-cart-item').data('idx');
            var cart = getCart();
            if (cart[idx]) {
                cart[idx].quantity = (cart[idx].quantity || 1) + 1;
                saveCart(cart);
            }
        });

        $(document).on('click', '.kien-cart-item-del', function() {
            var idx = $(this).closest('.kien-cart-item').data('idx');
            var cart = getCart();
            if (cart[idx]) {
                cart.splice(idx, 1);
                saveCart(cart);
            }
        });

        // Bấm "Tiến hành Đặt hàng" từ giỏ hàng
        $(document).on('click', '.kien-btn-checkout', function() {
            openCheckoutModal();
        });

        // Chọn phương thức thanh toán
        $(document).on('change', 'input[name="payment_method"]', function() {
            $('.kien-payment-option').removeClass('active');
            $(this).closest('.kien-payment-option').addClass('active');
        });

        // Submit form đặt hàng
        $(document).on('submit', '#kien-checkout-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('.kien-btn-confirm-order');

            var name = $.trim($form.find('input[name="customer_name"]').val());
            var phone = $.trim($form.find('input[name="customer_phone"]').val());
            var $phoneInput = $form.find('input[name="customer_phone"]');
            var $phoneError = $('#kien-phone-error');

            // 1. Kiểm tra định dạng số điện thoại chuẩn Việt Nam
            $phoneError.hide();
            $phoneInput.css('border-color', '');
            if (!phone || !validateVNPhone(phone)) {
                $phoneInput.css('border-color', '#e53935').focus();
                $phoneError.text('Số điện thoại không hợp lệ! Vui lòng nhập 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).').show();
                alert('Số điện thoại không hợp lệ! Vui lòng nhập 10 chữ số (Ví dụ: 0912345678 hoặc 0359235911).');
                return;
            }

            // 2. Lấy thông tin Tỉnh thành (34 tỉnh sáp nhập) / Xã Phường / Địa chỉ chi tiết
            var province = $('#kien-province option:selected').data('name') || $('#kien-province option:selected').text();
            var ward = $('#kien-ward').val();
            var detailAddress = $.trim($('#kien-detail-address').val());

            if (!province || province.indexOf('--') > -1) {
                alert('Vui lòng chọn Tỉnh / Thành phố nhận hàng.');
                $('#kien-province').focus();
                return;
            }
            if (!ward || ward.indexOf('--') > -1) {
                alert('Vui lòng chọn Xã / Phường / Thị trấn nhận hàng.');
                $('#kien-ward').focus();
                return;
            }
            if (!detailAddress) {
                alert('Vui lòng nhập địa chỉ chi tiết (Số nhà, tên đường, thôn, xóm...).');
                $('#kien-detail-address').focus();
                return;
            }

            var address = detailAddress + ', ' + ward + ', ' + province;
            $('#kien-full-address').val(address);
            var notes = $form.find('input[name="customer_notes"]').val().trim();
            var payment = $form.find('input[name="payment_method"]:checked').val() || 'cod';

            var cart = getCart();
            if (!cart.length) {
                alert('Giỏ hàng trống!');
                return;
            }

            if (!name || !phone || !address) {
                alert('Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ nhận hàng.');
                return;
            }

            $btn.prop('disabled', true).text('ĐANG XỬ LÝ ĐẶT HÀNG...');

            var payload = {
                action: 'kien_create_order',
                nonce: NONCE,
                name: name,
                phone: phone,
                address: address,
                notes: notes,
                payment_method: payment,
                items: JSON.stringify(cart)
            };

            $.ajax({
                url: AJAX_URL,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).text('XÁC NHẬN ĐẶT HÀNG');
                    if (res && res.success) {
                        var data = res.data;
                        // Xóa giỏ hàng
                        localStorage.removeItem(CART_KEY);
                        updateHeaderBadges();

                        // Hiển thị màn hình thành công
                        renderOrderSuccess(data, name, phone, address, payment);
                    } else {
                        var msg = (res && res.data && res.data.message) ? res.data.message : 'Có lỗi xảy ra, vui lòng thử lại.';
                        alert(msg);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('XÁC NHẬN ĐẶT HÀNG');
                    alert('Lỗi kết nối máy chủ (' + error + '). Vui lòng thử lại!');
                }
            });
        });
    }

    function renderOrderSuccess(data, name, phone, address, payment) {
        var qrSection = '';
        if (payment === 'banking') {
            var qrImg = data.qr_url || `https://img.vietqr.io/image/970422-0987654321-compact2.png?amount=${data.total}&addInfo=${data.order_code}&accountName=MAI%20DAM%20THE%20KIEN`;
            qrSection = `
                <div class="kien-qr-box">
                    <p style="font-weight:700; color:#000; margin-bottom:8px;">QUÉT MÃ VIETQR ĐỂ THANH TOÁN:</p>
                    <img class="kien-qr-img" src="${qrImg}" alt="Mã QR VietQR">
                    <div style="font-size:13px; line-height:1.6; text-align:left; max-width:320px; margin:0 auto;">
                        <div>• Ngân hàng: <strong>MB Bank (Quân Đội)</strong></div>
                        <div>• Số tài khoản: <strong>0987654321</strong></div>
                        <div>• Chủ TK: <strong>MAI DAM THE KIEN</strong></div>
                        <div>• Số tiền: <strong>${formatMoney(data.total)}</strong></div>
                        <div>• Nội dung CK: <strong>${data.order_code}</strong></div>
                    </div>
                </div>
            `;
        }

        var html = `
            <div class="kien-success-screen">
                <div class="kien-success-icon">✓</div>
                <h3 class="kien-success-title">Đặt hàng thành công!</h3>
                <div>Mã đơn hàng của bạn:</div>
                <div class="kien-order-code-badge">${data.order_code}</div>
                <p style="font-size:14px; color:#555; margin-bottom:12px;">
                    Cảm ơn <strong>${name}</strong> (${phone})!<br>
                    Đơn hàng đã được lưu với thời gian thực tế và sẽ sớm chuyển tới: <em>${address}</em>.
                </p>
                ${qrSection}
                <div style="margin-top:16px; display:flex; flex-direction:column; gap:8px;">
                    <button type="button" class="btn-track-my-order" data-code="${data.order_code}">
                        🔍 TRA CỨU ĐƠN HÀNG NÀY NGAY
                    </button>
                    <button type="button" class="kien-btn-confirm-order btn-finish-order" style="background:#000; padding:12px 30px;">
                        HOÀN TẤT & TIẾP TỤC MUA SẮM
                    </button>
                </div>
            </div>
        `;

        $('.kien-modal-body').html(html);
        $('.kien-modal-footer').hide();

        $(document).on('click', '.btn-finish-order', function() {
            closeCheckoutModal();
            window.location.href = (window.KienConfig && window.KienConfig.siteUrl) || '/thekien05/';
        });
    }

    // ================= ADD TO CART HOOKS =================
    
    // ================= MOBILE FAQ & ACCORDIONS =================
    function initMobileAccordions() {
        // Footer FAQ & Client Services Collapsible on mobile
        $(document).on('click', '.footer .title-menu, .footer .Collapsible__Plus, .col-footer .title-menu', function(e) {
            if ($(window).width() <= 767) {
                e.preventDefault();
                var $title = $(this).closest('.title-menu');
                var $menu = $title.siblings('.list-menu');
                
                $title.toggleClass('current');
                $menu.toggleClass('current').stop(true, true).slideToggle(250);
            }
        });

        // Product Details Tabs Accordion
        $(document).on('click', '.product-tab .tab-link h3, .product-tab .tab-link .collapse_plus', function(e) {
            if ($(window).width() <= 991) {
                e.preventDefault();
                var $li = $(this).closest('.tab-link');
                var $content = $li.find('.content_extab');
                
                $li.toggleClass('active current');
                $content.stop(true, true).slideToggle(250);
            }
        });

        $(document).on('click', '.product-tab .closes', function(e) {
            e.preventDefault();
            var $li = $(this).closest('.tab-link');
            $li.removeClass('active current');
            $li.find('.content_extab').removeClass('active').slideUp(250);
        });
    }

    $(document).ready(function() {
        initMobileAccordions();
        initCartUI();

        // 1. Hook form chi tiết sản phẩm (#add-to-cart-form)
        $('#add-to-cart-form').on('submit', function(e) {
            e.preventDefault();
            handleAddToCart(false);
        });

        // Bấm nút "THÊM VÀO GIỎ"
        $(document).on('click', '.btn_add_cart, .add_to_cart, #btn-add-cart', function(e) {
            e.preventDefault();
            handleAddToCart(false);
        });

        // Bấm nút "MUA NGAY"
        $(document).on('click', '.btn-kien-buynow, #btn-buy-now', function(e) {
            e.preventDefault();
            handleAddToCart(true); // true = mở thẳng Checkout modal
        });

        function handleAddToCart(isBuyNow) {
            var title = $('.title-product').text().trim() || $('h1').text().trim() || 'Sản phẩm Teelab Streetwear';
            
            // Lấy giá
            var priceText = $('.special-price .product-price').first().text().replace(/[^0-9]/g, '') ||
                            $('.product-price').first().text().replace(/[^0-9]/g, '') ||
                            '450000';
            var price = parseInt(priceText, 10) || 450000;

            // Lấy ảnh
            var image = $('.swiper-slide-active img').attr('src') ||
                        $('.gallery-thumbs img').first().attr('src') ||
                        $('.product-image-block img').first().attr('src') ||
                        $('img[alt="' + title + '"]').first().attr('src') ||
                        'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo.png';

            // Lấy Size & Màu
            var color = $('input[name*="option-0"]:checked').val() ||
                        $('.swatch[data-option-index="0"] .active').text().trim() ||
                        'Tiêu chuẩn';
            var size = $('input[name*="option-1"]:checked').val() ||
                       $('.swatch[data-option-index="1"] .active').text().trim() ||
                       'L';

            var qty = parseInt($('#qty').val() || 1, 10);
            if (isNaN(qty) || qty < 1) qty = 1;

            var cart = getCart();

            // Kiểm tra món đã có trong giỏ chưa
            var existingIdx = -1;
            for (var i = 0; i < cart.length; i++) {
                if (cart[i].name === title && cart[i].size === size && cart[i].color === color) {
                    existingIdx = i;
                    break;
                }
            }

            if (existingIdx > -1) {
                cart[existingIdx].quantity += qty;
            } else {
                cart.push({
                    id: Date.now(),
                    name: title,
                    image: image,
                    price: price,
                    size: size,
                    color: color,
                    quantity: qty
                });
            }

            saveCart(cart);

            if (isBuyNow) {
                openCheckoutModal();
            } else {
                showToast('Đã thêm "' + title + '" vào giỏ hàng!');
                openCartDrawer();
            }
        }
    });

})(jQuery);
