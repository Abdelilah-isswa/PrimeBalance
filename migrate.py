import os
import re

VUE_DIR = '/home/abde/Music/file rouge/filrouge/resources/vue/views'

def process_file(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    if '<NavBar' not in content and 'NavBar.vue' not in content:
        return

    # Replace import
    content = re.sub(r"import\s+NavBar\s+from\s+(['\"])(.*?)/components/NavBar\.vue\1;?", r"import DashboardLayout from \1\2/components/DashboardLayout.vue\1;", content)

    # Helper function for DashboardLayout substitution
    def template_sub(m):
        template_body = m.group(1)
        
        # Check if the file actually uses <NavBar />
        if '<NavBar' not in template_body:
            return m.group(0) # Unchanged if imported but not used

        # Pattern: <div>\s*<NavBar />
        # Replace the opening div + nav bar
        new_body, count = re.subn(r'<div>\s*<NavBar\s*/?>', r'<DashboardLayout :company-id="$route.params.companyId">', template_body, count=1)
        
        if count == 1:
            # Replaced opening tag successfully.
            # Now replace the last </div> before </template> with </DashboardLayout>
            idx = new_body.rfind('</div>')
            if idx != -1:
                new_body = new_body[:idx] + '</DashboardLayout>' + new_body[idx+6:]
            return f'<template>{new_body}</template>'
        else:
            # Maybe it is not wrapped in a div? Attempt simple replace.
            new_body = re.sub(r'<NavBar\s*/?>', '', template_body)
            # Find the root tag if possible and wrap it, or just wrap the whole thing
            # We'll just append it to the start and end of template.
            # But the user only has `<div><NavBar />...</div>` in most files.
            # Let's check if it matched count == 0.
            if count == 0:
                print(f"Warning: Could not do simple div replacement in {filepath}")
                # We'll just replace `<NavBar />` with nothing if it was wrapped differently,
                # then wrap the whole content without modifying outer tags.
                pass
            return f'<template>{new_body}</template>'

    content = re.sub(r'<template>(.*?)</template>', template_sub, content, flags=re.DOTALL)

    with open(filepath, 'w') as f:
        f.write(content)
        print(f"Updated {filepath}")

for root, _, files in os.walk(VUE_DIR):
    for f in files:
        if f.endswith('.vue'):
            process_file(os.path.join(root, f))
