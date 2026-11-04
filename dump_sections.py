import re

with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

# Find main content
# In HTML, let's look for everything between </header> and <footer
body_between = html.split('</header>')[1].split('<footer')[0]

# Let's find each section or major div
sections = re.findall(r'(<section[^>]*>.*?</section>)', body_between, re.DOTALL | re.I)

out = [f"Found {len(sections)} sections in body:"]
for i, s in enumerate(sections):
    sec_class = re.search(r'<section[^>]+class=[\'"]([^\'"]*)[\'"]', s, re.I)
    cls = sec_class.group(1) if sec_class else 'no-class'
    out.append(f"\n--- SECTION {i+1}: {cls} (len: {len(s)}) ---")
    out.append(s[:600] + "\n...[truncated]...\n" + s[-300:])

with open('all_sections.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))

print(f"Done dumping {len(sections)} sections to all_sections.txt")

