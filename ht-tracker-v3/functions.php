<?php
if (!defined('ABSPATH')) exit;

// Cấu hình múi giờ thực tế Việt Nam (GMT+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Theme setup
// Helper lấy thời gian thực hiện tại theo chuẩn Việt Nam (GMT+7)
function kien_get_vietnam_time() {
    $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
    $dt = new DateTime('now', $tz);
    return $dt->format('Y-m-d H:i:s');
}

function kien_fashion_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
}
add_action('after_setup_theme', 'kien_fashion_setup');

// Database Tables Installer
function kien_fashion_install() {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $charset = $wpdb->get_charset_collate();

    $t_products = $wpdb->prefix . 'kien_products';
    $t_orders = $wpdb->prefix . 'kien_orders';
    $t_order_items = $wpdb->prefix . 'kien_order_items';
    $t_settings = $wpdb->prefix . 'kien_settings';

    $sql_products = "CREATE TABLE {$t_products} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        slug varchar(255) NOT NULL,
        category varchar(100) NOT NULL DEFAULT 'Áo thun',
        price decimal(12,0) NOT NULL DEFAULT 0,
        sale_price decimal(12,0) NULL DEFAULT 0,
        image text NOT NULL,
        gallery text NULL,
        description text NULL,
        sizes varchar(100) NOT NULL DEFAULT 'S,M,L,XL',
        colors varchar(255) NOT NULL DEFAULT 'Đen,Trắng',
        stock int(11) NOT NULL DEFAULT 100,
        sold int(11) NOT NULL DEFAULT 0,
        rating decimal(3,1) NOT NULL DEFAULT 5.0,
        badge varchar(50) NULL DEFAULT '',
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY (id),
        KEY category (category),
        KEY slug (slug)
    ) {$charset};";

    $sql_orders = "CREATE TABLE {$t_orders} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        order_code varchar(50) NOT NULL,
        customer_name varchar(255) NOT NULL,
        customer_phone varchar(50) NOT NULL,
        customer_email varchar(255) NULL,
        customer_address text NOT NULL,
        payment_method varchar(50) NOT NULL DEFAULT 'cod',
        order_status varchar(50) NOT NULL DEFAULT 'pending',
        subtotal decimal(12,0) NOT NULL DEFAULT 0,
        discount decimal(12,0) NOT NULL DEFAULT 0,
        shipping_fee decimal(12,0) NOT NULL DEFAULT 0,
        total decimal(12,0) NOT NULL DEFAULT 0,
        notes text NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY order_code (order_code),
        KEY customer_phone (customer_phone),
        KEY order_status (order_status)
    ) {$charset};";

    $sql_order_items = "CREATE TABLE {$t_order_items} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        order_id bigint(20) unsigned NOT NULL,
        product_id bigint(20) unsigned NOT NULL,
        product_name varchar(255) NOT NULL,
        product_image text NULL,
        size varchar(50) NULL,
        color varchar(50) NULL,
        quantity int(11) NOT NULL DEFAULT 1,
        price decimal(12,0) NOT NULL DEFAULT 0,
        total decimal(12,0) NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        KEY order_id (order_id)
    ) {$charset};";

    $sql_settings = "CREATE TABLE {$t_settings} (
        setting_key varchar(100) NOT NULL,
        setting_value longtext NULL,
        PRIMARY KEY (setting_key)
    ) {$charset};";

    dbDelta($sql_products);
    dbDelta($sql_orders);
    dbDelta($sql_order_items);
    dbDelta($sql_settings);
}
add_action('after_switch_theme', 'kien_fashion_install');
add_action('init', 'kien_fashion_install', 1);

// Seed Products and Settings
function kien_fashion_seed() {
    global $wpdb;
    $t_products = $wpdb->prefix . 'kien_products';
    $count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$t_products}");
    if ($count > 0) return;

    $now = current_time('mysql');
    $sample_products = array(
        array(
            'name' => 'Áo Thun Unisex Cotton 100% Streetwear Form Rộng',
            'slug' => 'ao-thun-unisex-cotton-streetwear',
            'category' => 'Áo thun & Polo',
            'price' => 280000,
            'sale_price' => 199000,
            'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo thun unisex chất liệu 100% cotton định lượng 250gsm dày dặn, thấm hút mồ hôi cực tốt. Thiết kế form oversize phong cách Hàn Quốc trẻ trung năng động.',
            'sizes' => 'S,M,L,XL,XXL',
            'colors' => 'Đen,Trắng,Xám Tiêu,Be',
            'stock' => 150,
            'sold' => 842,
            'rating' => 4.9,
            'badge' => 'Bán chạy'
        ),
        array(
            'name' => 'Áo Sơ Mi Nam Oxford Premium Chống Nhăn Cao Cấp',
            'slug' => 'ao-so-mi-nam-oxford-premium',
            'category' => 'Sơ mi công sở',
            'price' => 450000,
            'sale_price' => 349000,
            'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo sơ mi nam vải Oxford dệt thoi cao cấp, không nhăn sau khi giặt, form slim-fit tôn dáng lịch lãm cho môi trường công sở hoặc dạo phố.',
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Trắng Tinh,Xanh Nhạt,Xanh Navy,Hồng Nhạt',
            'stock' => 90,
            'sold' => 520,
            'rating' => 4.8,
            'badge' => 'Hot'
        ),
        array(
            'name' => 'Áo Khoác Blazer Nữ 2 Lớp Phong Cách Hàn Quốc',
            'slug' => 'ao-khoac-blazer-nu-2-lop',
            'category' => 'Áo khoác & Blazer',
            'price' => 690000,
            'sale_price' => 499000,
            'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo khoác blazer nữ may 2 lớp lót lụa mềm mại, đứng form, tone màu pastel thanh lịch dễ phối đồ đi làm, đi tiệc sang trọng.',
            'sizes' => 'S,M,L',
            'colors' => 'Be Nude,Đen Huyền Bí,Nâu Tây',
            'stock' => 65,
            'sold' => 310,
            'rating' => 5.0,
            'badge' => 'Sale -28%'
        ),
        array(
            'name' => 'Áo Hoodie Streetwear Nỉ Bông Dày Dặn Unisex',
            'slug' => 'ao-hoodie-streetwear-ni-bong',
            'category' => 'Áo khoác & Blazer',
            'price' => 420000,
            'sale_price' => 299000,
            'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo hoodie unisex nỉ bông ngoại định lượng 380gsm giữ ấm hoàn hảo cho mùa đông. Mũ trùm rộng 2 lớp, túi kangaroo tiện lợi.',
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Xám Melange,Đen,Xanh Rêu,Nâu Đất',
            'stock' => 120,
            'sold' => 670,
            'rating' => 4.9,
            'badge' => 'Mùa Đông'
        ),
        array(
            'name' => 'Quần Jeans Ống Rộng Wide-Leg Vintage Nam Nữ',
            'slug' => 'quan-jeans-ong-rong-wide-leg',
            'category' => 'Quần jeans & Kaki',
            'price' => 480000,
            'sale_price' => 359000,
            'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&auto=format&fit=crop&q=80',
            'description' => 'Quần bò ống suông rộng chất denim dày 13oz bền bỉ, wash màu vintage bụi bặm cá tính, che khuyết điểm chân tuyệt vời.',
            'sizes' => 'S,M,L,XL',
            'colors' => 'Xanh Nhạt Vintage,Xanh Đậm,Đen Wash',
            'stock' => 85,
            'sold' => 430,
            'rating' => 4.7,
            'badge' => 'Xu hướng'
        ),
        array(
            'name' => 'Váy Hoa Nhí Vintage Dáng Xòe Tiểu Thư',
            'slug' => 'vay-hoa-nhi-vintage-dang-xoe',
            'category' => 'Váy & Đầm nữ',
            'price' => 420000,
            'sale_price' => 319000,
            'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80',
            'description' => 'Đầm hoa nhí chất voan tơ cao cấp 2 lớp mềm mại bay bổng. Cổ vuông phối nơ nhẹ nhàng, thắt eo tôn dáng hoàn hảo cho dạo phố chụp ảnh.',
            'sizes' => 'S,M,L',
            'colors' => 'Hoa Vàng Nhạt,Hoa Xanh Pastel,Hoa Trắng',
            'stock' => 70,
            'sold' => 285,
            'rating' => 4.9,
            'badge' => 'Mới về'
        ),
        array(
            'name' => 'Áo Polo Nam Thể Thao Pique Co Giãn Thoáng Khí',
            'slug' => 'ao-polo-nam-the-thao-pique',
            'category' => 'Áo thun & Polo',
            'price' => 320000,
            'sale_price' => 249000,
            'image' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo polo nam dệt tổ ong pique thoáng mát, co giãn 4 chiều kháng khuẩn chống nhăn. Cổ áo dệt bo dệt tinh xảo, lịch sự năng động.',
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Trắng,Đen,Xanh Đen,Đỏ Đô',
            'stock' => 110,
            'sold' => 590,
            'rating' => 4.8,
            'badge' => 'Best-seller'
        ),
        array(
            'name' => 'Quần Tây Nam Slimfit Co Giãn Công Sở Cao Cấp',
            'slug' => 'quan-tay-nam-slimfit-co-gian',
            'category' => 'Quần jeans & Kaki',
            'price' => 450000,
            'sale_price' => 329000,
            'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&auto=format&fit=crop&q=80',
            'description' => 'Quần âu nam vải tuyết mưa nhập khẩu mềm mịn, không bám bụi, co giãn nhẹ. Thiết kế lưng chun ẩn co giãn 4cm cực kỳ thoải mái khi ngồi làm việc.',
            'sizes' => '29,30,31,32,34',
            'colors' => 'Đen,Xám Đậm,Xanh Navy,Ghi Sáng',
            'stock' => 95,
            'sold' => 460,
            'rating' => 4.9,
            'badge' => 'Công sở'
        ),
        array(
            'name' => 'Đầm Dự Tiệc Body Sang Trọng Quyến Rũ',
            'slug' => 'dam-du-tiec-body-sang-trong',
            'category' => 'Váy & Đầm nữ',
            'price' => 750000,
            'sale_price' => 550000,
            'image' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&auto=format&fit=crop&q=80',
            'description' => 'Đầm bodycon chất thun kim tuyến cao cấp ôm sát đường cong cơ thể, xẻ tà tinh tế quyến rũ cho các buổi tiệc dạ hội và sự kiện quan trọng.',
            'sizes' => 'S,M,L',
            'colors' => 'Đen Huyền Bí,Đỏ Rượu Vang,Bạc Ánh Kim',
            'stock' => 40,
            'sold' => 150,
            'rating' => 5.0,
            'badge' => 'Cao cấp'
        ),
        array(
            'name' => 'Áo Sơ Mi Nữ Lụa Satin Cổ Nơ Phong Cách Pháp',
            'slug' => 'ao-so-mi-nu-lua-satin-co-no',
            'category' => 'Sơ mi công sở',
            'price' => 380000,
            'sale_price' => 289000,
            'image' => 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?w=800&auto=format&fit=crop&q=80',
            'description' => 'Áo sơ mi nữ chất lụa satin mượt mà óng ả, nhẹ thoáng không gây bí bách. Điểm nhấn nơ cổ duyên dáng phong cách thời trang quý cô thanh lịch.',
            'sizes' => 'S,M,L',
            'colors' => 'Trắng Kem,Hồng Pastel,Xanh Bạc Hà',
            'stock' => 80,
            'sold' => 340,
            'rating' => 4.8,
            'badge' => 'Hot'
        ),
        array(
            'name' => 'Túi Xách Da Đeo Chéo Thời Trang Minimalist',
            'slug' => 'tui-xach-da-deo-cheo-minimalist',
            'category' => 'Phụ kiện thời trang',
            'price' => 390000,
            'sale_price' => 269000,
            'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80',
            'description' => 'Túi xách da PU cao cấp chống xước chống thấm nước. Form hộp cứng cáp, khóa kim loại mạ vàng sang trọng, dây đeo điều chỉnh linh hoạt.',
            'sizes' => 'Freesize',
            'colors' => 'Đen,Nâu Da Bò,Trắng Sữa',
            'stock' => 100,
            'sold' => 510,
            'rating' => 4.9,
            'badge' => 'Phụ kiện'
        ),
        array(
            'name' => 'Mũ Lưỡi Trai Classic Unisex Thêu Chữ Nổi',
            'slug' => 'mu-luoi-trai-classic-unisex',
            'category' => 'Phụ kiện thời trang',
            'price' => 180000,
            'sale_price' => 129000,
            'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=80',
            'description' => 'Nón lưỡi trai chất cotton dệt chéo dày dặn thấm hút tốt. Thêu logo 3D sắc nét, khóa kim loại phía sau dễ dàng điều chỉnh kích cỡ vòng đầu.',
            'sizes' => 'Freesize',
            'colors' => 'Đen,Be,Xanh Rêu,Nâu Khói',
            'stock' => 150,
            'sold' => 890,
            'rating' => 4.9,
            'badge' => 'Phụ kiện'
        )
    );

    foreach ($sample_products as $p) {
        $wpdb->insert($t_products, array(
            'name' => $p['name'],
            'slug' => $p['slug'],
            'category' => $p['category'],
            'price' => $p['price'],
            'sale_price' => $p['sale_price'],
            'image' => $p['image'],
            'description' => $p['description'],
            'sizes' => $p['sizes'],
            'colors' => $p['colors'],
            'stock' => $p['stock'],
            'sold' => $p['sold'],
            'rating' => $p['rating'],
            'badge' => $p['badge'],
            'created_at' => $now,
            'updated_at' => $now
        ));
    }

    $t_settings = $wpdb->prefix . 'kien_settings';
    $defaults = array(
        'shop_name' => 'THEKIEN FASHION STORE',
        'shop_tagline' => 'Thời trang & Phong cách hiện đại - Trợ lý AI Stylist 24/7',
        'student_info' => 'Mai Đàm Thế Kiên - MSSV: 23103035 - Khoa KHTN&CN - ĐH Tây Nguyên',
        'shop_phone' => '0987.654.321',
        'shop_email' => 'thekien05@fashion.edu.vn',
        'shop_address' => '567 Lê Duẩn, TP. Buôn Ma Thuột, Đắk Lắk',
        'bank_name' => 'MB Bank (Ngân Hàng Quân Đội)',
        'bank_account' => '0987654321',
        'bank_holder' => 'MAI DAM THE KIEN',
        'bank_bin' => '970422',
        'gemini_api_key' => '',
        'bot_welcome' => 'Xin chào! Em là Kiên AI - Trợ lý Stylist ảo của TheKien Fashion. Anh/chị cần tư vấn size, phối đồ hay tìm mẫu nào đẹp hôm nay ạ?'
    );

    foreach ($defaults as $k => $v) {
        $wpdb->replace($t_settings, array('setting_key' => $k, 'setting_value' => $v));
    }
}
add_action('after_switch_theme', 'kien_fashion_seed');
add_action('init', 'kien_fashion_seed', 2);

