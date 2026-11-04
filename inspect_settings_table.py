import sys, re
sys.stdout.reconfigure(encoding='utf-8')

with open(r'e:\web quản lý bán hàng wordpress\ht-tracker-v3\functions.php', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('kien_admin_save_settings')
if idx != -1:
    print(text[idx-100:idx+1500])
