import re

with open('scratch_teelab.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

out = []
out.append(f'HTML Length: {len(html)}')

# Title
title = re.search(r'<title>(.*?)</title>', html, re.I)
out.append(f'Title: {title.group(1) if title else "None"}')

# Platform detection
for kw in ['haravan', 'bizweb', 'sapo', 'shopify', 'wordpress', 'woocommerce']:
    if kw in html.lower():
        out.append(f'Platform keyword detected: {kw}')

# Stylesheets
css_files = set(re.findall(r'href=[\'"]([^\'"]+\.css[^\'"]*)[\'"]', html))
out.append(f'\nCSS files count: {len(css_files)}')
for c in sorted(list(css_files)):
    out.append(f'  CSS: {c}')

# Scripts
scripts = set(re.findall(r'src=[\'"]([^\'"]+\.js[^\'"]*)[\'"]', html))
out.append(f'\nJS scripts count: {len(scripts)}')
for s in sorted(list(scripts))[:20]:
    out.append(f'  JS: {s}')

# Header inspection
header = re.search(r'(<header.*?</header>)', html, re.DOTALL | re.I)
if header:
    out.append(f'\nHeader length: {len(header.group(1))}')

# Navigation menu links
menu_links = re.findall(r'<a[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>(.*?)</a>', html, re.DOTALL | re.I)
out.append(f'\nTotal links: {len(menu_links)}')

# Sections in body
sections = re.findall(r'<(?:section|div)[^>]+class=[\'"]([^\'"]*(?:section|banner|slider|product|collection|header|footer)[^\'"]*)[\'"]', html, re.I)
out.append(f'\nSample section classes: {set(sections[:25])}')

with open('teelab_analysis.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))

print('Done analyzing teelab.vn!')