// Enqueue Assets
function kien_fashion_assets() {
    if (is_front_page() || is_home() || isset($_GET['product_slug']) || isset($_GET['product_id']) || isset($_GET['sp'])) {
        return;
    }
    wp_enqueue_style('kien-google-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap', array(), null);
    wp_enqueue_style('kien-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_script('kien-app', get_template_directory_uri() . '/assets/app.js', array(), filemtime(get_template_directory() . '/assets/app.js'), true);

    global $wpdb;
    $t_settings = $wpdb->prefix . 'kien_settings';
    $settings_rows = $wpdb->get_results("SELECT setting_key, setting_value FROM {$t_settings}", ARRAY_A);
    $settings = array();
    foreach ($settings_rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    $is_admin = current_user_can('manage_options');
    $user = wp_get_current_user();

    wp_localize_script('kien-app', 'KienShop', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kien_shop_nonce'),
        'isAdmin' => $is_admin,
        'user' => array(
            'isLoggedIn' => is_user_logged_in(),
            'id' => get_current_user_id(),
            'name' => is_user_logged_in() ? $user->display_name : '',
            'email' => is_user_logged_in() ? $user->user_email : ''
        ),
        'settings' => $settings,
        'siteUrl' => home_url('/'),
        'initialProducts' => $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kien_products ORDER BY id DESC LIMIT 150", ARRAY_A)
    ));
}
add_action('wp_enqueue_scripts', 'kien_fashion_assets');

// Standalone Product URL Routing & Rewrite Rules
function kien_custom_query_vars($vars) {
    $vars[] = 'product_id';
    $vars[] = 'sp';
    $vars[] = 'kien_product';
    return $vars;
}
add_filter('query_vars', 'kien_custom_query_vars');

function kien_add_rewrite_rules() {
    add_rewrite_rule('^san-pham/([0-9]+)/?$', 'index.php?product_id=$matches[1]', 'top');
    add_rewrite_rule('^san-pham/([^/]+)/?$', 'index.php?sp=$matches[1]', 'top');
}
add_action('init', 'kien_add_rewrite_rules');

function kien_template_routing($template) {
    $has_product = false;
    if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
        $has_product = true;
    } elseif (isset($_GET['id']) && is_numeric($_GET['id']) && !is_admin()) {
        $has_product = true;
    } elseif (isset($_GET['sp'])) {
        $has_product = true;
    } elseif (isset($_GET['product_slug'])) {
        $has_product = true;
    } elseif (get_query_var('product_id') || get_query_var('sp') || get_query_var('kien_product')) {
        $has_product = true;
    }

    if ($has_product) {
        $single = get_template_directory() . '/single-product.php';
        if (file_exists($single)) {
            return $single;
        }
    }
    return $template;
}
add_filter('template_include', 'kien_template_routing', 99);

// Helper Security Guard
function kien_ajax_check() {
    if (!empty($_REQUEST['nonce'])) {
        check_ajax_referer('kien_shop_nonce', 'nonce');
    }
}
function kien_admin_check() {
    kien_ajax_check();
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền quản trị.'), 403);
    }
}

// 1. API: Get Products
function kien_get_products() {
    kien_ajax_check();
    global $wpdb;
    $table = $wpdb->prefix . 'kien_products';

    $category = sanitize_text_field($_POST['category'] ?? '');
    $search = sanitize_text_field($_POST['search'] ?? '');
    $sort = sanitize_text_field($_POST['sort'] ?? 'popular');
    $badge = sanitize_text_field($_POST['badge'] ?? '');
    $color = sanitize_text_field($_POST['color'] ?? '');
    $size = sanitize_text_field($_POST['size'] ?? '');
    $min_price = isset($_POST['min_price']) && is_numeric($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
    $max_price = isset($_POST['max_price']) && is_numeric($_POST['max_price']) ? floatval($_POST['max_price']) : 0;

    $where = array('1=1');
    $params = array();

    if (!empty($category) && $category !== 'all') {
        $where[] = 'category = %s';
        $params[] = $category;
    }

    if (!empty($badge) && $badge !== 'all') {
        $where[] = 'badge LIKE %s';
        $params[] = '%' . $wpdb->esc_like($badge) . '%';
    }

    if (!empty($color) && $color !== 'all') {
        $where[] = 'colors LIKE %s';
        $params[] = '%' . $wpdb->esc_like($color) . '%';
    }

    if (!empty($size) && $size !== 'all') {
        $where[] = 'sizes LIKE %s';
        $params[] = '%' . $wpdb->esc_like($size) . '%';
    }

    if (!empty($search)) {
        $where[] = '(name LIKE %s OR description LIKE %s OR category LIKE %s)';
        $s = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $s;
        $params[] = $s;
        $params[] = $s;
    }

    if ($min_price > 0) {
        $where[] = 'COALESCE(NULLIF(sale_price, 0), price) >= %f';
        $params[] = $min_price;
    }
    if ($max_price > 0) {
        $where[] = 'COALESCE(NULLIF(sale_price, 0), price) <= %f';
        $params[] = $max_price;
    }

    $order_by = 'sold DESC, id DESC';
    if ($sort === 'newest') $order_by = 'id DESC';
    elseif ($sort === 'price_asc') $order_by = 'COALESCE(NULLIF(sale_price, 0), price) ASC';
    elseif ($sort === 'price_desc') $order_by = 'COALESCE(NULLIF(sale_price, 0), price) DESC';
    elseif ($sort === 'rating') $order_by = 'rating DESC, sold DESC';

    $where_sql = implode(' AND ', $where);
    $query = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_by}";

    if (!empty($params)) {
        $sql = $wpdb->prepare($query, $params);
    } else {
        $sql = $query;
    }

    $products = $wpdb->get_results($sql, ARRAY_A);
    $categories = $wpdb->get_col("SELECT DISTINCT category FROM {$table} ORDER BY category ASC");

    // All available colors & sizes
    $all_colors_raw = $wpdb->get_col("SELECT colors FROM {$table}");
    $all_colors = array();
    foreach ($all_colors_raw as $crow) {
        foreach (explode(',', $crow) as $citem) {
            $citem = trim($citem);
            if ($citem && !in_array($citem, $all_colors, true)) $all_colors[] = $citem;
        }
    }

    $all_sizes_raw = $wpdb->get_col("SELECT sizes FROM {$table}");
    $all_sizes = array();
    foreach ($all_sizes_raw as $srow) {
        foreach (explode(',', $srow) as $sitem) {
            $sitem = trim($sitem);
            if ($sitem && !in_array($sitem, $all_sizes, true)) $all_sizes[] = $sitem;
        }
    }

    wp_send_json_success(array(
        'products' => $products,
        'categories' => $categories,
        'available_colors' => $all_colors,
        'available_sizes' => $all_sizes
    ));
}
add_action('wp_ajax_kien_get_products', 'kien_get_products');
add_action('wp_ajax_nopriv_kien_get_products', 'kien_get_products');

// 2. API: Get Product Detail
function kien_get_product_detail() {
    kien_ajax_check();
    global $wpdb;
    $id = absint($_POST['id'] ?? 0);
    $table = $wpdb->prefix . 'kien_products';
    $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
    if (!$product) {
        wp_send_json_error(array('message' => 'Không tìm thấy thông tin sản phẩm.'), 404);
    }

    // Related products in same category
    $related = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE category = %s AND id != %d LIMIT 4",
        $product['category'], $id
    ), ARRAY_A);

    // Curated gallery images
    $gallery = array();
    if (!empty($product['gallery'])) {
        $gallery = array_map('trim', explode(',', $product['gallery']));
    }
    if (empty($gallery)) {
        $gallery = array($product['image']);
        $cat = mb_strtolower($product['category'], 'UTF-8');
        if (strpos($cat, 'áo thun') !== false || strpos($cat, 'polo') !== false) {
            $gallery[] = 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&auto=format&fit=crop&q=80';
        } elseif (strpos($cat, 'sơ mi') !== false) {
            $gallery[] = 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80';
        } elseif (strpos($cat, 'khoác') !== false || strpos($cat, 'blazer') !== false) {
            $gallery[] = 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1544441893-675973e31985?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&auto=format&fit=crop&q=80';
        } elseif (strpos($cat, 'quần') !== false) {
            $gallery[] = 'https://images.unsplash.com/photo-1542272604-780c96856592?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1475178626620-a4d074967452?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&auto=format&fit=crop&q=80';
        } elseif (strpos($cat, 'váy') !== false || strpos($cat, 'đầm') !== false) {
            $gallery[] = 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80';
        } else {
            $gallery[] = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=800&auto=format&fit=crop&q=80';
            $gallery[] = 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80';
        }
    }
    $product['gallery_list'] = $gallery;

    // Curated customer reviews
    $firstColor = 'Đen';
    if (!empty($product['colors'])) {
        $cArr = explode(',', $product['colors']);
        if (!empty($cArr[0])) $firstColor = trim($cArr[0]);
    }

    $reviews = array(
        array(
            'author' => 'Nguyễn Văn Hoàng',
            'city' => 'Hà Nội',
            'rating' => 5,
            'date' => '2 ngày trước',
            'verified' => true,
            'comment' => 'Form áo lên cực kỳ tôn dáng, chất vải đanh mềm mịn và không nhăn khi giặt. Mặc đi làm hay đi cà phê cuối tuần đều rất hợp gu. Đã đặt thêm 1 chiếc màu nữa!',
            'option' => 'Đã mua: Size L, Màu ' . $firstColor
        ),
        array(
            'author' => 'Trần Thị Thảo My',
            'city' => 'Buôn Ma Thuột (Đắk Lắk)',
            'rating' => 5,
            'date' => '5 ngày trước',
            'verified' => true,
            'comment' => 'Ủng hộ đồ án thương hiệu của sinh viên Thế Kiên ĐH Tây Nguyên! Đóng gói hộp rất chỉn chu, có thiệp cảm ơn. Tư vấn chọn size AI cực chuẩn, mặc vừa in luôn.',
            'option' => 'Đã mua: Size M'
        ),
        array(
            'author' => 'Phạm Minh Đức',
            'city' => 'TP. Hồ Chí Minh',
            'rating' => 5,
            'date' => '1 tuần trước',
            'verified' => true,
            'comment' => 'Giao hàng hỏa tốc trong 24h. Vải sờ mát tay, đường may tỉ mỉ giấu chỉ rất tinh tế. Giá tiền xứng đáng với chất lượng 10/10.',
            'option' => 'Đã mua: Size XL'
        )
    );

    wp_send_json_success(array(
        'product' => $product,
        'related' => $related,
        'gallery' => $gallery,
        'reviews' => $reviews
    ));
}
add_action('wp_ajax_kien_get_product_detail', 'kien_get_product_detail');
add_action('wp_ajax_nopriv_kien_get_product_detail', 'kien_get_product_detail');

