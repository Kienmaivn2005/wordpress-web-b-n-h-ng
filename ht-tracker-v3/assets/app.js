/**
 * THE KIEN - CLOTHING & STREETWEAR LAB
 * Client Engine: Fashion Catalog, Filter, Style Recommender, Cart & Checkout
 */

(function () {
  "use strict";

  // Base path for local clothing images
  const IMG_BASE = (function () {
    const scripts = document.getElementsByTagName("script");
    for (let s of scripts) {
      if (s.src && s.src.includes("/assets/app.js")) {
        return s.src.substring(0, s.src.indexOf("/assets/app.js")) + "/assets/images/";
      }
    }
    return "/thekien05/wp-content/themes/ht-tracker-v3/assets/images/";
  })();

  // 1. Streetwear & Fashion Catalog
  const PRODUCTS = [
    {
      id: 1,
      name: "Áo Thun Oversize Graphic Streetwear Black Cotton 100%",
      category: "ao-thun",
      style: "oversize",
      price: 220000,
      originalPrice: 350000,
      image: IMG_BASE + "tee-oversize-graphic-black.jpg",
      rating: 5.0,
      reviews: 148,
      sold: 86,
      stock: 100,
      badge: "Giảm 37%",
      isFlash: true,
      sizes: ["S", "M", "L", "XL", "XXL"],
      colors: ["Đen", "Trắng", "Xám"],
      desc: "Chất liệu Cotton Compact 100% định lượng 250gsm dày dặn, co giãn 2 chiều, thấm hút mồ hôi cực tốt. Họa tiết in lụa cao cấp bền màu giặt máy không nứt vỡ."
    },
    {
      id: 2,
      name: "Áo Hoodie Heavyweight Cotton Cream Boxy Fit Streetwear",
      category: "khoac-hoodie",
      style: "oversize",
      price: 380000,
      originalPrice: 550000,
      image: IMG_BASE + "hoodie-heavyweight-cream.jpg",
      rating: 4.9,
      reviews: 112,
      sold: 92,
      stock: 100,
      badge: "Bán chạy",
      isFlash: true,
      sizes: ["M", "L", "XL", "XXL"],
      colors: ["Kem", "Đen", "Nâu"],
      desc: "Chất nỉ bông ngoại định lượng 380gsm giữ ấm hoàn hảo. Mũ trùm rộng 2 lớp đứng form cá tính, túi kangaroo sâu tiện lợi."
    },
    {
      id: 3,
      name: "Áo Khoác Varsity Phối Da Cao Cấp Racing Cyber Lab",
      category: "khoac-hoodie",
      style: "y2k",
      price: 490000,
      originalPrice: 690000,
      image: IMG_BASE + "jacket-varsity-leather.jpg",
      rating: 5.0,
      reviews: 215,
      sold: 74,
      stock: 100,
      badge: "Hot Drop",
      isFlash: true,
      sizes: ["M", "L", "XL"],
      colors: ["Đen Phối Trắng", "Xanh Navy"],
      desc: "Thân dạ len phối tay da PU cao cấp không nổ, lót dù trần bông 2 lớp cách nhiệt. Logo thêu nổi 3D tỉ mỉ từng chi tiết."
    },
    {
      id: 4,
      name: "Quần Cargo Tactical Túi Hộp Đen Streetwear Kaki Co Giãn",
      category: "quan",
      style: "y2k",
      price: 320000,
      originalPrice: 450000,
      image: IMG_BASE + "pants-cargo-tactical-black.jpg",
      rating: 4.9,
      reviews: 136,
      sold: 68,
      stock: 100,
      badge: "Best Seller",
      isFlash: true,
      sizes: ["S", "M", "L", "XL"],
      colors: ["Đen", "Xám Khói"],
      desc: "Chất kaki thun co giãn nhẹ, thiết kế 6 túi hộp tiện dụng đậm chất Techwear / Cyber. Ống quần có dây rút tùy biến jogger hoặc ống suông."
    },
    {
      id: 5,
      name: "Quần Jeans Baggy Ống Rộng Lightwash Vintage Che Khuyết Điểm",
      category: "quan",
      style: "oversize",
      price: 350000,
      originalPrice: 480000,
      image: IMG_BASE + "pants-baggy-jeans-lightwash.jpg",
      rating: 4.8,
      reviews: 95,
      sold: 78,
      stock: 90,
      badge: "Trending",
      isFlash: false,
      sizes: ["S", "M", "L", "XL"],
      colors: ["Xanh Nhạt Vintage", "Xanh Đậm"],
      desc: "Chất denim dày 13oz wash màu vintage bụi bặm, form rộng thoải mái che khuyết điểm chân hoàn hảo, dễ phối đồ với mọi kiểu áo thun và sơ mi."
    },
    {
      id: 6,
      name: "Áo Polo Nam Nữ Boxy Cổ Khóa Zip Streetwear Minimalist",
      category: "ao-thun",
      style: "minimal",
      price: 260000,
      originalPrice: 380000,
      image: IMG_BASE + "polo-streetwear-boxy.jpg",
      rating: 5.0,
      reviews: 88,
      sold: 65,
      stock: 85,
      badge: "Clean Fit",
      isFlash: false,
      sizes: ["M", "L", "XL", "XXL"],
      colors: ["Đen", "Trắng", "Xám"],
      desc: "Form áo boxy ngắn trẻ trung, vải cá sấu mắt chim dệt thoi thoáng khí, cổ bẻ phối khóa kéo kim loại kim loại sáng bóng hiện đại."
    },
    {
      id: 7,
      name: "Áo Khoác Denim Rách Bụi Bặm Grunge Style Washed Blue",
      category: "khoac-hoodie",
      style: "y2k",
      price: 420000,
      originalPrice: 590000,
      image: IMG_BASE + "jacket-denim-grunge.jpg",
      rating: 4.8,
      reviews: 140,
      sold: 82,
      stock: 95,
      badge: "Grunge Y2K",
      isFlash: false,
      sizes: ["M", "L", "XL"],
      colors: ["Xanh Wash Rách", "Xám Đen"],
      desc: "Áo khoác bò denim cao cấp xử lý mài rách thủ công tạo điểm nhấn cá tính, cúc đồng đúc dập chìm thương hiệu chắc chắn."
    },
    {
      id: 8,
      name: "Nón Lưỡi Trai Dad Hat Kaki Thêu Chữ Nghệ Thuật Vintage",
      category: "phu-kien",
      style: "minimal",
      price: 119000,
      originalPrice: 165000,
      image: IMG_BASE + "hat-dadhat-vintage-embroidery.jpg",
      rating: 4.9,
      reviews: 172,
      sold: 145,
      stock: 150,
      badge: "Phụ kiện Hot",
      isFlash: false,
      sizes: ["Freesize"],
      colors: ["Đen Washed", "Nâu Tây", "Xanh Rêu"],
      desc: "Mũ dad hat phom mềm không gãy gập, chất vải kaki 100% cotton mộc mạc, thêu chữ typography tinh tế, dễ dàng phối với mọi kiểu trang phục."
    },
    {
      id: 9,
      name: "Giày Sneaker Chunky Sole Độn Đế Streetwear Sole 5cm",
      category: "giay",
      style: "oversize",
      price: 450000,
      originalPrice: 650000,
      image: IMG_BASE + "shoes-sneaker-chunky-dad.jpg",
      rating: 5.0,
      reviews: 180,
      sold: 94,
      stock: 100,
      badge: "Tôn Dáng",
      isFlash: false,
      sizes: ["39", "40", "41", "42", "43"],
      colors: ["Trắng Ngà", "Đen Phối Trắng"],
      desc: "Đế đệm cao su đúc nguyên khối êm ái tăng chiều cao 5cm tự nhiên, thân giày da vi sợi phối lưới thoáng khí êm chân cả ngày dài."
    },
    {
      id: 10,
      name: "Túi Đeo Chéo Canvas Mini Crossbody Bag Tactical Street",
      category: "phu-kien",
      style: "y2k",
      price: 180000,
      originalPrice: 250000,
      image: IMG_BASE + "bag-crossbody-streetwear.jpg",
      rating: 4.9,
      reviews: 84,
      sold: 72,
      stock: 80,
      badge: "Tiện dụng",
      isFlash: false,
      sizes: ["One Size"],
      colors: ["Đen", "Xám Quân Đội"],
      desc: "Vải Oxford trượt nước chống bám bụi, 3 ngăn khóa kéo rộng rãi đựng vừa điện thoại, ví tiền, sạc dự phòng gọn gàng."
    },
    {
      id: 11,
      name: "Áo Thun Acid Washed Vintage Xám Tiêu Streetwear Unisex",
      category: "ao-thun",
      style: "oversize",
      price: 240000,
      originalPrice: 350000,
      image: IMG_BASE + "tee-vintage-washed-grey.jpg",
      rating: 4.9,
      reviews: 130,
      sold: 89,
      stock: 100,
      badge: "Washed Tee",
      isFlash: false,
      sizes: ["S", "M", "L", "XL"],
      colors: ["Xám Tiêu Washed"],
      desc: "Xử lý wash màu thủ công tạo hiệu ứng loang vintage cổ điển, cổ áo bo dệt sợi spandex dày không bai dão sau hàng chục lần giặt."
    },
    {
      id: 12,
      name: "Mũ Bucket Tai Bèo Dây Rút Chống Nước Safari Tactical",
      category: "phu-kien",
      style: "y2k",
      price: 139000,
      originalPrice: 195000,
      image: IMG_BASE + "hat-bucket-safari-tactical.jpg",
      rating: 4.8,
      reviews: 64,
      sold: 58,
      stock: 70,
      badge: "Outdoor",
      isFlash: false,
      sizes: ["Freesize"],
      colors: ["Đen", "Be Vàng", "Rêu"],
      desc: "Vải polyester dù trượt nước chuyên dụng, vành nón rộng che nắng tốt có dây rút cố định tiện lợi khi đi phượt dạo phố."
    }
  ];

  // 2. Cart LocalStorage
  let cart = [];
  try {
    const saved = localStorage.getItem("thekien_fashion_cart");
    if (saved) cart = JSON.parse(saved);
  } catch (e) {
    cart = [];
  }

  function saveCart() {
    try {
      localStorage.setItem("thekien_fashion_cart", JSON.stringify(cart));
    } catch (e) {}
    renderCart();
  }

  function formatMoney(amount) {
    return Number(amount || 0).toLocaleString("vi-VN") + "đ";
  }

  // 3. Render Product Card HTML
  function createProductCardHTML(p) {
    const detailUrl = window.location.origin + window.location.pathname + `?product_id=${p.id}`;
    return `
      <div class="tk-product-card" data-id="${p.id}" data-cat="${p.category}" data-style="${p.style}">
        <div class="tk-card-img-wrap" onclick="window.location.href='${detailUrl}'">
          <span class="tk-tag-sale">${p.badge || 'Sale'}</span>
          <span class="tk-tag-badge"><i class="fa-solid fa-shirt"></i> Chuẩn Form</span>
          <img src="${p.image}" alt="${p.name}" loading="lazy">
        </div>
        <div class="tk-card-body">
          <span class="tk-card-cat">${getCategoryName(p.category)}</span>
          <a href="${detailUrl}" class="tk-card-title" title="${p.name}">${p.name}</a>
          <div class="tk-card-meta">
            <span class="tk-stars">★★★★★</span>
            <span>(${p.reviews} review)</span>
            <span style="margin-left: auto;">Đã bán ${p.sold}</span>
          </div>
          ${p.isFlash ? `
            <div class="tk-stock-bar">
              <div class="tk-stock-fill" style="width: ${(p.sold / p.stock) * 100}%"></div>
              <span class="tk-stock-text"><i class="fa-solid fa-fire"></i> Đã bán ${p.sold}/${p.stock} cái</span>
            </div>
          ` : ''}
          <div class="tk-card-price-row">
            <span class="tk-price-current">${formatMoney(p.price)}</span>
            <span class="tk-price-old">${formatMoney(p.originalPrice)}</span>
          </div>
          <button type="button" class="btn-card-buy" data-add="${p.id}">
            <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ
          </button>
        </div>
      </div>
    `;
  }

  function getCategoryName(cat) {
    switch (cat) {
      case "ao-thun": return "Áo Thun & Polo";
      case "khoac-hoodie": return "Hoodie & Áo Khoác";
      case "quan": return "Quần Jeans & Cargo";
      case "phu-kien": return "Mũ & Phụ Kiện";
      case "giay": return "Sneaker & Giày";
      default: return "Thời Trang Streetwear";
    }
  }

  // 4. Render Main Grids
  function renderProducts(filterCat = "all", filterStyle = "all", query = "") {
    const mainGrid = document.getElementById("mainProductsGrid");
    const countLabel = document.getElementById("productResultCount");
    if (!mainGrid) return;

    let filtered = PRODUCTS.filter((p) => {
      let matchCat = filterCat === "all" || p.category === filterCat;
      let matchStyle = filterStyle === "all" || p.style === filterStyle;
      let matchQuery = true;
      if (query && query.trim() !== "") {
        const q = query.toLowerCase().trim();
        matchQuery = p.name.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q);
      }
      return matchCat && matchStyle && matchQuery;
    });

    if (countLabel) {
      countLabel.textContent = `Hiển thị ${filtered.length} sản phẩm thời trang`;
    }

    if (filtered.length === 0) {
      mainGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; color: #64748b;">
          <i class="fa-solid fa-magnifying-glass" style="font-size: 36px; margin-bottom: 12px; color: var(--accent-crimson);"></i>
          <p style="font-size: 15px; font-weight: 800; color: var(--primary-noir);">Không tìm thấy sản phẩm thời trang phù hợp với từ khóa "${query}".</p>
          <button type="button" class="btn-tk-secondary" style="margin-top: 10px;" id="btnResetSearch">Xem tất cả bộ sưu tập</button>
        </div>
      `;
      const btnReset = document.getElementById("btnResetSearch");
      if (btnReset) {
        btnReset.addEventListener("click", () => {
          document.getElementById("liveSearchInput").value = "";
          renderProducts("all", "all", "");
        });
      }
      return;
    }

    mainGrid.innerHTML = filtered.map(createProductCardHTML).join("");
    bindAddToCartButtons();
  }

  function renderFlashSale() {
    const flashGrid = document.getElementById("flashsaleGrid");
    if (!flashGrid) return;
    const flashItems = PRODUCTS.filter((p) => p.isFlash);
    flashGrid.innerHTML = flashItems.map(createProductCardHTML).join("");
    bindAddToCartButtons();
  }

  // 5. Add to Cart Handling
  function bindAddToCartButtons() {
    document.querySelectorAll("[data-add]").forEach((btn) => {
      btn.onclick = function (e) {
        e.stopPropagation();
        const id = parseInt(this.getAttribute("data-add"), 10);
        addToCart(id);
      };
    });
  }

  function addToCart(productId) {
    const item = PRODUCTS.find((p) => p.id === productId);
    if (!item) return;

    const existing = cart.find((c) => c.id === productId);
    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({
        id: item.id,
        name: item.name,
        price: item.price,
        image: item.image,
        quantity: 1,
        size: "L"
      });
    }

    saveCart();
    showToast(`Đã thêm "${item.name.substring(0, 30)}..." vào giỏ!`);
    openCartDrawer();
  }

  function updateQuantity(productId, delta) {
    const existing = cart.find((c) => c.id === productId);
    if (!existing) return;
    existing.quantity += delta;
    if (existing.quantity <= 0) {
      cart = cart.filter((c) => c.id !== productId);
    }
    saveCart();
  }

  function removeFromCart(productId) {
    cart = cart.filter((c) => c.id !== productId);
    saveCart();
    showToast("Đã xóa sản phẩm khỏi giỏ hàng");
  }

  // 6. Render Cart Drawer
  function renderCart() {
    const body = document.getElementById("cartDrawerBody");
    const footer = document.getElementById("cartDrawerFooter");
    const headerCount = document.getElementById("headerCartCount");
    const mbCount = document.getElementById("mbCartCount");
    const drawerCount = document.getElementById("cartDrawerCount");
    const subtotalText = document.getElementById("cartSubtotalAmount");

    const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
    const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

    if (headerCount) headerCount.textContent = totalQty;
    if (mbCount) mbCount.textContent = totalQty;
    if (drawerCount) drawerCount.textContent = totalQty;
    if (subtotalText) subtotalText.textContent = formatMoney(subtotal);

    if (!body) return;

    if (cart.length === 0) {
      body.innerHTML = `
        <div class="tk-cart-empty">
          <i class="fa-solid fa-bag-shopping"></i>
          <p>Chưa có sản phẩm nào trong giỏ hàng</p>
          <button type="button" class="btn-tk-primary" style="margin-top: 14px;" onclick="window.closeCartDrawer();">
            Mua Sắm Ngay
          </button>
        </div>
      `;
      if (footer) footer.style.display = "none";
      return;
    }

    if (footer) footer.style.display = "block";

    body.innerHTML = cart
      .map(
        (c) => `
        <div class="tk-cart-item">
          <img src="${c.image}" alt="${c.name}" class="tk-cart-thumb">
          <div class="tk-cart-item-info">
            <div class="tk-cart-item-name">${c.name}</div>
            <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; margin-bottom: 2px;">Size: L • Đen</div>
            <div class="tk-cart-item-price">${formatMoney(c.price)}</div>
            <div class="tk-cart-qty">
              <button type="button" class="tk-qty-btn" onclick="window.updateCartQty(${c.id}, -1)">-</button>
              <span style="font-weight: 800; font-size: 13px; min-width: 20px; text-align: center;">${c.quantity}</span>
              <button type="button" class="tk-qty-btn" onclick="window.updateCartQty(${c.id}, 1)">+</button>
            </div>
          </div>
          <button type="button" class="tk-cart-remove" onclick="window.removeCartItem(${c.id})" title="Xóa">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
      `
      )
      .join("");
  }

  // 7. Cart Drawer Open/Close
  function openCartDrawer() {
    const drawer = document.getElementById("cartDrawer");
    const overlay = document.getElementById("cartOverlay");
    if (drawer) drawer.classList.add("active");
    if (overlay) overlay.classList.add("active");
  }

  function closeCartDrawer() {
    const drawer = document.getElementById("cartDrawer");
    const overlay = document.getElementById("cartOverlay");
    if (drawer) drawer.classList.remove("active");
    if (overlay) overlay.classList.remove("active");
  }

  // 8. Toast Notifications
  function showToast(msg) {
    const toast = document.getElementById("toastNotice");
    const msgEl = document.getElementById("toastMessage");
    if (!toast || !msgEl) return;
    msgEl.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => {
      toast.classList.remove("show");
    }, 2800);
  }

  // 9. Countdown Timer
  function initCountdown() {
    let secondsLeft = 6 * 3600 + 28 * 60 + 45;
    const hEl = document.getElementById("timerHours");
    const mEl = document.getElementById("timerMinutes");
    const sEl = document.getElementById("timerSeconds");

    setInterval(() => {
      if (secondsLeft <= 0) secondsLeft = 24 * 3600;
      secondsLeft--;
      const h = Math.floor(secondsLeft / 3600);
      const m = Math.floor((secondsLeft % 3600) / 60);
      const s = secondsLeft % 60;
      if (hEl) hEl.textContent = String(h).padStart(2, "0");
      if (mEl) mEl.textContent = String(m).padStart(2, "0");
      if (sEl) sEl.textContent = String(s).padStart(2, "0");
    }, 1000);
  }

  // 10. Place Order Quick Checkout
  function initOrderForm() {
    const btnPlace = document.getElementById("btnPlaceOrder");
    if (!btnPlace) return;

    btnPlace.addEventListener("click", () => {
      const name = document.getElementById("orderCustomerName")?.value?.trim();
      const phone = document.getElementById("orderCustomerPhone")?.value?.trim();
      const addr = document.getElementById("orderCustomerAddress")?.value?.trim();

      if (!name) {
        alert("Vui lòng nhập Họ và tên của bạn để đặt hàng!");
        return;
      }
      if (!phone || phone.length < 9) {
        alert("Vui lòng nhập Số điện thoại nhận hàng hợp lệ!");
        return;
      }
      if (!addr) {
        alert("Vui lòng nhập Địa chỉ giao hàng!");
        return;
      }

      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

      btnPlace.disabled = true;
      btnPlace.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang Xử Lý Đơn Hàng...';

      setTimeout(() => {
        alert(`🎉 ĐẶT HÀNG THỜI TRANG THÀNH CÔNG!\n\nCảm ơn bạn ${name} đã lựa chọn THE KIEN Studio.\nĐơn hàng trị giá ${formatMoney(total)} sẽ được nhân viên liên hệ xác nhận size số tại ${phone} trong 15 phút.\n\nMiễn phí vận chuyển & Đổi size tận nhà!`);
        cart = [];
        saveCart();
        closeCartDrawer();
        btnPlace.disabled = false;
        btnPlace.innerHTML = '<i class="fa-solid fa-circle-check"></i> XÁC NHẬN ĐẶT HÀNG NGAY';
      }, 900);
    });
  }

  // Global window bindings
  window.updateCartQty = updateQuantity;
  window.removeCartItem = removeFromCart;
  window.closeCartDrawer = closeCartDrawer;
  window.addToCart = addToCart;

  // 11. DOM Ready
  document.addEventListener("DOMContentLoaded", () => {
    renderFlashSale();
    renderProducts("all", "all", "");
    renderCart();
    initCountdown();
    initOrderForm();

    // Cart Buttons
    const openBtn = document.getElementById("cartOpenBtn");
    const mbOpenBtn = document.getElementById("mbCartOpenBtn");
    const closeBtn = document.getElementById("cartCloseBtn");
    const overlay = document.getElementById("cartOverlay");

    if (openBtn) openBtn.onclick = openCartDrawer;
    if (mbOpenBtn) mbOpenBtn.onclick = openCartDrawer;
    if (closeBtn) closeBtn.onclick = closeCartDrawer;
    if (overlay) overlay.onclick = closeCartDrawer;

    // Filter Tabs
    let currentCat = "all";
    let currentStyle = "all";

    document.querySelectorAll(".tk-filter-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        document.querySelectorAll(".tk-filter-btn").forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
        currentCat = this.getAttribute("data-filter") || "all";
        renderProducts(currentCat, currentStyle, document.getElementById("liveSearchInput")?.value || "");
      });
    });

    // Style Chips (AI Mix đồ)
    document.querySelectorAll(".tk-chip").forEach((chip) => {
      chip.addEventListener("click", function () {
        document.querySelectorAll(".tk-chip").forEach((c) => c.classList.remove("active"));
        this.classList.add("active");
        currentStyle = this.getAttribute("data-style") || "all";
        renderProducts(currentCat, currentStyle, document.getElementById("liveSearchInput")?.value || "");
      });
    });

    // Category Quick Cards
    document.querySelectorAll(".tk-cat-card").forEach((card) => {
      card.addEventListener("click", function () {
        const cat = this.getAttribute("data-cat");
        if (!cat) return;
        currentCat = cat;
        document.querySelectorAll(".tk-filter-btn").forEach((b) => {
          b.classList.toggle("active", b.getAttribute("data-filter") === cat);
        });
        renderProducts(currentCat, currentStyle, "");
        const target = document.getElementById("section-products");
        if (target) target.scrollIntoView({ behavior: "smooth" });
      });
    });

    // Live Search
    const searchInput = document.getElementById("liveSearchInput");
    if (searchInput) {
      searchInput.addEventListener("input", function () {
        renderProducts(currentCat, currentStyle, this.value);
      });
    }

    // Back to top
    const backTop = document.getElementById("backToTopBtn");
    if (backTop) {
      backTop.addEventListener("click", (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
    }

    // Nav Bar Category Links
    document.querySelectorAll(".tk-nav-item a[data-category]").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const cat = this.getAttribute("data-category");
        currentCat = cat;
        document.querySelectorAll(".tk-filter-btn").forEach((b) => {
          b.classList.toggle("active", b.getAttribute("data-filter") === cat);
        });
        renderProducts(currentCat, currentStyle, "");
        const target = document.getElementById("section-products");
        if (target) target.scrollIntoView({ behavior: "smooth" });
      });
    });
  });
})();
