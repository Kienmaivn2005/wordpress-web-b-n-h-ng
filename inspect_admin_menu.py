import sys, re
sys.stdout.reconfigure(encoding='utf-8')

with open(r'e:\web quản lý bán hàng wordpress\ht-tracker-v3\functions.php', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('function kien_admin_menu()')
if idx == -1:
    idx = text.find('add_menu_page')
print(text[idx-100:idx+2500])