// 3. API: Create Order (Checkout)
function kien_create_order() {
    kien_ajax_check();
    global $wpdb;

    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $address = sanitize_textarea_field($_POST['address'] ?? '');
    $payment = sanitize_key($_POST['payment_method'] ?? 'cod');
    $notes = sanitize_textarea_field($_POST['notes'] ?? '');
    $coupon = sanitize_text_field($_POST['coupon'] ?? '');

    $items_json = wp_unslash($_POST['items'] ?? '[]');
    $items = json_decode($items_json, true);
    if (!empty($items) && is_array($items) && (isset($items['id']) || isset($items['price']))) {
        $items = array($items);
    }

    if (empty($name) || empty($phone) || empty($address)) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng.'));
    }

    // Kiểm tra định dạng số điện thoại Việt Nam (10 số, bắt đầu bằng 03, 05, 07, 08, 09)
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^(0|\+?84)(3|5|7|8|9)[0-9]{8}$/', $phone) && !preg_match('/^0[35789][0-9]{8}$/', $clean_phone)) {
        wp_send_json_error(array('message' => 'Số điện thoại không đúng định dạng Việt Nam! Vui lòng nhập số gồm 10 chữ số (Ví dụ: 0912345678 hoặc 0359235911).'));
    }

    if (empty($items) || !is_array($items)) {
        wp_send_json_error(array('message' => 'Giỏ hàng của bạn đang trống.'));
    }

    $subtotal = 0;
    foreach ($items as $it) {
        $qty = max(1, absint($it['quantity'] ?? 1));
        $price = floatval($it['price'] ?? 0);
        $subtotal += ($qty * $price);
    }

    $discount = 0;
    if (strtoupper($coupon) === 'THEKIEN10') {
        $discount = round($subtotal * 0.1);
    } elseif (strtoupper($coupon) === 'FREESHIP') {
        $discount = 30000;
    }

    $shipping = ($subtotal >= 400000 || strtoupper($coupon) === 'FREESHIP') ? 0 : 30000;
    $total = max(0, $subtotal - $discount + $shipping);

    $now = kien_get_vietnam_time();
    $dt_vn = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $order_code = 'TK' . $dt_vn->format('ymd') . rand(1000, 9999);

    $t_orders = $wpdb->prefix . 'kien_orders';
    $t_order_items = $wpdb->prefix . 'kien_order_items';
    $t_products = $wpdb->prefix . 'kien_products';

    $insert_order = $wpdb->insert($t_orders, array(
        'order_code' => $order_code,
        'customer_name' => $name,
        'customer_phone' => $phone,
        'customer_email' => $email,
        'customer_address' => $address,
        'payment_method' => $payment,
        'order_status' => 'pending',
        'subtotal' => $subtotal,
        'discount' => $discount,
        'shipping_fee' => $shipping,
        'total' => $total,
        'notes' => $notes,
        'created_at' => $now
    ));

    if (!$insert_order) {
        wp_send_json_error(array('message' => 'Không thể tạo đơn hàng: ' . $wpdb->last_error));
    }

    $order_id = $wpdb->insert_id;

    foreach ($items as $it) {
        $pid = absint($it['id'] ?? 0);
        $qty = max(1, absint($it['quantity'] ?? 1));
        $price = floatval($it['price'] ?? 0);
        $line_total = $qty * $price;

        $wpdb->insert($t_order_items, array(
            'order_id' => $order_id,
            'product_id' => $pid,
            'product_name' => sanitize_text_field($it['name'] ?? ''),
            'product_image' => esc_url_raw($it['image'] ?? ''),
            'size' => sanitize_text_field($it['size'] ?? 'M'),
            'color' => sanitize_text_field($it['color'] ?? 'Tiêu chuẩn'),
            'quantity' => $qty,
            'price' => $price,
            'total' => $line_total
        ));

        // Update sold & stock
        $wpdb->query($wpdb->prepare(
            "UPDATE {$t_products} SET sold = sold + %d, stock = GREATEST(0, stock - %d) WHERE id = %d",
            $qty, $qty, $pid
        ));
    }

    // Build VietQR link if banking
    $qr_url = '';
    if ($payment === 'banking') {
        $t_settings = $wpdb->prefix . 'kien_settings';
        $bin = $wpdb->get_var("SELECT setting_value FROM {$t_settings} WHERE setting_key = 'bank_bin'") ?: '970422';
        $acc = $wpdb->get_var("SELECT setting_value FROM {$t_settings} WHERE setting_key = 'bank_account'") ?: '0987654321';
        $holder = $wpdb->get_var("SELECT setting_value FROM {$t_settings} WHERE setting_key = 'bank_holder'") ?: 'MAI DAM THE KIEN';
        $desc = rawurlencode($order_code . ' ' . $phone);
        $qr_url = "https://img.vietqr.io/image/{$bin}-{$acc}-compact2.png?amount={$total}&addInfo={$desc}&accountName=" . rawurlencode($holder);
    }

    wp_send_json_success(array(
        'order_id' => $order_id,
        'order_code' => $order_code,
        'total' => $total,
        'payment_method' => $payment,
        'qr_url' => $qr_url,
        'message' => 'Đặt hàng thành công!'
    ));
}
add_action('wp_ajax_kien_create_order', 'kien_create_order');
add_action('wp_ajax_nopriv_kien_create_order', 'kien_create_order');

// 4. API: Order Tracking
function kien_track_order() {
    kien_ajax_check();
    global $wpdb;
    $keyword = trim(sanitize_text_field($_POST['keyword'] ?? ''));
    if (empty($keyword)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập mã đơn hàng (VD: TK261102...) hoặc số điện thoại.'));
    }

    $t_orders = $wpdb->prefix . 'kien_orders';
    $t_items = $wpdb->prefix . 'kien_order_items';

    // Tìm kiếm thông minh: Không phân biệt chữ hoa/thường cho mã đơn hàng, hoặc khớp số điện thoại
    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$t_orders} WHERE UPPER(TRIM(order_code)) = UPPER(%s) OR customer_phone = %s ORDER BY id DESC LIMIT 10",
        $keyword, $keyword
    ), ARRAY_A);

    if (empty($orders)) {
        wp_send_json_error(array('message' => 'Không tìm thấy đơn hàng nào với mã hoặc SĐT: ' . $keyword . '. Vui lòng kiểm tra lại!'));
    }

    $status_map = array(
        'pending' => array('label' => 'Chờ xử lý / Đã tiếp nhận', 'color' => '#f59e0b', 'step' => 1),
        'processing' => array('label' => 'Đang đóng gói', 'color' => '#3b82f6', 'step' => 2),
        'shipping' => array('label' => 'Đang vận chuyển', 'color' => '#8b5cf6', 'step' => 3),
        'completed' => array('label' => 'Giao thành công', 'color' => '#10b981', 'step' => 4),
        'cancelled' => array('label' => 'Đã hủy', 'color' => '#ef4444', 'step' => 0)
    );

    $payment_map = array(
        'cod' => 'Thanh toán tiền mặt khi nhận hàng (COD)',
        'banking' => 'Chuyển khoản VietQR (MB Bank)'
    );

    $results = array();
    foreach ($orders as $o) {
        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$t_items} WHERE order_id = %d", $o['id']), ARRAY_A);
        
        $status_info = $status_map[$o['order_status']] ?? array('label' => $o['order_status'], 'color' => '#6b7280', 'step' => 1);
        $o['status_label'] = $status_info['label'];
        $o['status_color'] = $status_info['color'];
        $o['status_step'] = $status_info['step'];
        $o['payment_label'] = $payment_map[$o['payment_method']] ?? strtoupper($o['payment_method']);
        $o['created_at_formatted'] = date('H:i - d/m/Y', strtotime($o['created_at']));
        $o['total_formatted'] = number_format($o['total'], 0, ',', '.') . '₫';
        $o['subtotal_formatted'] = number_format($o['subtotal'], 0, ',', '.') . '₫';
        $o['shipping_formatted'] = $o['shipping_fee'] > 0 ? number_format($o['shipping_fee'], 0, ',', '.') . '₫' : 'Miễn phí';
        $o['discount_formatted'] = number_format($o['discount'], 0, ',', '.') . '₫';
        
        $formatted_items = array();
        foreach ($items as $it) {
            $l_total = !empty($it['total']) ? floatval($it['total']) : (!empty($it['line_total']) ? floatval($it['line_total']) : (floatval($it['price']) * intval($it['quantity'])));
            $it['line_total_formatted'] = number_format($l_total, 0, ',', '.') . '₫';
            $it['price_formatted'] = number_format($it['price'], 0, ',', '.') . '₫';
            $formatted_items[] = $it;
        }
        $o['items'] = $formatted_items;
        $results[] = $o;
    }

    wp_send_json_success(array('orders' => $results));
}
add_action('wp_ajax_kien_track_order', 'kien_track_order');
add_action('wp_ajax_nopriv_kien_track_order', 'kien_track_order');

// 5. API: AI Chatbot Stylist Assistant
function kien_chatbot_message() {
    kien_ajax_check();
    global $wpdb;

    $user_msg = trim(sanitize_text_field($_POST['message'] ?? ''));
    $history_json = wp_unslash($_POST['history'] ?? '[]');
    $history = json_decode($history_json, true) ?: array();

    if (empty($user_msg)) {
        wp_send_json_error(array('message' => 'Nội dung tin nhắn trống.'));
    }

    $t_settings = $wpdb->prefix . 'kien_settings';
    $t_products = $wpdb->prefix . 'kien_products';

    $gemini_key = $wpdb->get_var("SELECT setting_value FROM {$t_settings} WHERE setting_key = 'gemini_api_key'");

    // Load product summary for knowledge base
    $products = $wpdb->get_results("SELECT id, name, category, price, sale_price, image, sizes, colors, badge FROM {$t_products} ORDER BY sold DESC LIMIT 20", ARRAY_A);

    // If Gemini Key provided, use Google Gemini API
    if (!empty($gemini_key)) {
        $product_list_text = "";
        foreach ($products as $p) {
            $p_price = number_format($p['sale_price'] ?: $p['price'], 0, ',', '.') . 'đ';
            $product_list_text .= "- ID: {$p['id']} | {$p['name']} | Danh mục: {$p['category']} | Giá: {$p_price} | Size: {$p['sizes']} | Màu: {$p['colors']}\n";
        }

        $system_instruction = "Bạn là 'Kiên AI Stylist' - Trợ lý thời trang thông minh và thân thiện của cửa hàng TheKien Fashion Store (do sinh viên Mai Đàm Thế Kiên phát triển).
Nhiệm vụ của bạn:
1. Tư vấn size chuẩn xác:
   - Dưới 50kg: Size S
   - 50kg - 60kg: Size M (nếu thích rộng lấy L)
   - 60kg - 70kg: Size L (nếu cao trên 1m75 hoặc thích rộng lấy XL)
   - 70kg - 82kg: Size XL
   - Trên 82kg: Size XXL
2. Giới thiệu sản phẩm phù hợp từ danh sách sản phẩm của cửa hàng:
{$product_list_text}
3. Tư vấn phối đồ (mix & match): Streetwear, công sở, dự tiệc, đi chơi thanh lịch.
4. Chính sách shop: Đổi trả miễn phí 7 ngày, bảo hành đường may trọn đời, freeship đơn từ 399k hoặc mã 'FREESHIP', giảm 10% mã 'THEKIEN10'.
5. Phong cách trả lời: Tươi trẻ, lễ phép (dạ, em, anh/chị), có biểu tượng cảm xúc thời trang, ngắn gọn, súc tích và khéo léo gợi ý sản phẩm kèm cú pháp [PRODUCT_ID:x] khi muốn giới thiệu sản phẩm x để giao diện hiển thị thẻ sản phẩm.";

        $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($gemini_key);

        $contents = array();
        // Add past turns
        foreach (array_slice($history, -6) as $h) {
            $role = ($h['sender'] === 'user') ? 'user' : 'model';
            $contents[] = array(
                'role' => $role,
                'parts' => array(array('text' => $h['text']))
            );
        }
        $contents[] = array(
            'role' => 'user',
            'parts' => array(array('text' => $user_msg))
        );

        $payload = array(
            'contents' => $contents,
            'systemInstruction' => array('parts' => array(array('text' => $system_instruction))),
            'generationConfig' => array('temperature' => 0.7, 'maxOutputTokens' => 800)
        );

        $response = wp_remote_post($api_url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode($payload),
            'timeout' => 15
        ));

        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $bot_reply = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!empty($bot_reply)) {
                // Parse product tags [PRODUCT_ID:x]
                $suggested_products = array();
                if (preg_match_all('/\[PRODUCT_ID:(\d+)\]/', $bot_reply, $matches)) {
                    foreach ($matches[1] as $pid) {
                        foreach ($products as $pr) {
                            if ($pr['id'] == $pid) {
                                $suggested_products[] = $pr;
                                break;
                            }
                        }
                    }
                    $bot_reply = preg_replace('/\[PRODUCT_ID:\d+\]/', '', $bot_reply);
                }

                wp_send_json_success(array(
                    'reply' => trim($bot_reply),
                    'products' => $suggested_products,
                    'provider' => 'gemini'
                ));
            }
        }
    }

    // Fallback: Built-in Intelligent Rule-Based Fashion Knowledge Engine
    $clean_msg = mb_strtolower($user_msg, 'UTF-8');
    $reply = "";
    $matched_products = array();

    // Check size consulting
    if (preg_match('/(\d+)\s*(kg|kí|cân)/u', $clean_msg, $w_match) || preg_match('/(m\d+|\d+\s*cm)/u', $clean_msg, $h_match) || strpos($clean_msg, 'size') !== false || strpos($clean_msg, 'bảng size') !== false) {
        $weight = 0;
        if (preg_match('/(\d+)\s*(kg|kí|cân)/u', $clean_msg, $w_match)) {
            $weight = (int)$w_match[1];
        }

        $suggested_size = "M";
        if ($weight > 0) {
            if ($weight < 50) $suggested_size = "S";
            elseif ($weight <= 60) $suggested_size = "M";
            elseif ($weight <= 70) $suggested_size = "L";
            elseif ($weight <= 82) $suggested_size = "XL";
            else $suggested_size = "XXL";
            $reply = "Dạ với cân nặng khoảng **{$weight}kg**, size chuẩn đẹp nhất cho anh/chị là **Size {$suggested_size}** ạ!\n- Nếu anh/chị thích mặc vừa vặn tôn dáng: chọn **Size {$suggested_size}**\n- Nếu thích phong cách Oversize rộng rãi thoải mái: có thể tăng lên 1 size nhé!";
        } else {
            $reply = "Dạ để em tư vấn size chuẩn nhất, anh/chị cho em xin **Chiều cao** và **Cân nặng** của mình nhé! Hoặc anh/chị có thể tham khảo bảng size chuẩn của TheKien Store:\n• Size S: 40 - 49kg (1m50 - 1m60)\n• Size M: 50 - 60kg (1m60 - 1m68)\n• Size L: 61 - 70kg (1m68 - 1m75)\n• Size XL: 71 - 82kg (1m75 - 1m82)\n• Size XXL: Trên 82kg";
        }
    }
    // Check shirt / áo
    elseif (strpos($clean_msg, 'áo thun') !== false || strpos($clean_msg, 'polo') !== false || strpos($clean_msg, 't-shirt') !== false) {
        $reply = "Dạ bên em có các mẫu **Áo thun 100% Cotton định lượng 250gsm** và **Áo Polo Pique thể thao** siêu thoáng mát, thấm hút mồ hôi cực tốt. Em gửi anh/chị tham khảo những mẫu hot nhất dưới đây nha!";
        $matched_products = array_values(array_filter($products, function($p){ return strpos($p['category'], 'Áo thun') !== false; }));
    }
    elseif (strpos($clean_msg, 'sơ mi') !== false || strpos($clean_msg, 'công sở') !== false) {
        $reply = "Dạ shop có dòng **Áo Sơ Mi Oxford Premium chống nhăn** cho nam và **Sơ Mi Lụa Satin Pháp** quý phái cho nữ cực kì được yêu thích cho đi làm công sở hay đi tiệc. Mời anh/chị xem chi tiết nhé:";
        $matched_products = array_values(array_filter($products, function($p){ return strpos($p['category'], 'Sơ mi') !== false || strpos($p['name'], 'Tây') !== false; }));
    }
    elseif (strpos($clean_msg, 'khoác') !== false || strpos($clean_msg, 'blazer') !== false || strpos($clean_msg, 'hoodie') !== false || strpos($clean_msg, 'mùa đông') !== false) {
        $reply = "Dạ Bộ sưu tập Áo khoác & Blazer 2 lớp Thu Đông của TheKien Fashion vừa cập bến, chất liệu nỉ bông dày dặn hoặc dạ ép cao cấp cực kỳ giữ ấm và thời thượng:";
        $matched_products = array_values(array_filter($products, function($p){ return strpos($p['category'], 'Áo khoác') !== false; }));
    }
    elseif (strpos($clean_msg, 'quần') !== false || strpos($clean_msg, 'jeans') !== false || strpos($clean_msg, 'kaki') !== false) {
        $reply = "Dạ mẫu **Quần Jeans ống rộng Vintage 13oz** và **Quần Tây Slimfit lưng chun co giãn** bên em đều đang có ưu đãi giảm giá tốt trong tuần này ạ!";
        $matched_products = array_values(array_filter($products, function($p){ return strpos($p['category'], 'Quần') !== false; }));
    }
    elseif (strpos($clean_msg, 'váy') !== false || strpos($clean_msg, 'đầm') !== false) {
        $reply = "Dạ TheKien Store có các mẫu **Váy hoa nhí Vintage tiểu thư** và **Đầm bodycon dạ hội** tôn dáng xinh xắn cho các nàng đây ạ:";
        $matched_products = array_values(array_filter($products, function($p){ return strpos($p['category'], 'Váy') !== false; }));
    }
    elseif (strpos($clean_msg, 'khuyến mãi') !== false || strpos($clean_msg, 'mã giảm') !== false || strpos($clean_msg, 'voucher') !== false || strpos($clean_msg, 'sale') !== false) {
        $reply = "**Ưu đãi độc quyền hôm nay tại TheKien Fashion:**\n1. Nhập mã: **`THEKIEN10`** để giảm ngay **10%** tổng hóa đơn!\n2. Nhập mã: **`FREESHIP`** hoặc đơn hàng từ **399.000đ** được Miễn phí giao hàng toàn quốc!\n3. Đổi trả hàng miễn phí trong vòng **7 ngày** nếu không vừa size.\nAnh/chị nhanh tay chọn món đồ ưng ý nhé!";
        $matched_products = array_slice($products, 0, 3);
    }
    elseif (strpos($clean_msg, 'đổi trả') !== false || strpos($clean_msg, 'bảo hành') !== false || strpos($clean_msg, 'ship') !== false || strpos($clean_msg, 'vận chuyển') !== false) {
        $reply = "**Chính sách bán hàng & Hỗ trợ:**\n• **Giao hàng:** Nội thành 1-2 ngày, tỉnh xa 2-3 ngày. Được kiểm tra hàng trước khi thanh toán.\n• **Đổi trả:** Hỗ trợ đổi size/mẫu miễn phí tận nhà trong **7 ngày** kể từ khi nhận hàng.\n• **Bảo hành:** Cam kết đường may, cúc áo, khóa kéo chuẩn chỉnh 100%.\n• Hotline hỗ trợ trực tiếp: **0987.654.321**.";
    }
    elseif (strpos($clean_msg, 'phối đồ') !== false || strpos($clean_msg, 'mix') !== false || strpos($clean_msg, 'outfit') !== false) {
        $reply = "**Gợi ý phối đồ chuẩn Stylist:**\n• **Phong cách Trẻ trung / Dạo phố:** Áo thun oversize + Quần jeans ống suông + Mũ lưỡi trai.\n• **Phong cách Công sở thanh lịch:** Áo sơ mi Oxford + Quần âu slimfit + Thắt lưng da.\n• **Phong cách Quý cô dự tiệc:** Đầm bodycon ôm dáng phối cùng Áo khoác blazer màu be và Túi xách da cao cấp.\nAnh/chị đang cần phối đồ theo phong cách nào cụ thể để em lên outfit riêng cho mình ạ?";
        $matched_products = array_slice($products, 0, 4);
    }
    else {
        $reply = "Dạ em nghe đây ạ! Em có thể giúp anh/chị **tư vấn size chuẩn**, **gợi ý mẫu áo/quần/váy phù hợp dáng người**, **hướng dẫn phối đồ** hoặc giải đáp các thắc mắc về đơn hàng và khuyến mãi ạ. Anh/chị đang quan tâm đến sản phẩm nào ạ?";
        $matched_products = array_slice($products, 0, 3);
    }

    wp_send_json_success(array(
        'reply' => $reply,
        'products' => array_slice($matched_products, 0, 3),
        'provider' => 'knowledge_base'
    ));
}
add_action('wp_ajax_kien_chatbot_message', 'kien_chatbot_message');
add_action('wp_ajax_nopriv_kien_chatbot_message', 'kien_chatbot_message');

