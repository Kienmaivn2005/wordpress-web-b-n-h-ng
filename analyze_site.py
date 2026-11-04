import re
import html as html_lib

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

out = []
def p(*args):
    out.append(' '.join(str(a) for a in args))

p('Length:', len(html))
title_m = re.search(r'<title>(.*?)</title>', html, re.I)
p('Title:', title_m.group(1) if title_m else 'None')

themes = set(re.findall(r'/wp-content/themes/([^/\'\"?]+)', html))
p('Themes:', themes)

plugins = set(re.findall(r'/wp-content/plugins/([^/\'\"?]+)', html))
p('Plugins:', list(plugins)[:20])

# Find all stylesheets
css_files = set(re.findall(r'href=[\'"]([^\'"]+\.css[^\'"]*)[\'"]', html))
p('\nCSS files count:', len(css_files))
for c in sorted(list(css_files))[:20]:
    p('  CSS:', c)

# Check theme flags
for kw in ['flatsome', 'elementor', 'woocommerce', 'divi', 'astra', 'generatepress', 'storefront']:
    if kw in html.lower():
        p(f'Keyword {kw} detected in page!')

# Header inspection
header_m = re.search(r'(<header.*?</header>)', html, re.DOTALL | re.I)
if header_m:
    p('\nHeader found! First 1000 chars of header:')
    p(header_m.group(1)[:1000])

# Menu items
menu_items = re.findall(r'<li[^>]*class=[\'"][^\'"]*menu-item[^\'"]*[\'"][^>]*>\s*<a[^>]*>(.*?)</a>', html, re.DOTALL | re.I)
clean_menus = [re.sub(r'<[^>]+>', '', m).strip() for m in menu_items]
p('\nMenu items:', [m for m in clean_menus if m][:30])

# Footer inspection
footer_m = re.search(r'(<footer.*?</footer>)', html, re.DOTALL | re.I)
if footer_m:
    p('\nFooter found! Length:', len(footer_m.group(1)))
    p(footer_m.group(1)[:1500])

# Check images
imgs = re.findall(r'<img[^>]+src=[\'"]([^\'"]+)[\'"]', html, re.I)
p('\nImages count:', len(imgs))
for img in imgs[:15]:
    p('  IMG:', img)

# Colors in style or inline style
colors = set(re.findall(r'#(?:[0-9a-fA-F]{3}){1,2}\b', html))
p('\nSample hex colors in page:', list(colors)[:30])

# Body class
body_m = re.search(r'<body[^>]*class=[\'"]([^\'"]+)[\'"]', html, re.I)
if body_m:
    p('\nBody class:', body_m.group(1))

with open('analysis_results.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))
p('Done writing to analysis_results.txt')
