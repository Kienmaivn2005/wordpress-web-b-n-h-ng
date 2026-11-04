import re

with open('scratch_teelab.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# Let's inspect sections in body
body_between = html.split('</header>')[1].split('<footer')[0]

sections = re.findall(r'(<section[^>]*>.*?</section>)', body_between, re.DOTALL | re.I)
out = [f"Found {len(sections)} sections in Teelab body:"]
for i, s in enumerate(sections):
    sec_cls = re.search(r'<section[^>]+class=[\'"]([^\'"]*)[\'"]', s, re.I)
    cls = sec_cls.group(1) if sec_cls else 'no-class'
    out.append(f"\n--- SECTION {i+1}: {cls} (len: {len(s)}) ---")
    out.append(s[:500] + "\n...[truncated]...\n" + s[-300:])

with open('teelab_sections.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))

print('Done dumping teelab_sections.txt')