// 6. API: Admin Get Dashboard Data
function kien_admin_get_data() {
    kien_admin_check();
    global $wpdb;

    $t_orders = $wpdb->prefix . 'kien_orders';
    $t_items = $wpdb->prefix . 'kien_order_items';
    $t_products = $wpdb->prefix . 'kien_products';
    $t_settings = $wpdb->prefix . 'kien_settings';

    $total_revenue = $wpdb->get_var("SELECT SUM(total) FROM {$t_orders} WHERE order_status != 'cancelled'") ?: 0;
    $total_orders = $wpdb->get_var("SELECT COUNT(*) FROM {$t_orders}") ?: 0;
    $pending_orders = $wpdb->get_var("SELECT COUNT(*) FROM {$t_orders} WHERE order_status = 'pending'") ?: 0;
    $total_products = $wpdb->get_var("SELECT COUNT(*) FROM {$t_products}") ?: 0;

    $recent_orders = $wpdb->get_results("SELECT * FROM {$t_orders} ORDER BY id DESC LIMIT 20", ARRAY_A);
    foreach ($recent_orders as &$ord) {
        $ord['items'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$t_items} WHERE order_id = %d", $ord['id']), ARRAY_A);
    }

    $all_products = $wpdb->get_results("SELECT * FROM {$t_products} ORDER BY id DESC", ARRAY_A);

    $settings_rows = $wpdb->get_results("SELECT setting_key, setting_value FROM {$t_settings}", ARRAY_A);
    $settings = array();
    foreach ($settings_rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    wp_send_json_success(array(
        'stats' => array(
            'revenue' => (float)$total_revenue,
            'orders' => (int)$total_orders,
            'pending' => (int)$pending_orders,
            'products' => (int)$total_products
        ),
        'orders' => $recent_orders,
        'products' => $all_products,
        'settings' => $settings
    ));
}
add_action('wp_ajax_kien_admin_get_data', 'kien_admin_get_data');

// 7. API: Admin Save Product
function kien_admin_save_product() {
    kien_admin_check();
    global $wpdb;
    $table = $wpdb->prefix . 'kien_products';

    $id = absint($_POST['id'] ?? 0);
    $name = sanitize_text_field($_POST['name'] ?? '');
    $category = sanitize_text_field($_POST['category'] ?? 'Áo thun & Polo');
    $price = floatval($_POST['price'] ?? 0);
    $sale_price = floatval($_POST['sale_price'] ?? 0);
    $image = esc_url_raw($_POST['image'] ?? '');
    $description = sanitize_textarea_field($_POST['description'] ?? '');
    $sizes = sanitize_text_field($_POST['sizes'] ?? 'S,M,L,XL');
    $colors = sanitize_text_field($_POST['colors'] ?? 'Đen,Trắng');
    $stock = max(0, intval($_POST['stock'] ?? 100));
    $badge = sanitize_text_field($_POST['badge'] ?? '');
    $now = current_time('mysql');

    if (empty($name) || $price <= 0) {
        wp_send_json_error(array('message' => 'Vui lòng nhập tên sản phẩm và giá hợp lệ.'));
    }

    $slug = sanitize_title($name);

    $data = array(
        'name' => $name,
        'slug' => $slug,
        'category' => $category,
        'price' => $price,
        'sale_price' => $sale_price,
        'image' => $image ?: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80',
        'description' => $description,
        'sizes' => $sizes,
        'colors' => $colors,
        'stock' => $stock,
        'badge' => $badge,
        'updated_at' => $now
    );

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id));
    } else {
        $data['created_at'] = $now;
        $data['sold'] = 0;
        $data['rating'] = 5.0;
        $wpdb->insert($table, $data);
        $id = $wpdb->insert_id;
    }

    wp_send_json_success(array('id' => $id, 'message' => 'Đã lưu sản phẩm thành công.'));
}
add_action('wp_ajax_kien_admin_save_product', 'kien_admin_save_product');

// 8. API: Admin Delete Product
function kien_admin_delete_product() {
    kien_admin_check();
    global $wpdb;
    $id = absint($_POST['id'] ?? 0);
    if ($id) {
        $wpdb->delete($wpdb->prefix . 'kien_products', array('id' => $id));
    }
    wp_send_json_success(array('message' => 'Đã xóa sản phẩm.'));
}
add_action('wp_ajax_kien_admin_delete_product', 'kien_admin_delete_product');

// 9. API: Admin Update Order Status
function kien_admin_update_order() {
    kien_admin_check();
    global $wpdb;
    $id = absint($_POST['id'] ?? 0);
    $status = sanitize_key($_POST['status'] ?? 'pending');
    $allowed = array('pending', 'processing', 'shipping', 'completed', 'cancelled');
    if (!in_array($status, $allowed, true)) {
        wp_send_json_error(array('message' => 'Trạng thái không hợp lệ.'));
    }

    $wpdb->update($wpdb->prefix . 'kien_orders', array('order_status' => $status), array('id' => $id));
    wp_send_json_success(array('message' => 'Đã cập nhật trạng thái đơn hàng.'));
}
add_action('wp_ajax_kien_admin_update_order', 'kien_admin_update_order');

// 10. API: Admin Save Settings
function kien_admin_save_settings() {
    kien_admin_check();
    global $wpdb;
    $table = $wpdb->prefix . 'kien_settings';

    $keys = array('shop_name', 'shop_tagline', 'shop_phone', 'shop_email', 'shop_address', 'bank_name', 'bank_account', 'bank_holder', 'bank_bin', 'gemini_api_key', 'bot_welcome');
    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            $val = sanitize_textarea_field(wp_unslash($_POST[$k]));
            $wpdb->replace($table, array('setting_key' => $k, 'setting_value' => $val));
        }
    }
    wp_send_json_success(array('message' => 'Đã lưu cấu hình shop thành công.'));
}
add_action('wp_ajax_kien_admin_save_settings', 'kien_admin_save_settings');

// 11. Auth APIs
function kien_ajax_login() {
    kien_ajax_check();
    $username = sanitize_user(wp_unslash($_POST['username'] ?? ''));
    $password = (string)wp_unslash($_POST['password'] ?? '');

    $signon = wp_signon(array(
        'user_login' => $username,
        'user_password' => $password,
        'remember' => true
    ), is_ssl());

    if (is_wp_error($signon)) {
        wp_send_json_error(array('message' => 'Tài khoản hoặc mật khẩu không chính xác.'));
    }

    wp_set_current_user($signon->ID);
    wp_set_auth_cookie($signon->ID, true);

    wp_send_json_success(array(
        'message' => 'Đăng nhập thành công!',
        'user' => array(
            'id' => $signon->ID,
            'name' => $signon->display_name,
            'isAdmin' => current_user_can('manage_options')
        )
    ));
}
add_action('wp_ajax_kien_login', 'kien_ajax_login');
add_action('wp_ajax_nopriv_kien_login', 'kien_ajax_login');

function kien_ajax_logout() {
    kien_ajax_check();
    wp_logout();
    wp_send_json_success(array('message' => 'Đã đăng xuất.'));
}
add_action('wp_ajax_kien_logout', 'kien_ajax_logout');


// ================= API: SEND CONTACT MESSAGE =================
function kien_send_contact() {
    kien_ajax_check();
    global $wpdb;
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? 'Liên hệ chung');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ Họ tên, Email và Nội dung tin nhắn.'));
    }

    $t_contacts = $wpdb->prefix . 'kien_contacts';
    $charset = $wpdb->get_charset_collate();
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$t_contacts} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(50) NULL,
        email varchar(255) NOT NULL,
        subject varchar(255) NULL,
        message text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB {$charset};");

    $inserted = $wpdb->insert($t_contacts, array(
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ));

    if ($inserted === false) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi lưu tin nhắn, vui lòng thử lại!'));
    }

    wp_send_json_success(array(
        'message' => 'Cảm ơn bạn ' . esc_html($name) . '! Lời nhắn của bạn đã được gửi thành công đến THEKIEN FASHION. Chuyên viên chăm sóc khách hàng sẽ liên hệ với bạn trong thời gian sớm nhất.'
    ));
}
add_action('wp_ajax_kien_send_contact', 'kien_send_contact');
add_action('wp_ajax_nopriv_kien_send_contact', 'kien_send_contact');


// ==========================================================================
// WORDPRESS DASHBOARD INTEGRATION: PRODUCTS, POSTS, ORDERS & CONTACTS
// ==========================================================================

// 1. Register WordPress Admin Menus
add_action('admin_menu', 'kien_register_admin_menus');

// Helper lấy cấu hình giao diện & banner từ CSDL
function kien_get_theme_setting($key, $default = '') {
    global $wpdb;
    static $settings_cache = null;
    if ($settings_cache === null) {
        $t_settings = $wpdb->prefix . 'kien_settings';
        $rows = $wpdb->get_results("SELECT setting_key, setting_value FROM {$t_settings}", ARRAY_A);
        $settings_cache = array();
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $settings_cache[$r['setting_key']] = $r['setting_value'];
            }
        }
    }
    return (isset($settings_cache[$key]) && $settings_cache[$key] !== '') ? $settings_cache[$key] : $default;
}

