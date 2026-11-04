import re

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

print('Original HTML length:', len(html))

# 1. Fix lazy loading images so that src contains the real image URL
# Case 1: src="...lazy.png" data-src="..."
html = re.sub(
    r'src=[\'"][^\'"]*lazy\.png[\'"]\s+data-src=[\'"]([^\'"]+)[\'"]',
    r'src="\1" data-src="\1"',
    html,
    flags=re.I
)
# Case 2: data-src="..." ... src="...lazy.png"
html = re.sub(
    r'data-src=[\'"]([^\'"]+)[\'"]\s+class=[\'"]([^\'"]*)[\'"]\s+src=[\'"][^\'"]*lazy\.png[\'"]',
    r'src="\1" data-src="\1" class="\2"',
    html,
    flags=re.I
)
# Case 3: base64 1x1 png placeholder
html = re.sub(
    r'src=[\'"]data:image/png;base64,[^\'"]*[\'"]\s+data-src=[\'"]([^\'"]+)[\'"]',
    r'src="\1" data-src="\1"',
    html,
    flags=re.I
)
html = re.sub(
    r'data-src=[\'"]([^\'"]+)[\'"]\s+class=[\'"]([^\'"]*)[\'"]\s+src=[\'"]data:image/png;base64,[^\'"]*[\'"]',
    r'src="\1" data-src="\1" class="\2"',
    html,
    flags=re.I
)

# 2. Map domain links to local site
# Replace https://thaoduocannhien.net/ or https://thaoduocannhien.net with <?php echo home_url('/'); ?>
# Keep wp-content uploads to https://thaoduocannhien.net/wp-content/uploads/
# First protect wp-content links
html = html.replace('https://thaoduocannhien.net/wp-content/uploads/', '###WP_UPLOADS###')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/', '###WP_THEMES###')

# Replace home links
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/?[\'"]', 'href="<?php echo home_url(\'/\'); ?>"', html)
html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/\?action=([^\'"]+)[\'"]', r'href="<?php echo home_url(\'/?action=\1\'); ?>"', html)
html = re.sub(r'action=[\'"]https://thaoduocannhien\.net/?[\'"]', 'action="<?php echo home_url(\'/\'); ?>"', html)

# Map product and category links to ?category= or ?product= or preserve them
# For category links:
def map_cat_link(match):
    slug = match.group(1).strip('/')
    return f'href="<?php echo home_url(\'/?category={slug}\'); ?>"'

html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/danh-muc/([^/\'"]+)/?[\'"]', map_cat_link, html)

# For product links:
def map_prod_link(match):
    slug = match.group(1).strip('/')
    return f'href="<?php echo home_url(\'/?product_slug={slug}\'); ?>"'

html = re.sub(r'href=[\'"]https://thaoduocannhien\.net/([^/\'"]+)/?[\'"]', map_prod_link, html)

# Restore uploads
html = html.replace('###WP_UPLOADS###', 'https://thaoduocannhien.net/wp-content/uploads/')
html = html.replace('###WP_THEMES###', 'https://thaoduocannhien.net/wp-content/themes/')

# Also replace theme assets to local theme uri
theme_uri = "<?php echo get_template_directory_uri(); ?>"
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bootstrap-4-3-min.css', f'{theme_uri}/assets/css/annhien_bootstrap.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/main.scss.css', f'{theme_uri}/assets/css/annhien_main.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/index.scss.css', f'{theme_uri}/assets/css/annhien_index.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/swiper.scss.css', f'{theme_uri}/assets/css/annhien_swiper.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/ajaxcart.scss.css', f'{theme_uri}/assets/css/annhien_ajaxcart.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bpr.min.css', f'{theme_uri}/assets/css/annhien_bpr.css')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/fonts.google.css', f'{theme_uri}/assets/css/fonts.google.css')

html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/jquery.js', f'{theme_uri}/assets/js/jquery.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/swiper.js', f'{theme_uri}/assets/js/swiper.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/lazy.js', f'{theme_uri}/assets/js/lazy.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/bootstrap.min.js', f'{theme_uri}/assets/js/bootstrap.min.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/placeholdertypewriter.js', f'{theme_uri}/assets/js/placeholdertypewriter.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/main.js', f'{theme_uri}/assets/js/annhien_main.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/index.js', f'{theme_uri}/assets/js/annhien_index.js')
html = html.replace('https://thaoduocannhien.net/wp-content/themes/hbd/assets/public/pub_fnc.js', f'{theme_uri}/assets/js/annhien_pub_fnc.js')

# Also admin-ajax url:
html = html.replace('https://thaoduocannhien.net/wp-admin/admin-ajax.php', "<?php echo admin_url('admin-ajax.php'); ?>")

# Add Google Fonts link in head for Quicksand and FontAwesome
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

# Add wp_footer() before </body>
html = html.replace('</body>', '<?php wp_footer(); ?>\n</body>')

with open('e:/web quản lý bán hàng wordpress/ht-tracker-v3/index.php', 'w', encoding='utf-8') as f:
    f.write(html)

print('Successfully generated index.php! Length:', len(html))

