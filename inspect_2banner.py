import sys, re
sys.stdout.reconfigure(encoding='utf-8')

with open(r'e:\web quản lý bán hàng wordpress\ht-tracker-v3\index.php', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('class="section_2banner')
if idx != -1:
    print(text[idx:idx+1500])

