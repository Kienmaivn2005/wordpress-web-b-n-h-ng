import re

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# Let's inspect <head>
head_m = re.search(r'<head>(.*?)</head>', html, re.DOTALL | re.I)
if head_m:
    with open('annhien_head.txt', 'w', encoding='utf-8') as f:
        f.write(head_m.group(1))

# Let's inspect the entire body minus scripts at end
body_m = re.search(r'<body[^>]*>(.*?)</body>', html, re.DOTALL | re.I)
if body_m:
    print('Body length:', len(body_m.group(1)))

print('Done extracting head.')

