import re

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# Let's inspect after </footer>
after_footer = html.split('</footer>')
if len(after_footer) > 1:
    with open('annhien_after_footer.txt', 'w', encoding='utf-8') as f:
        f.write(after_footer[1])
    print('After footer length:', len(after_footer[1]))

