import re

with open('scratch_teelab_single.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

print('Original Teelab Single HTML length:', len(html))

# 1. Fix protocol-relative URLs
html = html.replace('href="//bizweb.dktcdn.net/', 'href="https://bizweb.dktcdn.net/')
html = html.replace('src="//bizweb.dktcdn.net/', 'src="https://bizweb.dktcdn.net/')
html = html.replace('srcset="//bizweb.dktcdn.net/', 'srcset="https://bizweb.dktcdn.net/')
html = html.replace('url(//bizweb.dktcdn.net/', 'url(https://bizweb.dktcdn.net/')
html = html.replace('url("//bizweb.dktcdn.net/', 'url("https://bizweb.dktcdn.net/')
html = html.replace("url('//bizweb.dktcdn.net/", "url('https://bizweb.dktcdn.net/")

# 2. Fix data-src
html = re.sub(
    r'data-src=[\'"]([^\'"]+)[\'"]',
    r'data-src="\1" src="\1"',
    html,
    flags=re.I
)

# 3. Map domain links to local site
html = re.sub(r'href=[\'"]https://teelab\.vn/?[\'"]', 'href="<?php echo home_url("/"); ?>"', html)
html = re.sub(r'href=[\'"]/?[\'"]\s+class=[\'"]logo-wrapper[\'"]', 'href="<?php echo home_url("/"); ?>" class="logo-wrapper"', html)
html = re.sub(r'href=[\'"]/#[\'"]', 'href="<?php echo home_url("/"); ?>"', html)

def replace_product_link(m):
    slug = m.group(1).strip('/')
    if slug in ['account', 'cart', 'search', 'lookbook', 'he-thong-cua-hang', 'apps', 'collections']:
        return f'href="<?php echo home_url("/?page={slug}"); ?>"'
    return f'href="<?php echo home_url("/?product_slug={slug}"); ?>"'

html = re.sub(r'href=[\'"]https://teelab\.vn/([a-zA-Z0-9_\-]+)/?[\'"]', replace_product_link, html)
html = re.sub(r'href=[\'"]/([a-zA-Z0-9_\-]+)/?[\'"]', replace_product_link, html)

# 4. Use local downloaded assets
theme_uri = "<?php echo get_template_directory_uri(); ?>"
html = re.sub(r'href=[\'"][^\'"]*bootstrap-min\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_bootstrap.css"', html)
html = re.sub(r'href=[\'"][^\'"]*main\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_main.css"', html)
html = re.sub(r'href=[\'"][^\'"]*index\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_index.css"', html)
html = re.sub(r'href=[\'"][^\'"]*swiper\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_swiper.css"', html)
html = re.sub(r'href=[\'"][^\'"]*ajaxcart\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_ajaxcart.css"', html)
html = re.sub(r'href=[\'"][^\'"]*bpr-products-module\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_bpr.css"', html)
html = re.sub(r'href=[\'"][^\'"]*breadcrumb_style\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_breadcrumb.css"', html)
html = re.sub(r'href=[\'"][^\'"]*picbox\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_picbox.css"', html)
html = re.sub(r'href=[\'"][^\'"]*product_style\.scss\.css[^\'"]*[\'"]', f'href="{theme_uri}/assets/css/teelab_product_style.css"', html)

html = re.sub(r'src=[\'"][^\'"]*jquery\.js[^\'"]*[\'"]', f'src="{theme_uri}/assets/js/jquery.js"', html)
html = re.sub(r'src=[\'"][^\'"]*swiper\.js[^\'"]*[\'"]', f'src="{theme_uri}/assets/js/swiper.js"', html)
html = re.sub(r'src=[\'"][^\'"]*lazy\.js[^\'"]*[\'"]', f'src="{theme_uri}/assets/js/lazy.js"', html)
html = re.sub(r'src=[\'"][^\'"]*main\.js[^\'"]*[\'"]', f'src="{theme_uri}/assets/js/teelab_main.js"', html)
html = re.sub(r'src=[\'"][^\'"]*index\.js[^\'"]*[\'"]', f'src="{theme_uri}/assets/js/teelab_index.js"', html)

# Ensure wp_head() and wp_footer() are integrated
html = html.replace('</head>', '<?php wp_head(); ?>\n</head>')
html = html.replace('</body>', '<?php wp_footer(); ?>\n</body>')

php_top = '''<?php
/**
 * Standalone Teelab Product Detail Page
 */
if (!defined('ABSPATH')) exit;
?>
'''

with open('e:/web quản lý bán hàng wordpress/ht-tracker-v3/single-product.php', 'w', encoding='utf-8') as f:
    f.write(php_top + html)

print('Successfully generated Teelab single-product.php! Length:', len(html))

