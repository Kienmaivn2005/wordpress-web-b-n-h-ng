with open('scratch_annhien.html', 'r', encoding='utf-8', errors='ignore') as f:
    html = f.read()

idx = html.find('Dr Pepti')
with open('sample_product_snippet.html', 'w', encoding='utf-8') as f:
    f.write(html[idx-300:idx+600])

print('Saved snippet to sample_product_snippet.html')

