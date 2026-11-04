import sys, re
sys.stdout.reconfigure(encoding='utf-8')

with open(r'e:\web quản lý bán hàng wordpress\ht-tracker-v3\assets\js\teelab_main.js', 'r', encoding='utf-8', errors='ignore') as f:
    js = f.read()

print("title-menu matches:", re.findall(r'title-menu[^\n;{}]*', js))
print("Collapsible__Plus matches:", re.findall(r'Collapsible__Plus[^\n;{}]*', js))
print("hidden-mobile matches:", re.findall(r'hidden-mobile[^\n;{}]*', js))
print("col-footer matches:", re.findall(r'col-footer[^\n;{}]*', js))