function kien_register_admin_menus() {
    // Main Menu: THEKIEN Fashion
    add_menu_page(
        'THEKIEN Fashion',
        'THEKIEN Fashion',
        'manage_options',
        'kien-products',
        'kien_render_admin_products_page',
        'dashicons-store',
        25
    );

    // Submenu 1: Tất cả sản phẩm
    add_submenu_page(
        'kien-products',
        'Quản lý Sản phẩm - THEKIEN',
        'Tất cả sản phẩm',
        'manage_options',
        'kien-products',
        'kien_render_admin_products_page'
    );

    // Submenu 2: Thêm sản phẩm mới
    add_submenu_page(
        'kien-products',
        'Thêm Sản Phẩm Mới - THEKIEN',
        'Thêm sản phẩm',
        'manage_options',
        'kien-product-new',
        'kien_render_admin_product_form_page'
    );

    // Submenu 3: Quản lý Đơn hàng
    add_submenu_page(
        'kien-products',
        'Quản lý Đơn hàng - THEKIEN',
        'Đơn hàng shop',
        'manage_options',
        'kien-orders',
        'kien_render_admin_orders_page'
    );

    // Submenu 4: Tin nhắn liên hệ
    add_submenu_page(
        'kien-products',
        'Tin nhắn Liên hệ Khách hàng',
        'Tin nhắn liên hệ',
        'manage_options',
        'kien-contacts',
        'kien_render_admin_contacts_page'
    );

    // Submenu 5: Link đến Bài viết WordPress (Blog / Tin tức)
    add_submenu_page(
        'kien-products',
        'Bài viết & Cẩm nang thời trang',
        'Bài viết thời trang',
        'manage_options',
        'edit.php'
    );

    // Submenu 6: Cài đặt Giao diện, Banner & Logo
    add_submenu_page(
        'kien-products',
        'Cài đặt Giao diện, Banner & Logo - THEKIEN',
        'Giao diện & Banner',
        'manage_options',
        'kien-theme-settings',
        'kien_render_admin_theme_settings_page'
    );
}

