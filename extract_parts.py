import re

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

scripts = re.findall(r'<script[^>]+src=[\'"]([^\'"]+)[\'"]', html, re.I)
out = ["=== SCRIPTS ==="]
for s in scripts:
    out.append(f"  JS: {s}")

# Let's inspect the exact HTML of the header
header_m = re.search(r'(<header.*?</header>)', html, re.DOTALL | re.I)
if header_m:
    out.append("\n=== FULL HEADER ===")
    out.append(header_m.group(1))

# Let's inspect footer
footer_m = re.search(r'(<footer.*?</footer>)', html, re.DOTALL | re.I)
if footer_m:
    out.append("\n=== FULL FOOTER ===")
    out.append(footer_m.group(1))

with open('site_header_footer.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))

