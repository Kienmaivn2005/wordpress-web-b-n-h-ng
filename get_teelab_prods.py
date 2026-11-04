import re

with open('scratch_teelab.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# Look for href to products
prod_links = set(re.findall(r'href=[\'"](?:https://teelab\.vn)?/([a-zA-Z0-9_\-]+)[\'"]', html))
valid_prods = [p for p in prod_links if any(w in p for w in ['ao-', 'quan-', 'tee', 'hoodie', 'polo', 'jacket', 'short', 'hat', 'bag'])]

print('Found product slugs:', len(valid_prods))
for p in sorted(valid_prods)[:10]:
    print(' ', p)

if valid_prods:
    with open('sample_teelab_prod_slug.txt', 'w') as f:
        f.write(valid_prods[0])