// 2. Enqueue Media Uploader & Admin CSS
add_action('admin_enqueue_scripts', 'kien_admin_assets');
function kien_admin_assets($hook) {
    if (strpos($hook, 'kien-') !== false) {
        wp_enqueue_media();
        wp_add_inline_style('wp-admin', '
            /* Triệt tiêu 100% lỗi scroll ngang trên toàn bộ Dashboard */
            html.wp-toolbar, body.wp-admin { overflow-x: hidden !important; }
            #wpwrap { overflow-x: hidden !important; max-width: 100vw !important; box-sizing: border-box !important; }
            #wpcontent { overflow-x: hidden !important; }
            #wpbody-content { overflow-x: hidden !important; padding-right: 20px !important; box-sizing: border-box !important; }
            .kien-admin-wrap { max-width: 100% !important; width: auto !important; margin: 15px 0 !important; box-sizing: border-box !important; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
            .kien-admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; max-width: 100%; box-sizing: border-box; }
            .kien-admin-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
            .kien-admin-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important; gap: 16px; margin-bottom: 24px; max-width: 100%; box-sizing: border-box; }
            .kien-stat-card { background: #ffffff; padding: 18px 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); box-sizing: border-box; }
            .kien-stat-card b { display: block; font-size: 26px; font-weight: 800; color: #1e293b; line-height: 1.2; margin-top: 4px; }
            .kien-stat-card span { font-size: 13px; color: #64748b; font-weight: 600; }
            .kien-admin-filter-bar { background: #ffffff; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; max-width: 100%; box-sizing: border-box; }
            .kien-badge { display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
            .kien-badge-hot { background: #fee2e2; color: #dc2626; }
            .kien-badge-sale { background: #fef3c7; color: #d97706; }
            .kien-badge-new { background: #dbeafe; color: #2563eb; }
            .kien-prod-thumb-mini { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; vertical-align: middle; }
            .kien-table-responsive { width: 100% !important; max-width: 100% !important; overflow-x: auto !important; -webkit-overflow-scrolling: touch; margin-bottom: 20px; border-radius: 8px; border: 1px solid #c3c4c7; background: #ffffff; box-sizing: border-box !important; }
            .kien-table-responsive table.wp-list-table { margin: 0 !important; border: none !important; width: 100% !important; min-width: 950px; table-layout: auto !important; box-sizing: border-box !important; }
            .kien-table-responsive table.wp-list-table th, .kien-table-responsive table.wp-list-table td { word-break: break-word; box-sizing: border-box; }
            .kien-form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; max-width: 100%; box-sizing: border-box; }
            .kien-form-box { background: #ffffff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px; box-sizing: border-box; }
            .kien-form-box h3 { font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }
            .kien-img-preview-box { width: 100%; aspect-ratio: 1; border-radius: 10px; background: #f8fafc; border: 1.5px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-top: 10px; }
            .kien-img-preview-box img { width: 100%; height: 100%; object-fit: cover; }        ');
    }
}

// 3. Render Page: Tất cả sản phẩm
function kien_render_admin_products_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'kien_products';

    // Handle Delete Action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $del_id = absint($_GET['id']);
        check_admin_referer('kien_del_product_' . $del_id);
        $wpdb->delete($table, array('id' => $del_id));
        echo '<div class="notice notice-success is-dismissible"><p><b>Thành công:</b> Đã xóa sản phẩm #' . $del_id . ' khỏi cơ sở dữ liệu!</p></div>';
    }

    // Filters & Search
    $search = sanitize_text_field($_GET['s'] ?? '');
    $cat_filter = sanitize_text_field($_GET['cat'] ?? '');

    $where = array('1=1');
    $params = array();
    if ($search) {
        $where[] = '(name LIKE %s OR description LIKE %s)';
        $params[] = '%' . $wpdb->esc_like($search) . '%';
        $params[] = '%' . $wpdb->esc_like($search) . '%';
    }
    if ($cat_filter) {
        $where[] = 'category = %s';
        $params[] = $cat_filter;
    }

    $where_sql = implode(' AND ', $where);
    $query = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY id DESC";
    $products = !empty($params) ? $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A) : $wpdb->get_results($query, ARRAY_A);

    // Stats
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    $total_stock = $wpdb->get_var("SELECT SUM(stock) FROM {$table}") ?: 0;
    $total_sale = $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE sale_price > 0");
    $total_orders = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kien_orders") ?: 0;
    $all_cats = $wpdb->get_col("SELECT DISTINCT category FROM {$table} ORDER BY category ASC");

    ?>
    <div class="wrap kien-admin-wrap">
        <div class="kien-admin-header">
            <div>
                <h1 class="kien-admin-title"><span class="dashicons dashicons-products"></span> Quản Lý Sản Phẩm - THEKIEN Fashion</h1>
                <p style="color:#64748b;margin:4px 0 0">Quản lý toàn bộ kho hàng, giá bán, màu sắc và danh mục sản phẩm của website.</p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=kien-product-new'); ?>" class="button button-primary button-hero" style="font-weight:700">
                <span class="dashicons dashicons-plus-alt2"></span> Thêm sản phẩm mới
            </a>
        </div>

        <!-- Stat Cards -->
        <div class="kien-admin-stats-grid">
            <div class="kien-stat-card">
                <span>TỔNG SẢN PHẨM</span>
                <b><?php echo number_format($total_count); ?></b>
            </div>
            <div class="kien-stat-card">
                <span>TỔNG TỒN KHO</span>
                <b><?php echo number_format($total_stock); ?> cái</b>
            </div>
            <div class="kien-stat-card">
                <span>ĐANG GIẢM GIÁ</span>
                <b style="color:#d97706"><?php echo number_format($total_sale); ?></b>
            </div>
            <div class="kien-stat-card">
                <span>TỔNG ĐƠN HÀNG</span>
                <b style="color:#2563eb"><?php echo number_format($total_orders); ?></b>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="kien-admin-filter-bar">
            <form method="get" action="<?php echo admin_url('admin.php'); ?>" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <input type="hidden" name="page" value="kien-products">
                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Tìm tên sản phẩm..." style="width:240px">
                <select name="cat">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($all_cats as $c): ?>
                        <option value="<?php echo esc_attr($c); ?>" <?php selected($cat_filter, $c); ?>><?php echo esc_html($c); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button" value="Lọc sản phẩm">
                <?php if ($search || $cat_filter): ?>
                    <a href="<?php echo admin_url('admin.php?page=kien-products'); ?>" class="button">Đặt lại</a>
                <?php endif; ?>
            </form>
            <span style="color:#64748b;font-size:13px">Hiển thị <b><?php echo count($products); ?></b> sản phẩm</span>
        </div>

        <!-- Products Table -->
        <div class="kien-table-responsive">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th style="width:70px">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="width:140px">Danh mục</th>
                    <th style="width:130px">Giá bán</th>
                    <th style="width:90px">Kho</th>
                    <th style="width:90px">Đã bán</th>
                    <th style="width:120px">Màu sắc</th>
                    <th style="width:100px">Nhãn</th>
                    <th style="width:130px;text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="10" style="text-align:center;padding:30px;color:#94a3b8">Không tìm thấy sản phẩm nào!</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): 
                        $del_url = wp_nonce_url(admin_url('admin.php?page=kien-products&action=delete&id=' . $p['id']), 'kien_del_product_' . $p['id']);
                        $edit_url = admin_url('admin.php?page=kien-product-new&id=' . $p['id']);
                        $price = number_format($p['price'], 0, ',', '.') . '₫';
                        $sale = $p['sale_price'] > 0 ? number_format($p['sale_price'], 0, ',', '.') . '₫' : '';
                    ?>
                        <tr>
                            <td><b>#<?php echo $p['id']; ?></b></td>
                            <td>
                                <img src="<?php echo esc_url($p['image']); ?>" class="kien-prod-thumb-mini" alt="<?php echo esc_attr($p['name']); ?>">
                            </td>
                            <td>
                                <strong style="font-size:14px"><a href="<?php echo $edit_url; ?>"><?php echo esc_html($p['name']); ?></a></strong>
                                <div style="font-size:11px;color:#94a3b8">Mã: TK-FASH-00<?php echo $p['id']; ?></div>
                            </td>
                            <td><span style="background:#eff6ff;color:#1d4ed8;padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600"><?php echo esc_html($p['category']); ?></span></td>
                            <td>
                                <?php if ($sale): ?>
                                    <b style="color:#dc2626"><?php echo $sale; ?></b><br>
                                    <span style="color:#94a3b8;font-size:11px;text-decoration:line-through"><?php echo $price; ?></span>
                                <?php else: ?>
                                    <b><?php echo $price; ?></b>
                                <?php endif; ?>
                            </td>
                            <td><b><?php echo number_format($p['stock']); ?></b></td>
                            <td><?php echo number_format($p['sold']); ?></td>
                            <td><span style="font-size:12px;color:#475569"><?php echo esc_html($p['colors']); ?></span></td>
                            <td>
                                <?php if (!empty($p['badge'])): ?>
                                    <span class="kien-badge kien-badge-<?php echo strpos($p['badge'], 'Hot') !== false ? 'hot' : (strpos($p['badge'], 'Sale') !== false ? 'sale' : 'new'); ?>">
                                        <?php echo esc_html($p['badge']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#cbd5e1">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center">
                                <a href="<?php echo $edit_url; ?>" class="button button-small" title="Chỉnh sửa">Sửa</a>
                                <a href="<?php echo $del_url; ?>" class="button button-small button-link-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" title="Xóa">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php
}

// 4. Render Page: Thêm / Chỉnh sửa sản phẩm
function kien_render_admin_product_form_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'kien_products';

    $id = absint($_GET['id'] ?? 0);
    $prod = null;
    if ($id) {
        $prod = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
    }

    // Handle Form Submit
    if ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['kien_save_product'])) {
        check_admin_referer('kien_product_form_nonce');

        $name = sanitize_text_field($_POST['name'] ?? '');
        $category = sanitize_text_field($_POST['category'] ?? 'Áo thun & Polo');
        $price = floatval($_POST['price'] ?? 0);
        $sale_price = floatval($_POST['sale_price'] ?? 0);
        $image = esc_url_raw($_POST['image'] ?? '');
        $gallery = sanitize_textarea_field($_POST['gallery'] ?? '');
        $description = wp_kses_post($_POST['description'] ?? '');
        $sizes = sanitize_text_field($_POST['sizes'] ?? 'S,M,L,XL');
        $colors = sanitize_text_field($_POST['colors'] ?? 'Đen,Trắng');
        $stock = absint($_POST['stock'] ?? 100);
        $sold = absint($_POST['sold'] ?? 0);
        $rating = floatval($_POST['rating'] ?? 5.0);
        $badge = sanitize_text_field($_POST['badge'] ?? '');
        $now = current_time('mysql');

        $data = array(
            'name' => $name,
            'slug' => sanitize_title($name),
            'category' => $category,
            'price' => $price,
            'sale_price' => $sale_price,
            'image' => $image,
            'gallery' => $gallery,
            'description' => $description,
            'sizes' => $sizes,
            'colors' => $colors,
            'stock' => $stock,
            'sold' => $sold,
            'rating' => $rating,
            'badge' => $badge,
            'updated_at' => $now
        );

        if ($id && $prod) {
            $wpdb->update($table, $data, array('id' => $id));
            echo '<div class="notice notice-success is-dismissible"><p><b>Thành công:</b> Đã cập nhật sản phẩm #' . $id . ' thành công!</p></div>';
            $prod = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        } else {
            $data['created_at'] = $now;
            $wpdb->insert($table, $data);
            $new_id = $wpdb->insert_id;
            echo '<div class="notice notice-success is-dismissible"><p><b>Thành công:</b> Đã thêm sản phẩm mới #' . $new_id . '! <a href="' . admin_url('admin.php?page=kien-products') . '">Quay lại danh sách</a></p></div>';
            $id = $new_id;
            $prod = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        }
    }

    $all_cats = array('Áo thun & Polo', 'Sơ mi công sở', 'Áo khoác & Blazer', 'Quần jeans & Kaki', 'Váy & Đầm nữ', 'Phụ kiện thời trang');

    ?>
    <div class="wrap kien-admin-wrap">
        <div class="kien-admin-header">
            <div>
                <h1 class="kien-admin-title"><?php echo $id ? 'Chỉnh Sửa Sản Phẩm #' . $id : 'Thêm Sản Phẩm Mới'; ?></h1>
                <p style="color:#64748b;margin:4px 0 0">Điền đầy đủ thông tin bên dưới để đăng hoặc cập nhật sản phẩm trên website.</p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=kien-products'); ?>" class="button">← Quay lại danh sách sản phẩm</a>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('kien_product_form_nonce'); ?>
            <input type="hidden" name="kien_save_product" value="1">

            <div class="kien-form-grid">
                <!-- Left Column -->
                <div>
                    <div class="kien-form-box">
                        <h3>Thông tin sản phẩm</h3>
                        <p>
                            <label><b>Tên sản phẩm:</b> <span style="color:red">*</span></label><br>
                            <input type="text" name="name" value="<?php echo esc_attr($prod['name'] ?? ''); ?>" required class="large-text" placeholder="Ví dụ: Áo Thun Unisex Cotton Compact 100%">
                        </p>
                        <p>
                            <label><b>Danh mục:</b></label><br>
                            <select name="category" class="regular-text">
                                <?php foreach ($all_cats as $c): ?>
                                    <option value="<?php echo esc_attr($c); ?>" <?php selected($prod['category'] ?? '', $c); ?>><?php echo esc_html($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <div style="display:flex;gap:20px">
                            <p style="flex:1">
                                <label><b>Giá gốc niêm yết (VNĐ):</b> <span style="color:red">*</span></label><br>
                                <input type="number" name="price" value="<?php echo esc_attr($prod['price'] ?? 350000); ?>" required class="regular-text">
                            </p>
                            <p style="flex:1">
                                <label><b>Giá khuyến mãi (VNĐ):</b></label><br>
                                <input type="number" name="sale_price" value="<?php echo esc_attr($prod['sale_price'] ?? 0); ?>" class="regular-text" placeholder="Để 0 nếu không sale">
                            </p>
                        </div>
                        <p>
                            <label><b>Mô tả sản phẩm:</b></label><br>
                            <textarea name="description" rows="5" class="large-text" placeholder="Nhập mô tả chất liệu, kiểu dáng, điểm nổi bật..."><?php echo esc_textarea($prod['description'] ?? ''); ?></textarea>
                        </p>
                        <div style="display:flex;gap:20px">
                            <p style="flex:1">
                                <label><b>Màu sắc (cách nhau dấu phẩy):</b></label><br>
                                <input type="text" name="colors" value="<?php echo esc_attr($prod['colors'] ?? 'Đen,Trắng,Be'); ?>" class="large-text">
                            </p>
                            <p style="flex:1">
                                <label><b>Kích cỡ (cách nhau dấu phẩy):</b></label><br>
                                <input type="text" name="sizes" value="<?php echo esc_attr($prod['sizes'] ?? 'S,M,L,XL'); ?>" class="large-text">
                            </p>
                        </div>
                    </div>

                    <div class="kien-form-box">
                        <h3>Dải ảnh Gallery chi tiết (Thumbnails)</h3>
                        <p style="font-size:12px;color:#64748b">Nhập các link ảnh cách nhau bằng dấu phẩy để hiển thị thư viện ảnh đa góc độ trong popup chi tiết sản phẩm.</p>
                        <textarea name="gallery" rows="3" class="large-text" placeholder="https://..., https://..."><?php echo esc_textarea($prod['gallery'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Publish Box -->
                    <div class="kien-form-box">
                        <h3>Lưu dữ liệu</h3>
                        <p style="color:#64748b;font-size:13px">Sản phẩm sẽ hiển thị ngay lập tức ngoài trang chủ sau khi bấm lưu.</p>
                        <input type="submit" class="button button-primary button-large" value="<?php echo $id ? 'Lưu thay đổi' : 'Đăng sản phẩm mới'; ?>" style="width:100%;font-weight:700">
                    </div>

                    <!-- Main Image Box with WP Media Integration -->
                    <div class="kien-form-box">
                        <h3>Ảnh đại diện sản phẩm</h3>
                        <input type="text" id="kien-prod-img-input" name="image" value="<?php echo esc_attr($prod['image'] ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80'); ?>" class="large-text" style="margin-bottom:10px">
                        <button type="button" class="button" id="kien-upload-media-btn" style="width:100%"><span class="dashicons dashicons-format-image"></span> Chọn ảnh từ Media WordPress</button>
                        <div class="kien-img-preview-box">
                            <img id="kien-prod-img-preview" src="<?php echo esc_url($prod['image'] ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80'); ?>" alt="Preview">
                        </div>
                    </div>

                    <!-- Inventory & Badges -->
                    <div class="kien-form-box">
                        <h3>Tồn kho & Hiển thị</h3>
                        <p>
                            <label><b>Số lượng trong kho:</b></label><br>
                            <input type="number" name="stock" value="<?php echo esc_attr($prod['stock'] ?? 100); ?>" class="regular-text">
                        </p>
                        <p>
                            <label><b>Số lượng đã bán:</b></label><br>
                            <input type="number" name="sold" value="<?php echo esc_attr($prod['sold'] ?? 0); ?>" class="regular-text">
                        </p>
                        <p>
                            <label><b>Điểm đánh giá (Stars):</b></label><br>
                            <input type="number" step="0.1" max="5.0" min="1.0" name="rating" value="<?php echo esc_attr($prod['rating'] ?? 5.0); ?>" class="regular-text">
                        </p>
                        <p>
                            <label><b>Huy hiệu (Badge):</b></label><br>
                            <input type="text" name="badge" value="<?php echo esc_attr($prod['badge'] ?? ''); ?>" placeholder="Ví dụ: Hot, Sale -30%, Mới" class="regular-text">
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Media Uploader Script -->
    <script>
    jQuery(document).ready(function($){
        $('#kien-upload-media-btn').on('click', function(e){
            e.preventDefault();
            var frame = wp.media({
                title: 'Chọn ảnh sản phẩm thời trang',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#kien-prod-img-input').val(attachment.url);
                $('#kien-prod-img-preview').attr('src', attachment.url);
            });
            frame.open();
        });
        $('#kien-prod-img-input').on('input', function(){
            $('#kien-prod-img-preview').attr('src', $(this).val());
        });
    });
    </script>
    <?php
}

// 5. Render Page: Quản lý Đơn hàng
function kien_render_admin_orders_page() {
    global $wpdb;
    $t_orders = $wpdb->prefix . 'kien_orders';

    // Handle Status Update
    if ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['update_order_id'])) {
        check_admin_referer('kien_update_order_nonce');
        $oid = absint($_POST['update_order_id']);
        $new_status = sanitize_key($_POST['new_status']);
        $wpdb->update($t_orders, array('order_status' => $new_status), array('id' => $oid));
        echo '<div class="notice notice-success is-dismissible"><p><b>Thành công:</b> Đã cập nhật trạng thái đơn hàng #' . $oid . ' thành <b>' . esc_html($new_status) . '</b>!</p></div>';
    }

    $orders = $wpdb->get_results("SELECT * FROM {$t_orders} ORDER BY id DESC LIMIT 50", ARRAY_A);
    $total_revenue = $wpdb->get_var("SELECT SUM(total) FROM {$t_orders} WHERE order_status != 'cancelled'") ?: 0;

    ?>
    <div class="wrap kien-admin-wrap">
        <div class="kien-admin-header">
            <div>
                <h1 class="kien-admin-title"><span class="dashicons dashicons-cart"></span> Quản Lý Đơn Hàng Khách Hàng</h1>
                <p style="color:#64748b;margin:4px 0 0">Theo dõi thông tin người mua, địa chỉ nhận hàng, phương thức thanh toán và trạng thái xử lý đơn.</p>
            </div>
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;padding:10px 18px;border-radius:10px">
                <span style="font-size:12px;color:#047857;font-weight:700">TỔNG DOANH THU:</span>
                <b style="font-size:20px;color:#065f46;display:block"><?php echo number_format($total_revenue, 0, ',', '.'); ?>₫</b>
            </div>
        </div>

        <div class="kien-table-responsive">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:80px">Mã ĐH</th>
                    <th style="width:130px">Khách hàng</th>
                    <th style="width:230px">Sản phẩm đặt mua</th>
                    <th>Địa chỉ nhận hàng</th>
                    <th style="width:85px">Thanh toán</th>
                    <th style="width:110px">Tổng tiền</th>
                    <th style="width:160px">Trạng thái</th>
                    <th style="width:130px">Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:30px;color:#94a3b8">Chưa có đơn hàng nào phát sinh!</td></tr>
                <?php else: ?>
                    <?php 
                    $t_items = $wpdb->prefix . 'kien_order_items';
                    foreach ($orders as $o): 
                        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$t_items} WHERE order_id = %d", $o['id']), ARRAY_A);
                    ?>
                        <tr>
                            <td><b style="font-size:13px;color:#0f172a"><?php echo esc_html($o['order_code']); ?></b></td>
                            <td>
                                <b><?php echo esc_html($o['customer_name']); ?></b><br>
                                <span style="font-size:12px;color:#2563eb"><span class="dashicons dashicons-phone"></span> <?php echo esc_html($o['customer_phone']); ?></span>
                                <?php if (!empty($o['customer_email'])): ?>
                                    <br><span style="font-size:11px;color:#64748b"><?php echo esc_html($o['customer_email']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($items)): ?>
                                    <div style="display:flex; flex-direction:column; gap:6px;">
                                        <?php foreach ($items as $it): 
                                            $img = !empty($it['product_image']) ? $it['product_image'] : 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo.png';
                                            $variants = array();
                                            if (!empty($it['color'])) $variants[] = $it['color'];
                                            if (!empty($it['size'])) $variants[] = 'Size ' . $it['size'];
                                            $variant_text = implode(' / ', $variants);
                                            $it_price = floatval($it['price']);
                                            $it_qty = intval($it['quantity'] ?: 1);
                                            $it_total = !empty($it['total']) ? floatval($it['total']) : (!empty($it['line_total']) ? floatval($it['line_total']) : ($it_price * $it_qty));
                                        ?>
                                            <div style="display:flex; gap:8px; align-items:center; background:#f8fafc; padding:6px; border-radius:4px; border:1px solid #e2e8f0;">
                                                <img src="<?php echo esc_url($img); ?>" style="width:36px; height:45px; object-fit:cover; border-radius:3px; flex-shrink:0; border:1px solid #cbd5e1;" />
                                                <div style="min-width:0; flex:1; line-height:1.25;">
                                                    <div style="font-weight:600; font-size:12px; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo esc_attr($it['product_name']); ?>">
                                                        <?php echo esc_html($it['product_name']); ?>
                                                    </div>
                                                    <?php if ($variant_text): ?>
                                                        <span style="display:inline-block; font-size:11px; background:#e2e8f0; color:#334155; padding:1px 5px; border-radius:3px; margin:2px 0;">
                                                            <?php echo esc_html($variant_text); ?>
                                                        </span><br>
                                                    <?php endif; ?>
                                                    <span style="font-size:11px; color:#64748b;">
                                                        SL: <b style="color:#0f172a">x<?php echo $it_qty; ?></b> &bull; <?php echo number_format($it_total, 0, ',', '.'); ?>₫
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:12px; font-style:italic;">Không có chi tiết</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:13px"><?php echo esc_html($o['customer_address']); ?></span>
                                <?php if (!empty($o['notes'])): ?>
                                    <div style="margin-top:4px; font-size:11px; color:#b45309; background:#fef3c7; padding:2px 6px; border-radius:3px; display:inline-block;">
                                        <b>Ghi chú:</b> <?php echo esc_html($o['notes']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><span style="text-transform:uppercase;font-weight:700;font-size:12px;color:#475569"><?php echo esc_html($o['payment_method']); ?></span></td>
                            <td><b style="color:#2563eb;font-size:14px"><?php echo number_format($o['total'], 0, ',', '.'); ?>₫</b></td>
                            <td>
                                <form method="post" action="" style="display:flex;align-items:center;gap:6px">
                                    <?php wp_nonce_field('kien_update_order_nonce'); ?>
                                    <input type="hidden" name="update_order_id" value="<?php echo $o['id']; ?>">
                                    <select name="new_status" style="font-size:12px">
                                        <option value="pending" <?php selected($o['order_status'], 'pending'); ?>>Chờ xử lý</option>
                                        <option value="processing" <?php selected($o['order_status'], 'processing'); ?>>Đang đóng gói</option>
                                        <option value="shipping" <?php selected($o['order_status'], 'shipping'); ?>>Đang giao hàng</option>
                                        <option value="completed" <?php selected($o['order_status'], 'completed'); ?>>Đã hoàn thành</option>
                                        <option value="cancelled" <?php selected($o['order_status'], 'cancelled'); ?>>Đã hủy</option>
                                    </select>
                                    <input type="submit" class="button button-small" value="Lưu">
                                </form>
                            </td>
                            <td><span style="font-size:12px;color:#64748b"><?php echo esc_html($o['created_at']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php
}

// 6. Render Page: Tin nhắn liên hệ
function kien_render_admin_contacts_page() {
    global $wpdb;
    $t_contacts = $wpdb->prefix . 'kien_contacts';

    // Handle delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $del_id = absint($_GET['id']);
        check_admin_referer('kien_del_contact_' . $del_id);
        $wpdb->delete($t_contacts, array('id' => $del_id));
        echo '<div class="notice notice-success is-dismissible"><p>Đã xóa tin nhắn #' . $del_id . '!</p></div>';
    }

    $contacts = $wpdb->get_results("SELECT * FROM {$t_contacts} ORDER BY id DESC LIMIT 50", ARRAY_A);

    ?>
    <div class="wrap kien-admin-wrap">
        <div class="kien-admin-header">
            <div>
                <h1 class="kien-admin-title"><span class="dashicons dashicons-email"></span> Tin Nhắn Khách Hàng Gửi Từ Website</h1>
                <p style="color:#64748b;margin:4px 0 0">Danh sách ý kiến đóng góp, thắc mắc và yêu cầu tư vấn gửi qua form Liên hệ.</p>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th style="width:160px">Họ tên</th>
                    <th style="width:140px">Liên hệ</th>
                    <th style="width:180px">Chủ đề</th>
                    <th>Nội dung lời nhắn</th>
                    <th style="width:130px">Thời gian gửi</th>
                    <th style="width:70px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">Chưa có tin nhắn nào từ khách hàng!</td></tr>
                <?php else: ?>
                    <?php foreach ($contacts as $c): 
                        $del_url = wp_nonce_url(admin_url('admin.php?page=kien-contacts&action=delete&id=' . $c['id']), 'kien_del_contact_' . $c['id']);
                    ?>
                        <tr>
                            <td>#<?php echo $c['id']; ?></td>
                            <td><b><?php echo esc_html($c['name']); ?></b></td>
                            <td>
                                <span class="dashicons dashicons-phone"></span> <?php echo esc_html($c['phone']); ?><br>
                                <span style="font-size:11px;color:#64748b"><?php echo esc_html($c['email']); ?></span>
                            </td>
                            <td><span style="font-weight:600;color:#2563eb"><?php echo esc_html($c['subject']); ?></span></td>
                            <td><div style="font-size:13px;line-height:1.5"><?php echo nl2br(esc_html($c['message'])); ?></div></td>
                            <td><span style="font-size:12px;color:#64748b"><?php echo esc_html($c['created_at']); ?></span></td>
                            <td>
                                <a href="<?php echo $del_url; ?>" class="button button-small button-link-delete" onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// 7. Seed Fashion Articles into WordPress wp_posts on admin_init if empty
add_action('admin_init', 'kien_seed_fashion_posts');
function kien_seed_fashion_posts() {
    $seeded = get_option('kien_fashion_posts_seeded', false);
    if ($seeded) return;

    // Check if any fashion posts exist
    $existing = get_posts(array(
        'post_type' => 'post',
        'numberposts' => 1,
        's' => 'Streetwear'
    ));
    if (!empty($existing)) {
        update_option('kien_fashion_posts_seeded', 1);
        return;
    }

    // Categories
    $cat_trend = wp_create_category('Xu Hướng 2026');
    $cat_mix = wp_create_category('Bí Quyết Phối Đồ');
    $cat_fabric = wp_create_category('Chất Liệu Cao Cấp');
    $cat_life = wp_create_category('Phong Cách Sống');

    $articles = array(
        array(
            'title' => '7 Xu Hướng Thời Trang Đường Phố (Streetwear) Thống Trị Năm 2026',
            'cat' => $cat_trend,
            'excerpt' => 'Sự trỗi dậy của phong cách Gorpcore kết hợp phom dáng Oversize tối giản và chất liệu bền vững định hình nên ngôn ngữ thời trang đường phố trẻ trung năm nay.',
            'content' => '<p><img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=900&auto=format&fit=crop&q=80" alt="Streetwear 2026"></p><p>Năm 2026 đánh dấu bước chuyển mình mạnh mẽ của xu hướng thời trang đường phố (Streetwear) toàn cầu. Không còn là những hình in cầu kỳ hay logo cỡ lớn phô trương, giới trẻ hiện đại hướng tới sự kết hợp tinh tế giữa <b>công năng vận động (Utility)</b> và <b>chủ nghĩa tối giản đương đại</b>.</p><h3>1. Sự lên ngôi của phom dáng Boxy & Oversize cân đối</h3><p>Những chiếc áo thun và sơ mi phom dáng boxy cut ngắn ngang thắt lưng nhưng mở rộng phần vai tạo nên tỉ lệ cơ thể cực kỳ khỏe khoắn. Khi phối cùng quần jeans ống suông hoặc quần túi hộp kaki, set đồ mang lại vẻ ngoài năng động mà không hề luộm thuộm.</p><blockquote>"Thời trang đường phố năm 2026 không chỉ là mặc đẹp, mà là tuyên ngôn tự tin về lối sống tự do, phóng khoáng và tràn đầy năng lượng tích cực." — Mai Đàm Thế Kiên</blockquote><h3>2. Gam màu trung tính kết hợp điểm nhấn rực rỡ</h3><p>Bảng màu chủ đạo của năm nay nghiêng về các tông màu đất trầm ấm như: Nâu khói (Smoky Brown), Xanh rêu quân đội (Army Moss), Be cát và Đen nhám.</p>'
        ),
        array(
            'title' => 'Bí Quyết Chọn Size Áo Sơ Mi Nam Chuẩn Quý Ông: Slim-fit Hay Regular?',
            'cat' => $cat_mix,
            'excerpt' => 'Hướng dẫn chi tiết từ số đo vòng cổ, độ rộng vai đến chiều dài tay áo giúp bạn chọn được chiếc sơ mi công sở vừa vặn hoàn hảo như may đo.',
            'content' => '<p><img src="https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=900&auto=format&fit=crop&q=80" alt="Shirt Guide"></p><p>Một chiếc áo sơ mi vừa vặn có thể nâng tầm diện mạo của phái mạnh ngay lập tức. Tuy nhiên, rất nhiều quý ông vẫn băn khoăn giữa hai phom dáng phổ biến nhất: <b>Slim-fit (ôm nhẹ)</b> và <b>Regular (tiêu chuẩn)</b>.</p><h3>Nên chọn Slim-fit khi nào?</h3><p>Phom Slim-fit được chiết nhẹ ở phần eo và lưng, phù hợp với những người có thể hình cân đối, săn chắc hoặc vóc dáng mảnh khảnh. Khi sơ vin cùng quần tây âu, sơ mi Slim-fit mang lại cảm giác trẻ trung, thanh thoát và hiện đại.</p>'
        ),
        array(
            'title' => 'Nghệ Thuật Phối Đồ Layering Mùa Lạnh: Ấm Áp Mà Vẫn Thanh Lịch',
            'cat' => $cat_mix,
            'excerpt' => 'Quy tắc 3 lớp thần thánh: Lớp lót thoáng khí, lớp giữ nhiệt mềm mại và lớp áo khoác blazer định hình phom dáng cho những ngày gió mùa.',
            'content' => '<p><img src="https://images.unsplash.com/photo-1544441893-675973e31985?w=900&auto=format&fit=crop&q=80" alt="Layering Tips"></p><p>Layering (mặc nhiều lớp trang phục) là thử thách thú vị giúp bạn thể hiện gu thẩm mỹ đỉnh cao trong mùa thu đông. Làm sao để mặc 3-4 lớp áo mà vóc dáng vẫn gọn gàng, thanh thoát?</p><h3>Quy tắc 1: Từ mỏng nhất đến dày nhất</h3><p>Lớp trong cùng tiếp xúc với da nên là áo thun cotton 100% mỏng nhẹ thấm hút mồ hôi. Lớp giữa là áo sơ mi hoặc áo len dệt kim mỏng để cách nhiệt. Lớp ngoài cùng là áo khoác Blazer 2 lớp hoặc Bomber chống gió.</p>'
        ),
        array(
            'title' => 'Khám Phá Sợi Cotton Compact: Bí Mật Tạo Nên Chiếc Áo Thun Không Bai Dão',
            'cat' => $cat_fabric,
            'excerpt' => 'Tại sao sợi Cotton Compact lại có độ bền gấp đôi sợi cotton thông thường và được các thương hiệu thời trang cao cấp thế giới ưa chuộng?',
            'content' => '<p><img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80" alt="Cotton Compact"></p><p>Cotton luôn là chất liệu may mặc phổ biến nhất hành tinh, nhưng không phải loại cotton nào cũng giống nhau. <b>Cotton Compact (Bông nén cơ học)</b> là bước tiến đột phá trong công nghệ kéo sợi hiện đại.</p><ul><li><b>Không xù lông:</b> Các xơ bông ngắn được nén ép vào trục sợi nhờ luồng khí áp lực cao.</li><li><b>Bền màu gấp đôi:</b> Cấu trúc sợi chặt chẽ giữ màu bền sau 50 lần giặt.</li></ul>'
        ),
        array(
            'title' => 'Phong Cách Minimalism: Khi Sự Tối Giản Tạo Nên Khí Chất Và Đẳng Cấp',
            'cat' => $cat_trend,
            'excerpt' => 'Cách xây dựng tủ đồ con nhộng (Capsule Wardrobe) với chỉ 10 món đồ cơ bản nhưng phối được hơn 30 set đồ sang trọng mỗi ngày.',
            'content' => '<p><img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=900&auto=format&fit=crop&q=80" alt="Minimalism Fashion"></p><p><i>"Less is more"</i> — Bớt đi những chi tiết thừa thãi là cách nhanh nhất để làm nổi bật thần thái tự nhiên của người mặc. Phong cách thời trang tối giản Minimalism không bao giờ lỗi mốt vì nó tập trung vào giá trị cốt lõi: <b>đường may chuẩn xác và chất vải thượng hạng</b>.</p>'
        ),
        array(
            'title' => 'THEKIEN Fashion Ra Mắt Bộ Sưu Tập Lấy Cảm Hứng Từ Đại Ngàn Tây Nguyên',
            'cat' => $cat_life,
            'excerpt' => 'Hòa quyện giữa vẻ đẹp mộc mạc của đất đỏ bazan và tinh thần thời trang đương đại, dự án đồ án tốt nghiệp đem đến hơi thở mới cho thời trang sinh viên.',
            'content' => '<p><img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=900&auto=format&fit=crop&q=80" alt="Highlands Spirit"></p><p>Được ấp ủ từ niềm đam mê với mảnh đất Tây Nguyên đầy nắng gió và công nghệ trí tuệ nhân tạo (AI), sinh viên <b>Mai Đàm Thế Kiên (MSSV: 23103035 - Đại học Tây Nguyên)</b> đã kiến tạo nên nền tảng mua sắm thời trang tích hợp trợ lý ảo <b>THEKIEN FASHION & AI STYLIST</b>.</p>'
        )
    );

    foreach ($articles as $a) {
        $post_id = wp_insert_post(array(
            'post_title' => $a['title'],
            'post_content' => $a['content'],
            'post_excerpt' => $a['excerpt'],
            'post_status' => 'publish',
            'post_author' => 1,
            'post_category' => array($a['cat'])
        ));
    }

    // Trash default "Hello world!"
    $hw = get_page_by_title('Hello world!', OBJECT, 'post');
    if ($hw) wp_trash_post($hw->ID);

    update_option('kien_fashion_posts_seeded', 1);
}

// 8. API: Get News Articles from WordPress Posts
function kien_get_news() {
    kien_ajax_check();
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 50,
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    $news = array();
    foreach ($posts as $p) {
        $cats = get_the_category($p->ID);
        $cat_name = !empty($cats) ? $cats[0]->name : 'Tin tức';
        $cat_slug = !empty($cats) ? $cats[0]->slug : 'all';
        if ($cat_slug === 'uncategorized') $cat_slug = 'all';

        // Extract image
        $image = get_the_post_thumbnail_url($p->ID, 'large');
        if (!$image) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $p->post_content, $m)) {
                $image = $m[1];
            } else {
                $image = 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=900&auto=format&fit=crop&q=80';
            }
        }

        $excerpt = $p->post_excerpt;
        if (empty($excerpt)) {
            $excerpt = wp_trim_words(strip_shortcodes(strip_tags($p->post_content)), 30, '...');
        }

        $news[] = array(
            'id' => $p->ID,
            'title' => $p->post_title,
            'slug' => $p->post_name,
            'category' => $cat_slug,
            'categoryLabel' => $cat_name,
            'readTime' => max(2, ceil(str_word_count(strip_tags($p->post_content)) / 150)) . ' phút đọc',
            'date' => get_the_date('d/m/Y', $p->ID),
            'author' => get_the_author_meta('display_name', $p->post_author) ?: 'THEKIEN Stylist',
            'image' => $image,
            'excerpt' => $excerpt,
            'content' => apply_filters('the_content', $p->post_content)
        );
    }

    wp_send_json_success(array('articles' => $news));
}
add_action('wp_ajax_kien_get_news', 'kien_get_news');
add_action('wp_ajax_nopriv_kien_get_news', 'kien_get_news');


// Color Hex Helper for Teelab Color Dots
function kien_get_color_hex($col) {
    $map = array(
        'đen' => '#18181b', 'trắng' => '#ffffff', 'xanh' => '#2563eb', 'xanh navy' => '#1e3a8a',
        'xanh rêu' => '#3f6212', 'xám' => '#64748b', 'ghi' => '#94a3b8', 'be' => '#f5f5dc',
        'nâu' => '#78350f', 'đỏ' => '#dc2626', 'hồng' => '#f472b6', 'vàng' => '#eab308'
    );
    $key = mb_strtolower(trim($col), 'UTF-8');
    foreach ($map as $k => $hex) {
        if (strpos($key, $k) !== false) return $hex;
    }
    return '#64748b';
}


// Render Page: Cài đặt Giao diện, Banner & Logo Tổng Quan
function kien_render_admin_theme_settings_page() {
    global $wpdb;
    $t_settings = $wpdb->prefix . 'kien_settings';

    // Xử lý lưu cài đặt khi submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_kien_theme_settings'])) {
        check_admin_referer('kien_save_theme_settings_nonce');

        $fields = array(
            'site_logo', 'site_logo_fixed', 'site_favicon', 'shop_name', 'shop_tagline',
            'shop_phone', 'shop_email', 'shop_address',
            'banner1_img', 'banner1_img_mb', 'banner1_title', 'banner1_btn', 'banner1_link',
            'slider2_img', 'slider2_img_mb', 'slider2_title', 'slider2_btn1_text', 'slider2_btn1_link',
            'slider2_btn2_text', 'slider2_btn2_link'
        );

        foreach ($fields as $field) {
            $val = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            $wpdb->replace($t_settings, array('setting_key' => $field, 'setting_value' => $val));
        }

        echo '<div class="notice notice-success is-dismissible" style="margin:20px 0;"><p><b>Thành công:</b> Đã lưu toàn bộ cấu hình Banner, Logo và thông tin Website thành công!</p></div>';
    }

    // Giá trị hiện tại
    $logo = kien_get_theme_setting('site_logo', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo.png?1793084272807');
    $logo_fixed = kien_get_theme_setting('site_logo_fixed', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/logo-fixed.png?1793084272807');
    $favicon = kien_get_theme_setting('site_favicon', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/favicon.png?1793084272807');
    $shop_name = kien_get_theme_setting('shop_name', 'teelab:alter');
    $shop_tagline = kien_get_theme_setting('shop_tagline', 'Thời trang Streetwear phong cách hiện đại');
    $shop_phone = kien_get_theme_setting('shop_phone', '0987.654.321');
    $shop_email = kien_get_theme_setting('shop_email', 'contact@thekien05.vn');
    $shop_address = kien_get_theme_setting('shop_address', '567 Lê Duẩn, TP. Buôn Ma Thuột, Đắk Lắk');

    $b1_img = kien_get_theme_setting('banner1_img', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/image_2banner_1.jpg?1793084272807');
    $b1_img_mb = kien_get_theme_setting('banner1_img_mb', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/image_2banner_1_mb.jpg?1793084272807');
    $b1_title = kien_get_theme_setting('banner1_title', 'WORLD TOUR: TOKYO');
    $b1_btn = kien_get_theme_setting('banner1_btn', 'NEW ARRIVALS');
    $b1_link = kien_get_theme_setting('banner1_link', home_url('/?product_slug=world-tour-tokyo'));

    $s2_img = kien_get_theme_setting('slider2_img', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/slider_1.jpg?1793084272807');
    $s2_img_mb = kien_get_theme_setting('slider2_img_mb', 'https://bizweb.dktcdn.net/100/575/016/themes/1111006/assets/slider_mb_1.jpg?1793084272807');
    $s2_title = kien_get_theme_setting('slider2_title', 'TEELAB ALTER ESSENTIALS FW26');
    $s2_btn1_text = kien_get_theme_setting('slider2_btn1_text', 'SHOP NOW');
    $s2_btn1_link = kien_get_theme_setting('slider2_btn1_link', home_url('/?product_slug=teelab-alter-essentials-fw26-1'));
    $s2_btn2_text = kien_get_theme_setting('slider2_btn2_text', 'THE STORY');
    $s2_btn2_link = kien_get_theme_setting('slider2_btn2_link', home_url('/?page=lookbook'));
    ?>
    <div class="wrap kien-admin-wrap">
        <div class="kien-admin-header">
            <div>
                <h1 class="kien-admin-title"><span class="dashicons dashicons-format-image"></span> Quản Lý Giao Diện, Banner & Logo</h1>
                <p style="color:#64748b;margin:4px 0 0">Tùy chỉnh logo, biểu tượng favicon, các banner chính và thông tin thương hiệu hiển thị trên toàn website.</p>
            </div>
            <div>
                <a href="<?php echo home_url('/'); ?>" target="_blank" class="button button-secondary" style="font-weight:600;"><span class="dashicons dashicons-external" style="margin-top:4px;"></span> Xem website ngoài trang chủ</a>
            </div>
        </div>

        <form method="post" action="" style="max-width:1100px;">
            <?php wp_nonce_field('kien_save_theme_settings_nonce'); ?>
            <input type="hidden" name="save_kien_theme_settings" value="1">

            <!-- Card 1: Logo & Thương hiệu -->
            <div class="kien-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0; font-size:17px; font-weight:700; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <span class="dashicons dashicons-art"></span> 1. Logo & Nhận diện thương hiệu
                </h2>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:16px;">
                    <!-- Logo chính -->
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Logo chính (Header)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="site_logo" name="site_logo" value="<?php echo esc_attr($logo); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#site_logo" data-preview="#preview_site_logo">Chọn ảnh</button>
                        </div>
                        <div id="preview_site_logo" style="background:#0f172a; padding:10px; border-radius:6px; display:inline-block; border:1px solid #334155;">
                            <img src="<?php echo esc_url($logo); ?>" style="max-height:36px; max-width:200px; display:block;" />
                        </div>
                    </div>

                    <!-- Logo Fixed / Cuộn -->
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Logo nhỏ (Khi cuộn trang / Mobile)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="site_logo_fixed" name="site_logo_fixed" value="<?php echo esc_attr($logo_fixed); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#site_logo_fixed" data-preview="#preview_site_logo_fixed">Chọn ảnh</button>
                        </div>
                        <div id="preview_site_logo_fixed" style="background:#0f172a; padding:10px; border-radius:6px; display:inline-block; border:1px solid #334155;">
                            <img src="<?php echo esc_url($logo_fixed); ?>" style="max-height:36px; max-width:50px; display:block;" />
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:20px;">
                    <!-- Favicon -->
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Favicon (Biểu tượng Tab)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="site_favicon" name="site_favicon" value="<?php echo esc_attr($favicon); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#site_favicon" data-preview="#preview_site_favicon">Chọn</button>
                        </div>
                        <div id="preview_site_favicon" style="padding:6px; border-radius:4px; display:inline-block; border:1px solid #e2e8f0; background:#fff;">
                            <img src="<?php echo esc_url($favicon); ?>" style="width:24px; height:24px; display:block;" />
                        </div>
                    </div>

                    <!-- Tên thương hiệu -->
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Tên thương hiệu & Khẩu hiệu</label>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <input type="text" name="shop_name" value="<?php echo esc_attr($shop_name); ?>" placeholder="Tên shop (VD: teelab:alter)" style="width:100%;">
                            <input type="text" name="shop_tagline" value="<?php echo esc_attr($shop_tagline); ?>" placeholder="Khẩu hiệu / Slogan" style="width:100%;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Banner Đầu Trang (Hero 1 - World Tour) -->
            <div class="kien-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0; font-size:17px; font-weight:700; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <span class="dashicons dashicons-desktop"></span> 2. Banner chính đầu trang (Hero Banner 1 - World Tour)
                </h2>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Ảnh Banner Desktop (Kích thước khuyên dùng: 1920x1080px hoặc 2000x1200px)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="banner1_img" name="banner1_img" value="<?php echo esc_attr($b1_img); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#banner1_img" data-preview="#preview_b1_img">Chọn ảnh</button>
                        </div>
                        <div id="preview_b1_img" style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; max-height:120px;">
                            <img src="<?php echo esc_url($b1_img); ?>" style="width:100%; height:120px; object-fit:cover; display:block;" />
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Ảnh Banner Mobile (Kích thước khuyên dùng: 750x1200px)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="banner1_img_mb" name="banner1_img_mb" value="<?php echo esc_attr($b1_img_mb); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#banner1_img_mb" data-preview="#preview_b1_img_mb">Chọn ảnh</button>
                        </div>
                        <div id="preview_b1_img_mb" style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; max-height:120px; width:100px;">
                            <img src="<?php echo esc_url($b1_img_mb); ?>" style="width:100%; height:120px; object-fit:cover; display:block;" />
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1.5fr; gap:16px;">
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Tiêu đề hiển thị trên Banner</label>
                        <input type="text" name="banner1_title" value="<?php echo esc_attr($b1_title); ?>" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Chữ trên nút bấm</label>
                        <input type="text" name="banner1_btn" value="<?php echo esc_attr($b1_btn); ?>" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Đường link khi click vào</label>
                        <input type="text" name="banner1_link" value="<?php echo esc_attr($b1_link); ?>" style="width:100%;">
                    </div>
                </div>
            </div>

            <!-- Card 3: Banner Slider Thứ 2 (Essentials FW26) -->
            <div class="kien-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0; font-size:17px; font-weight:700; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <span class="dashicons dashicons-images-alt2"></span> 3. Banner Slider thứ 2 (Essentials Collection)
                </h2>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Ảnh Slider Desktop (1920x1080px)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="slider2_img" name="slider2_img" value="<?php echo esc_attr($s2_img); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#slider2_img" data-preview="#preview_s2_img">Chọn ảnh</button>
                        </div>
                        <div id="preview_s2_img" style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; max-height:120px;">
                            <img src="<?php echo esc_url($s2_img); ?>" style="width:100%; height:120px; object-fit:cover; display:block;" />
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Ảnh Slider Mobile (750x1200px)</label>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="slider2_img_mb" name="slider2_img_mb" value="<?php echo esc_attr($s2_img_mb); ?>" class="regular-text" style="width:100%;">
                            <button type="button" class="button kien-upload-btn" data-target="#slider2_img_mb" data-preview="#preview_s2_img_mb">Chọn ảnh</button>
                        </div>
                        <div id="preview_s2_img_mb" style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; max-height:120px; width:100px;">
                            <img src="<?php echo esc_url($s2_img_mb); ?>" style="width:100%; height:120px; object-fit:cover; display:block;" />
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Tiêu đề hiển thị trên Slider</label>
                    <input type="text" name="slider2_title" value="<?php echo esc_attr($s2_title); ?>" style="width:100%; max-width:500px;">
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div style="background:#f8fafc; padding:12px; border-radius:6px; border:1px solid #e2e8f0;">
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Nút bấm 1 (Chính)</label>
                        <input type="text" name="slider2_btn1_text" value="<?php echo esc_attr($s2_btn1_text); ?>" placeholder="Tên nút (VD: SHOP NOW)" style="width:100%; margin-bottom:6px;">
                        <input type="text" name="slider2_btn1_link" value="<?php echo esc_attr($s2_btn1_link); ?>" placeholder="Đường link nút 1" style="width:100%;">
                    </div>

                    <div style="background:#f8fafc; padding:12px; border-radius:6px; border:1px solid #e2e8f0;">
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Nút bấm 2 (Phụ)</label>
                        <input type="text" name="slider2_btn2_text" value="<?php echo esc_attr($s2_btn2_text); ?>" placeholder="Tên nút (VD: THE STORY)" style="width:100%; margin-bottom:6px;">
                        <input type="text" name="slider2_btn2_link" value="<?php echo esc_attr($s2_btn2_link); ?>" placeholder="Đường link nút 2" style="width:100%;">
                    </div>
                </div>
            </div>

            <!-- Card 4: Hotline & Chân trang -->
            <div class="kien-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0; font-size:17px; font-weight:700; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <span class="dashicons dashicons-phone"></span> 4. Thông tin liên hệ & Chân trang (Footer)
                </h2>

                <div style="display:grid; grid-template-columns: 1fr 1fr 2fr; gap:16px;">
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Hotline mua hàng</label>
                        <input type="text" name="shop_phone" value="<?php echo esc_attr($shop_phone); ?>" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Email CSKH</label>
                        <input type="email" name="shop_email" value="<?php echo esc_attr($shop_email); ?>" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#334155;">Địa chỉ cửa hàng / Showroom</label>
                        <input type="text" name="shop_address" value="<?php echo esc_attr($shop_address); ?>" style="width:100%;">
                    </div>
                </div>
            </div>

            <!-- Nút Lưu -->
            <div style="margin-top:20px; padding:15px 0;">
                <button type="submit" class="button button-primary button-hero" style="background:#0f172a; border-color:#0f172a; font-weight:700; padding:10px 30px; font-size:15px; border-radius:8px;">
                    💾 LƯU CẤU HÌNH GIAO DIỆN & BANNER
                </button>
            </div>
        </form>
    </div>

    <!-- Script Media Uploader WordPress -->
    <script>
    jQuery(document).ready(function($) {
        $('.kien-upload-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var targetInput = $btn.data('target');
            var previewBox = $btn.data('preview');

            var customUploader = wp.media({
                title: 'Chọn hoặc Tải ảnh lên',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            }).on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                $(targetInput).val(attachment.url);
                if (previewBox) {
                    $(previewBox).find('img').attr('src', attachment.url);
                }
            }).open();
        });
    });
    </script>
    <?php
}

// ================= REST API & AJAX 34 TỈNH THÀNH (SÁP NHẬP MỚI) =================
add_action('rest_api_init', function () {
    register_rest_route('kien/v1', '/provinces34', array(
        'methods'  => 'GET',
        'callback' => 'kien_rest_get_provinces_34',
        'permission_callback' => '__return_true'
    ));
});

function kien_rest_get_provinces_34($request = null) {
    $json_file = get_template_directory() . '/api/provinces_34.json';
    if (!file_exists($json_file)) {
        return new WP_Error('no_data', 'Dữ liệu không khả dụng', array('status' => 404));
    }
    $raw = file_get_contents($json_file);
    $data = json_decode($raw, true);

    $prov_param = $request ? $request->get_param('province') : '';
    $key_param = $request ? $request->get_param('key') : '';
    $depth = $request ? intval($request->get_param('depth')) : 3;

    if (!empty($prov_param) || !empty($key_param)) {
        $target = !empty($prov_param) ? $prov_param : $key_param;
        foreach ($data as $item) {
            if ($item['key'] === $target || $item['name'] === $target || strpos($item['name'], $target) !== false) {
                return rest_ensure_response(array(
                    'province' => $item['name'],
                    'key' => $item['key'],
                    'total_wards' => $item['total_wards'],
                    'wards' => $item['wards']
                ));
            }
        }
        return new WP_Error('not_found', 'Không tìm thấy tỉnh thành', array('status' => 404));
    }

    if ($depth === 1) {
        $summary = array();
        foreach ($data as $item) {
            $summary[] = array(
                'id' => $item['id'],
                'code' => $item['code'],
                'key' => $item['key'],
                'name' => $item['name'],
                'total_wards' => $item['total_wards']
            );
        }
        return rest_ensure_response($summary);
    }

    return rest_ensure_response($data);
}

add_action('wp_ajax_kien_get_provinces_34', 'kien_ajax_get_provinces_34');
add_action('wp_ajax_nopriv_kien_get_provinces_34', 'kien_ajax_get_provinces_34');
function kien_ajax_get_provinces_34() {
    $res = kien_rest_get_provinces_34(null);
    if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
    } else {
        wp_send_json_success($res->get_data());
    }
}
