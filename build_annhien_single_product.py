import re

with open('scratch_real_product.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# 1. Fix lazy loading images
html = re.sub(
    r'src=[\'"][^\'"]*lazy\.png[\'"]\s+data-src=[\'"]([^\'"]+)[\'"]',
    r'src="\1" data-src="\1"',
    html,
    flags=re.I
)
html = re.sub(
    r'data-src=[\'"]([^\'"]+)[\'"]\s+class=[\'"]([^\'"]*)[\'"]\s+src=[\'"][^\'"]*lazy\.png[\'"]',
    r'src="\1" data-src="\1" class="\2"',
    html,
    flags=re.I
)
html = re.sub(
    r'src=[\'"]data:image/png;base64,[^\'"]*[\'"]\s+data-src=[\'"]([^\'"]+)[\'"]',
    r'src="\1" data-src="\1"',
    html,
    flags=re.I
)

# 2. Protect uploads and themes
html = html.replace('https://thaoduocannhien.net/wp-content/uploads/', '###WP_UPLOADS###')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/', '###WP_THEMES###')

# Domain mapping
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/?[\'"]', 'href="<?php echo home_url("/"); ?>"', html)
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/\?action=([^\'"]+)[\'"]', r'href="<?php echo home_url("/?action=\1"); ?>"', html)
html = re.sub(r'action=[\'"]https://thaoduocannhien\.net/?[\'"]', 'action="<?php echo home_url("/"); ?>"', html)

# Category links:
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/danh-muc/([^/\'"]+)/?[\'"]', r'href="<?php echo home_url("/?category=\1"); ?>"', html)

# Product links:
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/san-pham/([^/\'"]+)/?[\'"]', r'href="<?php echo home_url("/?product_slug=\1"); ?>"', html)

# Restore uploads & themes
html = html.replace('###WP_UPLOADS###', 'https://thaoduocannhien.net/wp-content/uploads/')
html = html.replace('###WP_THEMES###', 'https://thaoduocannhien.net/wp-content/themes/')

# Asset URLs
theme_uri = "<?php echo get_template_directory_uri(); ?>"
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bootstrap-4-3-min.css', f'{theme_uri}/assets/css/annhien_bootstrap.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/main.scss.css', f'{theme_uri}/assets/css/annhien_main.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/index.scss.css', f'{theme_uri}/assets/css/annhien_index.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/swiper.scss.css', f'{theme_uri}/assets/css/annhien_swiper.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/ajaxcart.scss.css', f'{theme_uri}/assets/css/annhien_ajaxcart.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bpr.min.css', f'{theme_uri}/assets/css/annhien_bpr.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/fonts.google.css', f'{theme_uri}/assets/css/fonts.google.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/breadcrumb_style.scss.css', f'{theme_uri}/assets/css/breadcrumb_style.scss.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/product_style.scss.css', f'{theme_uri}/assets/css/product_style.scss.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/picbox.scss.css', f'{theme_uri}/assets/css/picbox.scss.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/sweetalert.min.css', f'{theme_uri}/assets/css/sweetalert.min.css')

html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/jquery.js', f'{theme_uri}/assets/js/jquery.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/swiper.js', f'{theme_uri}/assets/js/swiper.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/lazy.js', f'{theme_uri}/assets/js/lazy.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bootstrap.min.js', f'{theme_uri}/assets/js/bootstrap.min.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/placeholdertypewriter.js', f'{theme_uri}/assets/js/placeholdertypewriter.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/main.js', f'{theme_uri}/assets/js/annhien_main.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/index.js', f'{theme_uri}/assets/js/annhien_index.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/pub_fnc.js', f'{theme_uri}/assets/js/annhien_pub_fnc.js')

html = html.replace('https://thaoduocannhien.net/wp-admin/admin-ajax.php', "<?php echo admin_url('admin-ajax.php'); ?>")

# Google Fonts & FontAwesome
font_link = '''
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
body, button, input, select, textarea {
    font-family: 'Quicksand', sans-serif !important;
}
.header-nav .item_big > li > a {
    font-family: 'Quicksand', sans-serif !important;
}
</style>
'''
html = html.replace('</head>', f'{font_link}\n<?php wp_head(); ?>\n</head>')
html = html.replace('</body>', '<?php wp_footer(); ?>\n</body>')

php_top = '''<?php
/**
 * Standalone Product Detail Page (Thảo Dược An Nhiên)
 */
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'kien_products';
$pid = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
?>
'''

with open('e:/web quản lý bán hàng wordpress/ht-tracker-v3/single-product.php', 'w', encoding='utf-8') as f:
    f.write(php_top + html)

print('Done writing single-product.php without syntax errors!')
